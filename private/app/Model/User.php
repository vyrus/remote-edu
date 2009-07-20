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
        * @param  string $login  Логин.
        * @param  string $passwd Пароль.
        * @param  string $fio    Ф.И.О.
        * @return boolean
        */
        public function register($login, $passwd, $fio) {
            $sql = '
                INSERT INTO ' . $this->_table . '
                (login, passwd, fio)
                VALUES
                (:login, :passwd, :fio)
            ';
            
            /* Вычисляем хэш пароля */
            $auth = Resources::getInstance()->auth;
            $passwd = $auth->getPasswdHash($passwd);
            
            $values = array(
                ':login'  => $login,
                ':passwd' => $passwd,
                ':fio'    => $fio
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
            $auth->init()->setUser($user['login'], $user['fio']);
            
            /* И возвращаем данные пользователя */
            return $user;
        }
        
        public function showAll() {
            $sql = 'SELECT * FROM ' . $this->_table;
            $users = $this->fetchAll($sql, Db_Pdo::FETCH_ASSOC);
            
            return $users;
        }    
    }

?>