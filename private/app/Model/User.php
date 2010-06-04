<?php

    /* $Id$ */

    /**
    * Модель для работы с пользователями.
    */
    class Model_User extends Model_Base {
        /**
        * Роль пользователя: администратор.
        *
        * @var const
        */
        const ROLE_ADMIN = 'admin';

        /**
        * Роль пользователя: преподаватель.
        *
        * @var const
        */
        const ROLE_TEACHER = 'teacher';

        /**
        * Роль пользователя: слушатель.
        *
        * @var const
        */
        const ROLE_STUDENT = 'student';

        /**
        * Статус пользователя: активен.
        *
        * @var const
        */
        const STATUS_ACTIVE = 'active';

        /**
        * Статус пользователя: неактивен.
        *
        * @var const
        */
        const STATUS_INACTIVE = 'inactive';

        /**
        * Ошибка: пользователь не найден.
        *
        * @see self::login()
        * @var const
        */
        const ERR_USER_NOT_FOUND = 'user-not-found';

        /**
        * Ошибка: пользователь неактивен.
        *
        * @see self::login()
        * @var const
        */
        const ERR_USER_INACTIVE = 'user-inactive';

        const DOC_TYPE_EMPTY = '';

        const DOC_TYPE_DIPLOMA_HIGH = 'diploma-high';

        const DOC_TYPE_DIPLOMA_MEDIUM = 'diploma-medium';

        const DOC_TYPE_CUSTOM = 'custom';

        /**
        * Кэш данных авторизации.
        *
        * @var array
        */
        protected static $_auth_cache = null;

        /**
        * Создание экземпляра модели.
        *
        * @return Model_User
        */
        public static function create() {
            return new self();
        }

        /**
        * Проверяет, существует ли в БД пользователь с указанным логином.
        *
        * @param  string $login Логин пользователя.
        * @return boolean
        */
        public function exists($login) {
            $sql = '
                SELECT COUNT(*)
                FROM  ' . $this->_tables['users'] . '
                WHERE login = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($login));

            $count = current($stmt->fetch());

            return $count > 0;
        }

        /**
        * Добавляет в БД новую запись о пользователе.
        *
        * @param  string $login      Логин.
        * @param  string $passwd     Пароль.
        * @param  string $role       Роль пользователя.
        * @param  string $email      Email.
        * @param  string $surname    Фамилия.
        * @param  string $name       Имя.
        * @param  string $patronymic Отчество.
        * @return int Идентификатор нового пользователя.
        */
        public function register
        (
            $login, $passwd, $role, $email, $surname = null, $name = null,
            $patronymic = null
        )
        {
            $sql = '
                INSERT INTO ' . $this->_tables['users'] . '
                (login, passwd, role, email, surname, name, patronymic, status)
                VALUES
                (:login, :passwd, :role, :email, :surname, :name, :patronymic,
                :status)
            ';

            if (null !== $passwd) {
                /* Вычисляем хэш пароля */
                $auth = Resources::getInstance()->auth;
                $passwd = $auth->getPasswdHash($passwd);
            }

            $values = array(
                ':login'      => $login,
                ':passwd'     => $passwd,
                ':role'       => $role,
                ':email'      => $email,
                ':surname'    => $surname,
                ':name'       => $name,
                ':patronymic' => $patronymic,
                ':status'     => self::STATUS_INACTIVE
            );

            $this->prepare($sql)
                 ->execute($values);

            return $this->lastInsertId();
        }

        /**
        * Получение статуса пользователя.
        *
        * @param  int $id Идентификатор пользователя.
        * @return mixed
        */
        public function getStatus($id) {
            $sql = '
                SELECT status
                FROM ' . $this->_tables['users']  . '
                WHERE user_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            $status = $stmt->fetchColumn();

            return $status;
        }

        /**
        * Генерация кода активации пользователя.
        *
        * @param  int $id Идентификатор пользователя.
        * @return string
        */
        public function getActivationCode($id) {
            $auth = Resources::getInstance()->auth;
            $code = $auth->getActivationCode($id);

            return $code;
        }

        /**
        * Генерация кода для восстановления пароля.
        *
        * @param  int $id Идентификатор пользователя.
        * @return string
        */
        public function getPasswdRestoreCode($id) {
            $auth = Resources::getInstance()->auth;
            $code = $auth->getPasswdRestoreCode($id);

            return $code;
        }
        
        /**
        * Шифрование пароля.
        *
        * @param  string $passwd
        * @return string
        */
        public function getPasswdHash($passwd) {
            $auth = Resources::getInstance()->auth;
            return $auth->getPasswdHash($passwd);
        }

        /**
        * Генерация пароля из класса символов [a-zA-Z0-9].
        *
        * @param  int $length Длина пароля.
        * @return string
        */
        public function generatePassword($length = 10) {
            $lowercase_letters = 'absdefghijklmnopqrstuwyxyz';
            $uppercase_letters = strtoupper($lowercase_letters);
            $digits = '1234567890';

            $chars = $lowercase_letters . $uppercase_letters  . $digits;
            $chars = str_split($chars);

            $passwd = '';

            for ($i = 0; $i < $length; $i++) {
                $passwd .= $chars[ array_rand($chars) ];
            }

            return $passwd;
        }

        /**
        * Активация аккаунта слушателя.
        *
        * @param  int $id Идентификатор пользователя.
        * @return boolean
        */
        public function activateStudent($id) {
            $sql = '
                UPDATE ' . $this->_tables['users'] . '
                SET status = :status
                WHERE user_id = :id AND
                      role = :role
            ';

            $values = array(
                ':id'     => $id,
                ':status' => self::STATUS_ACTIVE,
                ':role'   => self::ROLE_STUDENT
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $row_count = $stmt->rowCount();
            return $row_count > 0;
        }

        /**
        * Активация аккаунта сотрудника (администратора или преподавателя) и
        * установка пароля.
        *
        * @param  int    $id     Идентификатор пользователя.
        * @param  string $passwd Пароль.
        * @return boolean
        */
        public function activateEmployee($id, $passwd) {
            $sql = '
                UPDATE ' . $this->_tables['users'] . '
                SET passwd = :passwd, status = :status
                WHERE user_id = :id AND
                      (role = :role_t OR role = :role_a)
            ';

            /* Шифруем пароль */
            $auth = Resources::getInstance()->auth;
            $passwd = $auth->getPasswdHash($passwd);

            $values = array(
                ':id'     => $id,
                ':passwd' => $passwd,
                ':status' => self::STATUS_ACTIVE,
                ':role_t' => self::ROLE_TEACHER,
                ':role_a' => self::ROLE_ADMIN
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $row_count = $stmt->rowCount();
            return $row_count > 0;
        }

        /**
        * Проверка пользовательских данных и авторизация.
        *
        * @param  string $login  Логин.
        * @param  string $passwd Пароль.
        * @param  mixed  $result Результат: либо код ошибки, либо данные пользователя.
        * @return boolean
        */
        public function login($login, $passwd, & $result) {

             $sql = '
                SELECT *
                FROM  ' . $this->_tables['users'] . '
                WHERE login = :login AND
                      passwd = :passwd
            ';

            /* Получаем хеш пароля */
            $auth = Resources::getInstance()->auth;
            $passwd = $auth->getPasswdHash($passwd);

            $values = array(
                ':login'  => $login,
                ':passwd' => $passwd
            );

            /* Выполняем поиск в базе */
            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            /* Если пользователь не найден, возвращаем false и код ошибки */
            if (false === ($user = $stmt->fetch(Db_Pdo::FETCH_ASSOC))) {
                $result = self::ERR_USER_NOT_FOUND;
                return false;
            }

            /* Аналогично, если пользователь не активен */
            if (self::STATUS_INACTIVE == $user['status']) {
                $result = self::ERR_USER_INACTIVE;
                return false;
            }

            /* Если всё хорошо, сохраняем данные авторизации */
            $auth->init()
                 ->setUserId($user['user_id']);

            /* И возвращаем данные пользователя */
            $result = $user;
            return true;
        }

        /**
        * Получение данных авторизованного пользователя.
        *
        * @return boolean|array False, если авторизация не пройдена, иначе массив с данными пользователя.
        */
        public function getAuth() {
            /* Если в кэше авторизации уже есть данные, то возвращаем их */
            if (null !== self::$_auth_cache) {
                return self::$_auth_cache;
            }

            $auth = Resources::getInstance()->auth;
            $auth->init();

            /* Если пользователь не прошёл авторизацию, возвращаем false */
            if (false === ($user_id = $auth->getUserId())) {
                return false;
            }

            /* Находим данные активного пользователя */
            if (false === ($user = $this->_getActiveUserById($user_id))) {
                /* Если не находим, то снимаем авторизацию */
                $auth->unsetUserId();
                return false;
            }

            /* Сохраняем данные пользователя в кэш авторизации */
            self::$_auth_cache = $user;

            return $user;
        }

        /**
        * Сброс авторизации.
        *
        * return void
        */
        public function resetAuth() {
            $auth = Resources::getInstance()->auth;
            $auth->init()
                 ->unsetUserId();
        }

        /**
        * Проверяет, заполнен ли расширенный профиль слушателя.
        * 
        * @param  int $id Идентификатор пользователя.
        * @return boolean
        */
        public function isExtendedProfileSet($id) {
            /**
            * Проверяем, чтобы были заданы паспортные данные. Если они есть - 
            * значит и профиль заполнен (так как паспортные данные - 
            * обязательные поля профиля).
            */
            
            $sql = '
                SELECT COUNT(*)
                FROM ' . $this->_tables['passports'] . '
                WHERE user_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            $count = $stmt->fetchColumn();

            return $count > 0;
        }

        /**
        * Обновление фамилии-имени-отчества в записи пользователя. Если 
        * передаются такие же значение, какие сейчас хранятся в базе, то метод 
        * вернёт false.
        * 
        * @param  int      $uid Идентификатор пользователя.
        * @param  stdClass $snp Объект с новыми значениями.
        * @return boolean  Была ли обновлена запись в базе.
        */
        public function updateSNP($uid, $snp) {
            $sql = '
                UPDATE users
                SET surname = :surname, name = :name, patronymic = :patronymic
                WHERE user_id = :user_id
                LIMIT 1
            ';

            $stmt = $this->prepare($sql);

            $stmt->bindParam(':surname',    $snp->surname);
            $stmt->bindParam(':name',       $snp->name);
            $stmt->bindParam(':patronymic', $snp->patronymic);
            $stmt->bindParam(':user_id',    $uid);

            $stmt->execute();
            $affected = $stmt->rowCount();

            return $affected > 0;
        }

        /**
        * Сохранение паспортных данных.
        * 
        * @param  int                 $uid      Идентификатор пользователя.
        * @param  Model_User_Passport $passport Контейнер с данными.
        * @return boolean
        */
        public function savePassport($uid, Model_User_Passport $passport) {
            /* Удаляем старую запись (если есть) */
            $this->deletePassport($uid);
            
            /* И добавляем новую */
            $sql = '
                INSERT INTO passports
                (
                    user_id, series, number, birthday, given_by,
                    given_date, region_id, city_id, street, house,
                    flat
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ';

            $stmt = $this->prepare($sql);

            $stmt->bindParam(1,  $uid);
            $stmt->bindParam(2,  $passport->series);
            $stmt->bindParam(3,  $passport->number);
            $stmt->bindParam(4,  $passport->birthday);
            $stmt->bindParam(5,  $passport->givenBy);
            $stmt->bindParam(6,  $passport->givenDate);
            $stmt->bindParam(7,  $passport->regionId);
            $stmt->bindParam(8,  $passport->cityId);
            $stmt->bindParam(9,  $passport->street);
            $stmt->bindParam(10, $passport->house);
            $stmt->bindParam(11, $passport->flat);

            return $stmt->execute();
        }
        
        /**
        * Удаление записи о паспортных данных пользователя.
        * 
        * @param  int $uid Идентификатор пользователя.
        * @return boolean Была ли удалена запись.
        */
        public function deletePassport($uid) {
            $sql = '
                DELETE FROM ' . $this->_tables['passports'] . '
                WHERE user_id = ?
                LIMIT 1
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($uid));
            
            $affected = $stmt->rowCount();
            return $affected;
        }

        /**
        * Сохранение данных о документе об образовании.
        * 
        * @param  int               $uid Идентификатор пользователя.
        * @param  Model_User_EduDoc $doc Контейнер с данными.
        * @return boolean
        */
        public function saveEduDoc($uid, Model_User_EduDoc $doc) {
            /* Удаляем старую запись (если есть) */
            $this->deleteEduDoc($uid);
            
            /* И добавляем новую */
            $sql = '
                INSERT INTO edu_docs (
                    user_id, type, custom_type, number, exit_year, speciality, 
                    qualification
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ';

            $stmt = $this->prepare($sql);

            $stmt->bindParam(1, $uid);
            $stmt->bindParam(2, $doc->type);
            $stmt->bindParam(3, $doc->customType);
            $stmt->bindParam(4, $doc->number);
            $stmt->bindParam(5, $doc->exitYear);
            $stmt->bindParam(6, $doc->speciality);
            $stmt->bindParam(7, $doc->qualification);

            return $stmt->execute();
        }
        
        /**
        * Удаление записи о документе об образовании.
        * 
        * @param  int $uid Идентификатор пользователя.
        * @return boolean Была ли удалена запись.
        */
        public function deleteEduDoc($uid) {
            $sql = '
                DELETE FROM ' . $this->_tables['edu_docs'] . '
                WHERE user_id = ?
                LIMIT 1
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($uid));
            
            $affected = $stmt->rowCount();
            return $affected;
        }

        /**
        * Сохранение данных о телефонах пользователя.
        * 
        * @param  int               $uid    Идентификатор пользователя.
        * @param  Model_User_Phones $phones Контейнер с данными.
        * @return boolean
        */
        public function savePhones($uid, Model_User_Phones $phones) {
            /* Удаляем старую запись (если есть) */
            $this->deletePhones($uid);
            
            /* И добавляем новую */
            $sql = '
                INSERT INTO ' . $this->_tables['phones'] . '
                (user_id, stationary, mobile)
                VALUES (?, ?, ?)
            ';

            $stmt = $this->prepare($sql);

            $stmt->bindParam(1, $uid);
            $stmt->bindParam(2, $phones->stationary);
            $stmt->bindParam(3, $phones->mobile);

            return $stmt->execute();
        }
        
        /**
        * Удаление записи о телефонах пользователя.
        * 
        * @param  int $uid Идентификатор пользователя.
        * @return boolean Была ли удалена запись.
        */
        public function deletePhones($uid) {
            $sql = '
                DELETE FROM ' . $this->_tables['phones'] . '
                WHERE user_id = ?
                LIMIT 1
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($uid));
            
            $affected = $stmt->rowCount();
            return $affected;
        }

        /**
        * Получение данных активного пользователя. Если такой пользователь есть,
        * но он не активен, возвращает false.
        *
        * @param  int $id Идентификатор.
        * @return array|false
        */
        protected function _getActiveUserById($id) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['users'] . '
                WHERE user_id = ? AND
                      status = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($id, self::STATUS_ACTIVE));

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Получение данных расширенной анкеты слушателя.
        *
        * @param  int $user_id Идентификатор пользователя.
        * @return stdClass Данные расширенного профиля слушателя.
        */
        public function getExtendedProfile($user_id)
        {
            
            $aliasing = array(
                'passport' => array('p', array('series', 'number', 'birthday',
                                               'given_by', 'given_date', 
                                               'region_id', 'city_id', 
                                               'street', 'house', 'flat')),
                                               
                'edu_doc' => array('ed', array('type', 'custom_type', 'number',
                                                'exit_year', 'speciality', 
                                                'qualification')),
                                                
                'phone' => array('ph', array('stationary', 'mobile'))
            );
            
            $columns = array();
            
            foreach ($aliasing as $column_prefix => $tbl)
            {
                foreach ($tbl[1] as $column) {
                    $columns[] = sprintf('%s.%s AS %s_%s', 
                                         $tbl[0], $column,
                                         $column_prefix, $column);
                }
            }                    
            
            $sql = '
                SELECT ' . implode(', ' . CRLF, $columns) . '
                FROM ' . $this->_tables['users'] . ' u
                
                LEFT JOIN ' . $this->_tables['passports'] . ' p
                ON p.user_id = u.user_id 
                
                LEFT JOIN ' . $this->_tables['edu_docs'] . ' ed
                ON ed.user_id = u.user_id
                
                LEFT JOIN ' . $this->_tables['phones'] . ' ph
                ON ph.user_id = u.user_id 
                                         
                WHERE u.user_id = :uid AND
                      u.role = :role
            ';
            
            $values = array(
                ':uid'  => $user_id,
                ':role' => self::ROLE_STUDENT
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $profile = $stmt->fetch(Db_PdO::FETCH_ASSOC);
            
            $passport = Model_User_Passport::create()->fromRow($profile);
            $edu_doc  = Model_User_EduDoc::create()->fromRow($profile);
            $phones   = Model_User_Phones::create()->fromRow($profile);
            
            return (object) array('passport' => $passport,
                                  'edu_doc'  => $edu_doc,
                                  'phones'   => $phones);
        }

        /**
        * Возвращает полный список преподавателей.
        *
        * @return array
        */
        public function getTeachersList() {
            $sql = 'SELECT `user_id`,`name`,`surname`,`patronymic` FROM `users`
                WHERE `role`=\'teacher\'';
            $stmt = $this->prepare($sql);
            $stmt->execute(array());
            $teachers = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            $retval = array();

            if (count($teachers)) {
                foreach ($teachers as $i => $teacher) {
                    $retval[$teacher['user_id']] = array(
                        'name' => $teacher['name'],
                        'surname' => $teacher['surname'],
                        'patronymic' => $teacher['patronymic'],
                    );
                }
            }

            return $retval;
        }

        /**
        * Возвращает полный список студентов в совокупности с их кураторами.
        *
        * @return array
        */
        public function getStudentsList() {
            $sql = 'SELECT `user_id`,`name`,`surname`,`patronymic`,`curator`
                FROM `users` WHERE `role`=\'student\'';
            $stmt = $this->prepare($sql);
            $stmt->execute(array());
            $students = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            $retval = array();

            if (count($students)) {
                foreach ($students as $i => $student) {
                    $retval[$student['user_id']] = array(
                        'name' => $student['name'],
                        'surname' => $student['surname'],
                        'patronymic' => $student['patronymic'],
                        'curator' => $student['curator'],
                    );
                }
            }

            return $retval;
        }

        /**
        * Назначает куратора студенту.
        *
        * @param  $studentId int Идентификатор студента
        * @param  $teacherId int Идентификатор преподавателя
        * @return void
        */
        public function setUserCurator($studentId, $teacherId) {
            $sql = 'UPDATE `users` SET `curator`=:teacher_id WHERE `role`=\'student\' AND `user_id`=:student_id';
            $params = array(
                ':teacher_id' => $teacherId,
                ':student_id' => $studentId,
            );
            $this->prepare($sql)->execute($params);
        }

        public function getStudentResponsibleTeachers() {
            $udata = (object) $this->getAuth();

            $app = Model_Application::create();
            $program = Model_Education_Programs::create();
            $disc = Model_Discipline::create();
            $payment = Model_Payment::create();

            $availPrograms = array();
            $availDisciplines = array();

            $programApps = $app->getProcessedAppsForPrograms($udata->user_id);

            foreach ($programApps as $a) {
                $a = (object) $a;

                if (Model_Education_Programs::PAID_TYPE_FREE == $a->paid_type && Model_Application::STATUS_ACCEPTED == $a->status ||
                        Model_Education_Programs::PAID_TYPE_PAID == $a->paid_type && Model_Application::STATUS_SIGNED == $a->status) {
                    $discs = $disc->getAllowed($a->object_id, $a->paid_type, $a->app_id);
                    $programData = $program->getProgramInfo($a->object_id);
                    $programData['disciplines'] = $discs;

                    $availPrograms[] = $programData;
                }
            }

            $discApp = $app->getProcessedAppsForDisciplines($udata->user_id);

            foreach ($discApp as $a) {
                $a = (object) $a;

                if (Model_Education_Programs::PAID_TYPE_PAID == $a->paid_type) {
                    if (Model_Application::STATUS_SIGNED !== $a->status) {
                        continue;
                    }

                    $paymentTotal = $payment->getTotal($a->app_id);

                    if (null === $paymentTotal) {
                        continue;
                    }

                    $programData = $program->getProgramInfo($a->program_id);
                    $programCost = $programData['cost'];

                    $totalCost = round($a->coef / 100, 3) * $programCost;

                    if ($paymentTotal < $totalCost) {
                        continue;
                    }

                }
                elseif (Model_Education_Programs::PAID_TYPE_FREE == $a->paid_type) {
                    if (Model_Application::STATUS_ACCEPTED !== $a->status)
                    {
                        continue;
                    }
                }

                $disc = Model_Discipline::create()->get($a->object_id);
                $availDisciplines[] = $disc;
            }

            $retval = array();
            $sql = 'SELECT `name`,`surname`,`patronymic` FROM `users` WHERE `user_id`=:user_id';
            $stmt = $this->prepare($sql);

            foreach ($availPrograms as $i => $program) {
                if ($program['edu_type'] == 'course') {
                    $stmt->execute(array(':user_id' => $program['responsible_teacher']));
                    $teacher = $stmt->fetch(Db_Pdo::FETCH_ASSOC);

                    if (isset($retval[$program['responsible_teacher']])) {
                        $retval[$program['responsible_teacher']]['recipient_description'][] = 'отвественный за курсы \'' . $program['title'] .  '\',';
                    }
                    else {
                        $retval[$program['responsible_teacher']] = array(
                            'recipient_name' => $teacher['surname'] . ' ' . mb_substr($teacher['surname'], 0, 1, 'utf-8') . '. ' . mb_substr($teacher['patronymic'], 0, 1, 'utf-8') . '.',
                            'recipient_description' => array('отвественный за курсы \'' . $program['title'] . '\''),
                        );
                    }
                }
                else {
                    $availDisciplines = array_merge($availDisciplines, $program['disciplines']);
                }
            }

            foreach ($availDisciplines as $i => $disc) {
                $stmt->execute(array(':user_id' => $disc['responsible_teacher']));
                $teacher = $stmt->fetch(Db_Pdo::FETCH_ASSOC);

                if (isset($retval[$disc['responsible_teacher']])) {
                    $retval[$disc['responsible_teacher']]['recipient_description'][] = 'отвественный за дисциплину \'' . $disc['title'] . '\'';
                }
                else {
                    $retval[$disc['responsible_teacher']] = array(
                        'recipient_name' => $teacher['surname'] . ' ' . mb_substr($teacher['surname'], 0, 1, 'utf-8') . '. ' . mb_substr($teacher['patronymic'], 0, 1, 'utf-8') . '.',
                        'recipient_description' => array('отвественный за дисциплину \'' . $disc['title'] . '\''),
                    );
                }
            }

            return $retval;
        }

        public function getUsersList($usersRole) {
            $sql = 'SELECT `user_id`,`login`,`role`,`name`,`surname`,`patronymic`
                FROM `users`' . ($usersRole == 'all' ? '' : ' WHERE `role`=:role');
            $stmt = $this->prepare($sql);
            $params = array();
            
            if ($usersRole != 'all') {
                $params[':role'] = $usersRole;
            }
            
            $stmt->execute($params);

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

        public function getUserInfo($userId) {
            $sql = 'SELECT `role`,`name`,`surname`,`patronymic`
                FROM `users`
                WHERE `user_id`=:user_id';
            $stmt = $this->prepare($sql);
            $params = array(
                'user_id' => $userId,
            );
            $stmt->execute($params);

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        public function setUserInfo($userInfo) {
            $sql = 'UPDATE `users`
                SET `name`=:name,`surname`=:surname,`patronymic`=:patronymic,`role`=:role
                WHERE `user_id`=:user_id';
            $this->prepare($sql)->execute($userInfo);
        }
        
        /**
        * Получение данных пользователя по логину.
        * 
        * @param  string $login
        * @return array
        */
        public function getInfoByLogin($login) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['users'] . '
                WHERE login = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($login));
            
            $info = $stmt->fetch(Db_Pdo::FETCH_ASSOC);
            return $info;
        }
        
        /**
        * Установка нового пароля для пользователя.
        * 
        * @param int    $user_id Идентификатор пользователя.
        * @param string $passwd  Пароль.
        * @return boolean
        */
        public function setPasswd($user_id, $passwd) {
            $sql = '
                UPDATE ' . $this->_tables['users'] . '
                SET passwd = :passwd
                WHERE user_id = :uid
                LIMIT 1
            ';
            
            $stmt = $this->prepare($sql);
            
            /* Вычисляем хэш пароля */
            $auth = Resources::getInstance()->auth;
            $passwd = $auth->getPasswdHash($passwd);
            
            $values = array(
                ':uid'    => $user_id,
                ':passwd' => $passwd
            );
            
            return $stmt->execute($values);
        }
    }
?>