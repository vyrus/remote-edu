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
            $sql = 'DELETE FROM ' . $this->_tables['message'] . '
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

        public function getInboxCount() {
            if ($this->userRole != Model_User::ROLE_ADMIN) {
                $sql = 'SELECT COUNT(*)
                    FROM ' . $this->_tables['message'] . ' AS `m`,`users` AS `u`
                    WHERE `m`.`to`=:user_id AND `m`.`from`=`u`.`user_id`';
                $params = array(
                    ':user_id' => $this->userId,
                );
            }
            else {
                $sql = 'SELECT COUNT(*)
                    FROM ' . $this->_tables['message'] . ' AS `m`
                    INNER JOIN `' . $this->_tables['users'] . '` AS `u` ON `m`.`to`=`u`.`user_id` AND `u`.`role`=\'admin\'
                    INNER JOIN `' . $this->_tables['users'] . '` AS `u1` ON `m`.`from`=`u1`.`user_id`';
                $params = array();
            }
            //echo $sql; die();

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        }

        public function getInbox($page) {
            if ($this->userRole != Model_User::ROLE_ADMIN) {
                $sql = 'SELECT `m`.`message_id`,`m`.`subject`,`m`.`read`,`m`.`time`,`u`.`surname`,`u`.`name`,`u`.`patronymic`,`u`.`role`
                    FROM ' . $this->_tables['message'] . ' AS `m`,`users` AS `u`
                    WHERE `m`.`to`=:user_id AND `m`.`from`=`u`.`user_id`
                    ORDER BY `m`.`time` DESC';
                $params = array(
                    ':user_id' => $this->userId,
                );
            }
            else {
                $sql = 'SELECT `m`.`message_id`,`m`.`subject`,`m`.`read`,`m`.`time`,`u1`.`surname`,`u1`.`name`,`u1`.`patronymic`,`u1`.`role`
                    FROM ' . $this->_tables['message'] . ' AS `m`
                    INNER JOIN `' . $this->_tables['users'] . '` AS `u` ON `m`.`to`=`u`.`user_id` AND `u`.`role`=\'admin\'
                    INNER JOIN `' . $this->_tables['users'] . '` AS `u1` ON `m`.`from`=`u1`.`user_id`
                    ORDER BY `m`.`time` DESC';
                $params = array();
            }
            //echo ($sql); die();
            $sql .= ' LIMIT ' . self::INBOX_MESSAGES_ON_PAGE  . ' OFFSET ' . intval($page) * self::INBOX_MESSAGES_ON_PAGE;

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            $retval = $stmt->fetchAll(Db_PdO::FETCH_ASSOC);

            foreach ($retval as $i => $message) {
                unset($retval[$i]['name']);
                unset($retval[$i]['surname']);
                unset($retval[$i]['patronymic']);

                if ($message['role'] == Model_User::ROLE_ADMIN) {
                    $retval[$i]['author'] = 'Администратор';
                }
                else {
                    $retval[$i]['author'] = $message['surname'] . ' ' . mb_substr($message['name'], 0, 1, 'utf-8') . '. ' . mb_substr($message['patronymic'], 0, 1, 'utf-8') . '.';
                }
            }

            return $retval;
        }

        public function getMessage($messageId) {
            $sql = 'SELECT `m`.`message_id`,`m`.`subject`,`m`.`message`,`m`.`from`,`m`.`read`,`m`.`time`,`u`.`surname`,`u`.`name`,`u`.`patronymic`,`u`.`role`
                FROM ' . $this->_tables['message'] . ' AS `m`, `users` AS `u`
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
                $sql = 'UPDATE ' . $this->_tables['message'] . ' SET `read`=\'read\' WHERE `message_id`=:message_id';
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
            $sql = 'INSERT INTO ' . $this->_tables['message'] . '(`from`,`to`,`subject`,`message`,`read`,`time`)
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

        public function addAttachments($messageIDs, $files) {
            $storage = Resources::getInstance()->attachments_storage;
            
            $sql = 'INSERT INTO ' . $this->_tables['message_attachment'] . '(`message`,`original_filename`,`mime_type`,`filename`)
                VALUES (:message, :original_filename, :mime_type, :filename)';
            $stmt = $this->prepare($sql);
            
            foreach ($files['name'] as $i => $value) {
                $filename = $storage->storeFile($files['tmp_name'][$i]);
                foreach ($messageIDs as $message) {
                    $params = array(
                        ':message' => $message,
                        ':original_filename' => $files['name'][$i],
                        ':mime_type' => $files['type'][$i],
                        ':filename' => $filename
                    );
                    
                    $stmt->execute($params);
                }
            }
            
            /*foreach ($files['name'] as $i => $value) {
                $filename = $storage->storeFile($files['tmp_name'][$i]);
                $params = array(
                    ':message' => $message,
                    ':original_filename' => $files['name'][$i],
                    ':mime_type' => $files['type'][$i],
                    ':filename' => $filename,
                );
                
                $stmt->execute($params);
            }*/
        }
        
        public function getAttachments($message) {
            $sql = 'SELECT *
                FROM ' . $this->_tables['message_attachment'] . '
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
            
            // да, надо было перепроектировать все-таки...
            $sql = "SELECT COUNT(filename) AS file_count, filename FROM ' . $this->_tables['message_attachment'] . '
                    GROUP BY filename HAVING filename = ANY
                    (SELECT filename FROM ' . $this->_tables['message_attachment'] . ' WHERE message = :message_id)";
            $params = array(
                    ':message_id' => $messageId,
                );
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            $attachmentsCountInfo = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            $fileCountAr = array();
            foreach ($attachmentsCountInfo as $rec) {
                $fileCountAr[$rec['filename']] = $rec['file_count'];
            }
            unset($attachmentsCountInfo);
            
            foreach ($attachments as $i => $attachment) {
                if ($fileCountAr[$attachment['filename']] == 1)
                    $storage->removeFile($attachment['filename']);
                    
                $sql = 'DELETE FROM ' . $this->_tables['message_attachment'] . '
                    WHERE `id`=:attachment_id';
                $params = array(
                    ':attachment_id' => $attachment['id'],
                );
                
                $this->prepare($sql)->execute($params);
            }
        }

        public function getAttachment($attachmentId) {
            $sql = 'SELECT `message`,`original_filename`,`filename`,`mime_type`
                FROM ' . $this->_tables['message_attachment'] . '
                WHERE `id`=:attachment_id';
            $params = array(
                ':attachment_id' => $attachmentId,
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($params);            
            $attachment = $stmt->fetch(Db_Pdo::FETCH_ASSOC);
            
            if ($attachment === FALSE) {
                return FALSE;
            }
                        
            $sql = 'SELECT `to`
                FROM ' . $this->_tables['message'] . '
                WHERE `message_id`=:message_id';                            
            $params = array(
                ':message_id' => $attachment['message'],
            );
                        
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            $message = $stmt->fetchColumn();
            
            $udata = (object)Model_User::create()->getAuth();
            
            if ($udata->user_id != $message) {
                return FALSE;
            }
            
            $storage = Resources::getInstance()->attachments_storage;
            
            header('Content-Disposition: attachment; filename="' . $attachment['original_filename']) . '"';
            header('Content-Type: ' . $attachment['mime_type']);

            echo $storage->getFileContent($attachment['filename']);
            
            return TRUE;
        }

	    /**
		* Возвращает список потенциальных получателей сообщений для текущего пользователя
		*
		* @return |array
	    */
        public function getRecipientsList() {

            if ($this->userRole == Model_User::ROLE_STUDENT || $this->userRole == Model_User::ROLE_TEACHER) {
                $admin = array();
                $sql = 'SELECT `user_id` FROM `' . $this->_tables['users'] . '` WHERE `role`=\'admin\' LIMIT 1';
                $stmt = $this->prepare($sql);
                $stmt->execute();
                $adminId = $stmt->fetchColumn();
                $admin[$adminId] = array(
                    'recipient_name' => 'Администратор',
                    'recipient_description' => array('Администратор'),
                );
            }

            $user = Model_User::create();

            switch ($this->userRole) {
                case Model_User::ROLE_STUDENT: {
                    $teachers = $user->getResponsibleTeacherInfoForStudent();
                    $retval = $admin + $teachers;
                    break;
                }

                case Model_User::ROLE_TEACHER: {
                    $students = $user->getStudentsInfoForResponsibleTeacher();
                    $retval = $admin + $students;
                    break;
                }

                case Model_User::ROLE_ADMIN: {
                    $teachers = $user->getAllTeachersResponsibleInfo();
                    array_walk(
                        $teachers, 
                        function (&$value, $key) { 
                            $value['role'] = Model_User::ROLE_TEACHER;
                        }
                    );
                    $students = $user->getStudentsList();
                    array_walk(
                        $students, 
                        function (&$value, $key) { 
                            $value['role'] = Model_User::ROLE_STUDENT;
                            $value['recipient_name'] =  $value['surname'] . ' ' . $value['name'] . ' ' . $value['patronymic'];
                            //$value['recipient_name'] =  $recipient['surname'] . ' ' . mb_substr($recipient['name'], 0, 1, 'utf-8') . '. ' . mb_substr($recipient['pat    ronymic'], 0, 1, 'utf-8') . '.';
                            unset ($value['name'], $value['surname'], $value['patronymic']);
                        }
                    );
                   $retval = $teachers + $students;
                   //print_r($retval); die();
                }
            }

            return $retval;
        }
    }
?>
