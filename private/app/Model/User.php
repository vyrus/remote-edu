<?php
    
    /* $Id$ */

    class Model_User extends Mvc_Model_Abstract {
        protected $_table = 'users';
        
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
        * @param  string $role       Тип пользователя.
        * @param  string $email      e-mail.
        * @param  string $surname    Фамилия.
        * @param  string $name       Имя.
        * @param  string $patronymic Отчество.
        * @return boolean
        */
        public function register($login, $passwd, $role, $email, $surname, $name, $patronymic) {
            $sql = '
                INSERT INTO ' . $this->_table . '
                (login, passwd, role, email, surname, name, patronymic)
                VALUES
                (:login, :passwd, :role, :email, :surname, :name, :patronymic)
            ';
            
            /* Вычисляем хэш пароля */
            $auth = Resources::getInstance()->auth;
            $passwd = $auth->getPasswdHash($passwd);
            
            $values = array(
                ':login'      => $login,
                ':passwd'     => $passwd,
                ':role'       => $role,
                ':email'      => $email,
                ':surname'    => $surname,
                ':name'       => $name,
                ':patronymic' => $patronymic
            );
            
            return $this->prepare($sql)
                        ->execute($values);
        }
        
        /**
        * Проверка пользовательских данных и авторизация.
        * 
        * @param  string $login  Логин.
        * @param  string $passwd Пароль.
        * @return array|false Если пользователь найден, массив с его данными, иначе false.
        */
        public function login($login, $passwd) {
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
            
            /* Если пользователь не найден, возвращаем false */
            if (false === ($user = $stmt->fetch(Db_Pdo::FETCH_ASSOC))) {
                return false;
            }
                  
            /* Если найден, запоминаем его */
            $auth->init()->setUser($user['login'], $user['role'], $user['email'], $user['surname'], $user['name'], $user['patronymic']);
            
            /* И возвращаем данные пользователя */
            return $user;
        }
        
        public function showAll() {
            $sql = 'SELECT * FROM ' . $this->_table;
            $users = $this->fetchAll($sql, Db_Pdo::FETCH_ASSOC);
            
            return $users;
        }
        
        public function showAdmins() {
            $sql = '
                SELECT *
                FROM  ' . $this->_table . '
                WHERE role = \'admin\' OR
                      role = \'teacher\'
            ';
            $admins = $this->fetchAll($sql, Db_Pdo::FETCH_ASSOC);
            
            return $admins;
        }
    }

?>