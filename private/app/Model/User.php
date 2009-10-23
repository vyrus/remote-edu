<?php
    
    /* $Id$ */

    /**
    * Модель для работы с пользователями.
    */
    class Model_User extends Mvc_Model_Abstract {
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
        * Название таблицы с пользователями в БД.
        * 
        * @var string
        */
        protected $_table = 'users';
        
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
                FROM  ' . $this->_table . '
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
                INSERT INTO ' . $this->_table . '
                (login, passwd, role, email, surname, name, patronymic, status)
                VALUES
                (:login, :passwd, :role, :email, :surname, :name, :patronymic, :status)
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
                FROM ' . $this->_table  . '
                WHERE user_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            $status = $stmt->fetchColumn();
            
            return $status;
        }
        
        /**
        * Генерация кода активации пользователя
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
                UPDATE ' . $this->_table . '
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
                UPDATE ' . $this->_table . '
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
                FROM  ' . $this->_table . '
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
        
        public function isExtendedProfileSet($id) {
            $sql = '
                SELECT COUNT(*)
                FROM passports
                WHERE user_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            $count = $stmt->fetchColumn();
            
            return $count > 0;
        }
        
        public function setExtendedProfile($uid, $profile) {
            if (!$this->_updateSNP($uid, $profile['general'])) {
                return false;
            }
                
            if (!$this->_savePassport($uid, $profile['passport'])) {
                return false;
            }
            
            if (!empty($profile['edu_doc']['type']) && 
                !$this->_saveEduDoc($uid, $profile['edu_doc']))
            {
                return false;
            }
                
            if (!empty($profile['phones']['mobile']) ||
                !empty($profile['phones']['stationary']))
            {
                if (!$this->_savePhones($uid, $profile['phones'])) {
                    return false;
                }
            }
            
            return true;
        }
        
        protected function _updateSNP($uid, $snp) {
            $sql = '
                UPDATE users
                SET surname = :surname, name = :name, patronymic = :patronymic
                WHERE user_id = :user_id
            ';
            
            $stmt = $this->prepare($sql);
            
            $snp = (object) $snp;
            $stmt->bindParam(':surname',    $snp->surname);
            $stmt->bindParam(':name',       $snp->name);
            $stmt->bindParam(':patronymic', $snp->patronymic);
            $stmt->bindParam(':user_id',    $uid);
            
            $stmt->execute();
            $affected = $stmt->rowCount();
            
            return $affected > 0;
        }
        
        protected function _savePassport($uid, $passport) {
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
            
            $passport = (object) $passport;
            $stmt->bindParam(1,  $uid);
            $stmt->bindParam(2,  $passport->series);
            $stmt->bindParam(3,  $passport->number);
            $stmt->bindParam(4,  $passport->birthday);
            $stmt->bindParam(5,  $passport->given_by);
            $stmt->bindParam(6,  $passport->given_date);
            $stmt->bindParam(7,  $passport->region_id);
            $stmt->bindParam(8,  $passport->city_id);
            $stmt->bindParam(9,  $passport->street);
            $stmt->bindParam(10, $passport->house);
            $stmt->bindParam(11, $passport->flat);
            
            return $stmt->execute();
        }
        
        protected function _saveEduDoc($uid, $doc) {
            $sql = '
                INSERT INTO edu_docs
                (user_id, type, custom_type, number, exit_year, speciality, qualification)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ';
            
            $stmt = $this->prepare($sql);
            
            $doc = (object) $doc;
            $stmt->bindParam(1, $uid);
            $stmt->bindParam(2, $doc->type);
            $stmt->bindParam(3, $doc->custom_type);
            $stmt->bindParam(4, $doc->number);
            $stmt->bindParam(5, $doc->exit_year);
            $stmt->bindParam(6, $doc->speciality);
            $stmt->bindParam(7, $doc->qualification);
            
            return $stmt->execute();
        }
        
        protected function _savePhones($uid, $phones) {
            $sql = '
                INSERT INTO phones
                (user_id, stationary, mobile)
                VALUES (?, ?, ?)
            ';
            
            $stmt = $this->prepare($sql);
            
            $phones = (object) $phones;
            $stmt->bindParam(1, $uid);
            $stmt->bindParam(2, $phones->stationary);
            $stmt->bindParam(3, $phones->mobile);
            
            return $stmt->execute();
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
                FROM ' . $this->_table . '
                WHERE user_id = ? AND
                      status = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id, self::STATUS_ACTIVE));
                           
            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }
    }

?>