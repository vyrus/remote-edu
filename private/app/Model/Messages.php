<?php
    class Model_Messages extends Model_Base {
        const INBOX_MESSAGES_ON_PAGE = 15;

        private $userId;
        private $userRole;

        public function __construct() {
            parent::__construct();
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $this->userId = $udata->user_id;
            $this->userRole = $udata->role;
        }

        public static function getUnreadCount($userId) {
            $sql = 'SELECT COUNT(`message_id`)
                FROM `message`
                WHERE `read`=\'unread\' AND `to`=:user_id';
            $params = array(
                'user_id' => $userId
            );

            $db = Resources::getInstance()->db;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $retval = $stmt->fetch(Db_Pdo::FETCH_NUM);

            return $retval[0];
        }

        public function removeMessage($messageId) {
            $sql = 'DELETE FROM `message`
                WHERE `message_id`=:message_id AND `to`=:user_id';
            $params = array(
                'message_id' => $messageId,
                'user_id' => $this->userId,
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            
            if ($stmt->rowCount()) {
                $this->removeAttachments($messageId);                
            }            
        }

        public function getInbox($page, &$messagesTotalNumber) {
            if ($this->userRole != Model_User::ROLE_ADMIN) {
                $sql = 'SELECT `m`.`message_id`,`m`.`subject`,`m`.`read`,`m`.`time`,`u`.`surname`,`u`.`name`,`u`.`patronymic`,`u`.`role`
                    FROM `message` AS `m`,`users` AS `u`
                    WHERE `m`.`to`=:user_id AND `m`.`from`=`u`.`user_id`
                    ORDER BY `m`.`time` DESC';
                $params = array(
                    'user_id' => $this->userId,
                );
            }
            else {
                $sql = 'SELECT `m`.`message_id`,`m`.`subject`,`m`.`read`,`m`.`time`,`u1`.`surname`,`u1`.`name`,`u1`.`patronymic`,`u1`.`role`
                    FROM `message` AS `m`
                    INNER JOIN `users` AS `u` ON `m`.`to`=`u`.`user_id` AND `u`.`role`=\'admin\'
                    INNER JOIN `users` AS `u1` ON `m`.`from`=`u1`.`user_id`
                    ORDER BY `m`.`time` DESC';
                $params = array();
            }

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            $retval = $stmt->fetchAll(Db_PdO::FETCH_ASSOC);

            $messagesTotalNumber = count($retval);
            /**
            * @todo Take use of SQL's LIMIT clause instead of array_slice().
            */
            $retval = array_slice($retval, $page * self::INBOX_MESSAGES_ON_PAGE, self::INBOX_MESSAGES_ON_PAGE);

            foreach ($retval as $i => $message) {
                unset($retval[$i]['name']);
                unset($retval[$i]['surname']);
                unset($retval[$i]['patronymic']);

                if ($message['role'] == Model_User::ROLE_ADMIN) {
                    $retval[$i]['author'] = 'Администратор';
                }
                else {
                    $retval[$i]['author'] = $message['surname'] . ' ' . mb_substr($message['surname'], 0, 1, 'utf-8') . '. ' . mb_substr($message['patronymic'], 0, 1, 'utf-8') . '.';
                }
            }

            return $retval;
        }

        public function getMessage($messageId) {
            $sql = 'SELECT `m`.`message_id`,`m`.`subject`,`m`.`message`,`m`.`from`,`m`.`read`,`m`.`time`,`u`.`surname`,`u`.`name`,`u`.`patronymic`,`u`.`role`
                FROM `message` AS `m`, `users` AS `u`
                WHERE `m`.`message_id`=:message_id AND `m`.`to`=:user_id AND `m`.`from`=`u`.`user_id`';
            $params = array(
                'message_id' => $messageId,
                'user_id' => $this->userId,
            );
            $stmt = $this->prepare($sql);
            $stmt->execute($params);

            if (($retval = $stmt->fetch(Db_Pdo::FETCH_ASSOC)) === FALSE) {
                return FALSE;
            }

            if ($retval['read'] == 'unread') {
                $sql = 'UPDATE `message` SET `read`=\'read\' WHERE `message_id`=:message_id';
                $params = array(
                    'message_id' => $messageId,
                );
                $this->prepare($sql)->execute($params);
            }

            $retval['author'] = $retval['role'] == Model_User::ROLE_ADMIN ? 'Администратор' : $retval['surname'] . ' ' . $retval['name'] . ' ' . $retval['patronymic'];
            unset($retval['name']);
            unset($retval['surname']);
            unset($retval['patronymic']);
            unset($retval['role']);

            return $retval;
        }

        public function sendMessage($to, $subject, $message) {
            $sql = 'INSERT INTO `message`(`from`,`to`,`subject`,`message`,`read`,`time`)
                VALUES (:user_id,:to_id,:subject,:message,\'unread\',:time)';
            $params = array(
                'user_id' => $this->userId,
                'to_id' => $to,
                'subject' => $subject,
                'message' => $message,
                'time' => time(),
            );

            $this->prepare($sql)->execute($params);
            
            return $this->lastInsertId();
        }

        public function addAttachments($message, $files) {
            $storage = Resources::getInstance()->attachments_storage;
            
            $sql = 'INSERT INTO `message_attachment`(`message`,`original_filename`,`mime_type`,`filename`)
                VALUES (:message, :original_filename, :mime_type, :filename)';
            $stmt = $this->prepare($sql);
            
            foreach ($files['name'] as $i => $value) {
                $filename = $storage->storeFile($files['tmp_name'][$i]);
                $params = array(
                    ':message' => $message,
                    ':original_filename' => $files['name'][$i],
                    ':mime_type' => $files['type'][$i],
                    ':filename' => $filename,
                );
                
                $stmt->execute($params);
            }
        }
        
        public function getAttachments($message) {
            $sql = 'SELECT *
                FROM `message_attachment`
                WHERE `message`=:message';                
            $params = array(
                ':message' => $message,
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }
        
        public function removeAttachments($messageId) {
            $attachments = $this->getAttachments($messageId);
            $storage = Resources::getInstance()->attachments_storage;
            
            foreach ($attachments as $i => $attachment) {
                $storage->removeFile($attachment['filename']);
                                
                $sql = 'DELETE FROM `message_attachment`
                    WHERE `id`=:arrachment_id';                
                $params = array(
                    ':arrachment_id' => $attachment['id'],
                );
                
                $this->prepare($sql)->execute($params);
            }
        }

        public function getAttachment($attachmentId) {
            $sql = 'SELECT `message`,`original_filename`,`filename`,`mime_type`
                FROM `message_attachment`
                WHERE `id`=:attachment_id';
            $params = array(
                ':attachment_id' => $attachmentId,
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($params);            
            $attachment = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            if (empty($attachment)) {
                return FALSE;
            }
                        
            $sql = 'SELECT `to`
                FROM `message`
                WHERE `message_id`=:message_id';                            
            $params = array(
                ':message_id' => $attachment[0]['message'],
            );
                        
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            $message = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            $udata = (object)Model_User::create()->getAuth();
            
            if ($udata->user_id != $message[0]['to']) {
                return FALSE;
            }
            
            $storage = Resources::getInstance()->attachments_storage;
            
            header('Content-Disposition: attachment; filename="' . $attachment[0]['original_filename']) . '"';
            header('Content-Type: ' . $attachment[0]['mime_type']);

            echo $storage->getFileContent($attachment[0]['filename']);
            
            return TRUE;
        }

        public function getRecipientsList() {
            $retval = array();

            if ($this->userRole == Model_User::ROLE_ADMIN) {
                $sql = 'SELECT `user_id`,`name`,`surname`,`patronymic`
                    FROM `users`
                    WHERE `role`<>\'admin\'';
                $stmt = $this->prepare($sql);
                $stmt->execute();
                $recipients = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);

                foreach ($recipients as $i => $recipient) {
                    $retval[$recipient['user_id']] = array(
                        'recipient_name' => $recipient['surname'] . ' ' . mb_substr($recipient['name'], 0, 1, 'utf-8') . '. ' . mb_substr($recipient['patronymic'], 0, 1, 'utf-8') . '.',
                        'recipient_description' => array(),
                    );
                }

                return $retval;
            }

            $sql = 'SELECT `user_id` FROM `users` WHERE `role`=\'admin\' LIMIT 1';
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $adminId = $stmt->fetch(Db_Pdo::FETCH_NUM);
            $retval[$adminId[0]] = array(
                'recipient_name' => 'Администратор',
                'recipient_description' => array('Администратор'),
            );

            switch ($this->userRole) {
                case Model_User::ROLE_STUDENT: {
                    $sql = 'SELECT `curator`
                        FROM `users`
                        WHERE `role`=\'student\' AND `user_id`=:user_id';
                    $params = array(
                        'user_id' => $this->userId,
                    );
                    $stmt = $this->prepare($sql);
                    $stmt->execute($params);
                    $curatorId = $stmt->fetch(Db_Pdo::FETCH_NUM);

                    $sql = 'SELECT `name`,`surname`,`patronymic`
                        FROM `users`
                        WHERE `role`=\'teacher\' AND `user_id`=:curator_id';
                    $params = array(
                        'curator_id' => $curatorId[0],
                    );
                    $stmt = $this->prepare($sql);
                    $stmt->execute($params);
                    $curator = $stmt->fetch(Db_Pdo::FETCH_ASSOC);

                    $retval[$curatorId[0]] = array(
                        'recipient_name' => 'Куратор: ' . $curator['surname'] . ' ' . mb_substr($curator['name'], 0, 1, 'utf-8') . '. ' . mb_substr($curator['patronymic'], 0, 1, 'utf-8') . '.',
                        'recipient_description' => array('Куратор'),
                    );

                    $user = Model_User::create();
                    $teachers = $user->getStudentResponsibleTeachers();

                    foreach ($teachers as $i => $teacher) {
                        if (isset($retval[$i])) {
                            array_merge($retval[$i]['recipient_description'], $teacher['recipient_description']);
                        }
                        else {
                            $retval[] = $teacher;
                        }
                    }

                    break;
                }

                case Model_User::ROLE_TEACHER: {
                    $sql = 'SELECT `user_id`,`name`,`surname`,`patronymic`
                        FROM `users`
                        WHERE `role`=\'student\' AND `curator`=:curator_id';
                    $params = array(
                        'curator_id' => $this->userId,
                    );
                    $stmt = $this->prepare($sql);
                    $stmt->execute($params);
                    $students = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);

                    foreach ($students as $i => $student) {
                        $retval[$student['user_id']] = array(
                            'recipient_name' => $student['surname'] . ' ' . mb_substr($student['name'], 0, 1, 'utf-8') . '. ' . mb_substr($student['patronymic'], 0, 1, 'utf-8') . '.',
                            'recipient_description' => array(),
                        );
                    }

                    break;
                }
            }

            return $retval;
        }
    }
?>