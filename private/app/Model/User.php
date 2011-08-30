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
                (login, passwd, role, email, surname, name, patronymic, status, date_reg)
                VALUES
                (:login, :passwd, :role, :email, :surname, :name, :patronymic, :status, NOW())
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

	    /**
		* Возвращает список преподавателей, ответсвенных за дисцилины, изучаемых студентом и его куратора
		*
		* @return array
	    */
        public function getResponsibleTeacherInfoForStudent() {

            $udata = $this->getAuth(); 

            $sql = '
                SELECT u.user_id, \'#curator\' AS title, u.surname, u.name, u.patronymic
                FROM ' . $this->_tables['users'] . ' u
                WHERE u.user_id = (SELECT curator FROM ' . $this->_tables['users'] . ' WHERE user_id = :uid)
                UNION DISTINCT
                SELECT u.user_id, d.title, u.surname, u.name, u.patronymic 
                FROM ' . $this->_tables['applications'] . ' a 
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d 
                    ON d.discipline_id = a.object_id AND a.type = \'discipline\' 
                LEFT JOIN ' . $this->_tables['users'] . ' u 
                    ON d.responsible_teacher = u.user_id 
                WHERE a.status IN (\'signed\', \'prepaid\') AND a.user_id = :uid AND a.type = \'discipline\'
                UNION DISTINCT
                SELECT u.user_id, d.title, u.surname, u.name, u.patronymic 
                FROM ' . $this->_tables['applications'] . ' a 
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON p.program_id = a.object_id AND a.type = \'program\'
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON d.program_id = p.program_id
                LEFT JOIN ' . $this->_tables['users'] . ' u 
                    ON d.responsible_teacher = u.user_id 
                WHERE a.status IN (\'signed\', \'prepaid\') AND a.user_id = :uid AND a.type = \'program\'
                ';

            $values = array (':uid' => $udata['user_id']);

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $a = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            //print_r($a); die();
            
            // результирующий массив
            $res = array();
            if (is_array($a)) {
                // выбираем ключи для будущего массива
                $keys = array_unique(array_map(function ($x) { return ($x['user_id']); }, $a));
                // для каждого ключа
                foreach ($keys as $y) {
                    // отфильтровавыем те значения, которые соответствуют ключу
                    $c = array_filter($a, function ($x) use ($y) { return $x['user_id'] == $y; } );
                    // выбираем те поля, которые относятся к ключу как 1-к-1
                    $cur = current($c);
                    $res[$y]['recipient_name'] =  $cur['surname'] . ' ' . $cur['name'] . ' ' . $cur['patronymic'];
                    // группируем в одно поле те поля, которые относятся к ключу как 1-к-много
                    $res[$y]['recipient_description'] = array_reduce(
                        $c, 
                        function ($z, $x) { $z[] = ($x['title'] == '#curator') ? 'Куратор' :  'Преподаватель по дисциплине \"' . $x['title'] . '\"'; return $z; }, 
                        array()
                    );
                }
            } 

            //print_r($res); die();

            return $res;
        }

	    /**
		* Возвращает информацию а студентах обучающихся у преподавателя и слушателей, у которых он куратор
		*
		* @return array
	    */
        public function getStudentsInfoForResponsibleTeacher () {

            $udata = $this->getAuth(); 

            $sql = '
                SELECT u.user_id, \'#curator\' AS title, u.surname, u.name, u.patronymic
                FROM ' . $this->_tables['users'] . ' u
                WHERE curator = :uid
                UNION DISTINCT
                SELECT u.user_id, d.title, u.surname, u.name, u.patronymic 
                FROM ' . $this->_tables['applications'] . ' a 
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d 
                    ON d.discipline_id = a.object_id AND a.type = \'discipline\' 
                LEFT JOIN ' . $this->_tables['users'] . ' u 
                    ON a.user_id = u.user_id
                WHERE a.status IN (\'signed\', \'prepaid\') AND d.responsible_teacher = :uid AND a.type = \'discipline\'
                UNION DISTINCT
                SELECT u.user_id, d.title, u.surname, u.name, u.patronymic 
                FROM ' . $this->_tables['applications'] . ' a 
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON p.program_id = a.object_id AND a.type = \'program\'
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON d.program_id = p.program_id
                LEFT JOIN ' . $this->_tables['users'] . ' u 
                    ON a.user_id = u.user_id
                WHERE a.status IN (\'signed\', \'prepaid\') AND d.responsible_teacher = :uid AND a.type = \'program\'
                ';

            $values = array (':uid' => $udata['user_id']);

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $a = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            //print_r($a); die();
            
            // результирующий массив
            $res = array();
            if ($a) {
                // выбираем ключи для будущего массива
                $keys = array_unique(array_map(function ($x) { return ($x['user_id']); }, $a));
                // для каждого ключа
                foreach ($keys as $y) {
                    // отфильтровавыем те значения, которые соответствуют ключу
                    $c = array_filter($a, function ($x) use ($y) { return $x['user_id'] == $y; } );
                    // выбираем те поля, которые относятся к ключу как 1-к-1
                    $cur = current($c);
                    $res[$y]['recipient_name'] =  $cur['surname'] . ' ' . $cur['name'] . ' ' . $cur['patronymic'];
                    // группируем в одно поле те поля, которые относятся к ключу как 1-к-много
                    $res[$y]['recipient_description'] = array_reduce(
                        $c, 
                        function ($z,$x) { $z[] = ($x['title'] == '#curator') ? 'Курируемый слушатель' :  'Изучает дисциплину \"' . $x['title'] . '\"'; return $z; },
                        array()
                    );
                }
            }

            //print_r($res);

            return $res;
        }

	    /**
		* Возвращает список всех преподователей вместе с информацией об дисциплинах, за которую они ответственны
		*
		* @return array
	    */
        public function getAllTeachersResponsibleInfo() {

            $sql = 'SELECT u.user_id, d.title, u.surname, u.name, u.patronymic 
                    FROM ' . $this->_tables['disciplines'] . ' d 
                    LEFT JOIN ' . $this->_tables['users'] . ' u 
                        ON d.responsible_teacher = u.user_id';

            $stmt = $this->prepare($sql);
            $stmt->execute();

            $a = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            //print_r($a); die();
            
            // результирующий массив
            $res = array();
            if ($a) {
                // выбираем ключи для будущего массива
                $keys = array_unique(array_map(function ($x) { return ($x['user_id']); }, $a));
                // для каждого ключа
                foreach ($keys as $y) {
                    // отфильтровавыем те значения, которые соответствуют ключу
                    $c = array_filter($a, function ($x) use ($y) { return $x['user_id'] == $y; } );
                    // выбираем те поля, которые относятся к ключу как 1-к-1
                    $cur = current($c);
                    $res[$y]['recipient_name'] =  $cur['surname'] . ' ' . $cur['name'] . ' ' . $cur['patronymic'];
                    // группируем в одно поле те поля, которые относятся к ключу как 1-к-много
                    $res[$y]['recipient_description'] = array_reduce(
                        $c, 
                        function ($z,$x) { $z[] = 'Отвественный за дисциплину \"' . $x['title'] . '\"'; return $z; },
                        array()
                    );
                }
            }

            //print_r($res);

            return $res;
        }

	    /**
		* Возвращает список всех пользователей
		*
        * @param string $userRole Фильтр по роли пользователя
        * @param string $sortField Поле сортировки
        * @param string $sortDirection Направление сортировки
		* @return bool|array
	    */
        public function getUsersList($usersRole, $sortField, $sortDirection) {
            $sql = 'SELECT `user_id`,`login`,`role`,`name`,`surname`,`patronymic`, `date_reg`
				FROM `users`' . ($usersRole == 'all' ? '' : ' WHERE `role`=:role');  
				//' ORDER BY :sort_field :sort_direction';
			// знаю, костыль, но что делать, если строка выше, написанная логически верно, работать не изволит
			switch ($sortField) {
				case 'id': $sql .= " ORDER BY user_id"; break; 
				case 'login': $sql .= " ORDER BY login"; break;
				case 'role': $sql .= " ORDER BY role"; break;
				case 'date_reg': $sql .= " ORDER BY date_reg"; break;
			}
			if ($sortField == 'fio') {
				if ($sortDirection == 'asc') $sql .= " ORDER BY surname ASC, name ASC, patronymic ASC";
				else if ($sortDirection == 'desc') $sql .= " ORDER BY surname DESC, name DESC, patronymic DESC";
			} else {
				if ($sortDirection == 'asc') $sql .= " ASC";
				else if ($sortDirection == 'desc') $sql .= " DESC";
			}

            $params = array();

            if ($usersRole != 'all') {
				$params[':role'] = $usersRole;
            }

			$stmt = $this->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

	    /**
		* Возвращает информацию о заданном пользователе
		*
		* @param int $userId
		* @return bool|array
	    */
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

	    /**
		* Изменяет информацию о заданном пользователе
		*
		* @param array $userInfo
		* @return void
	    */
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
