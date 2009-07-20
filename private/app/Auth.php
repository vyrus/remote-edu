<?php
    
    /* $Id$ */

    /**
    * @todo Поддержка авторизации через cookie? Если удалить пользователя из БД,
    *       он некоторое время всё равно сможет работать на сайте.
    */
    /**
    * Контейнер для хранения данных авторизации + шифрование паролей.
    */
    class Auth {
        /**
        * Случайные символы (соль, salt) для алгоритма хеширования. 
        * 
        * @var string
        */
        protected $_salt;
        
        /**
        * Метод-конструктор класса.
        * 
        * @param  string $salt Соль для хеширования.
        * @return void
        */
        public function __construct($salt) {
            $this->setSalt($salt);
        }
        
        /**
        * Создание экземпляра класса.
        * 
        * @param  string $salt Соль для хеширования.
        * @return Auth
        */
        public static function create($salt) {
            return new self($salt);
        }
        
        /**
        * Установка значения соли для хеширования.
        * 
        * @param  string $salt Соль.
        * @return void
        */
        public function setSalt($salt) {
            $this->_salt = $salt;
        }
        
        /**
        * Вычисление хеша пароля.
        * 
        * @param  string $passwd Пароль.
        * @return string
        */
        public function getPasswdHash($passwd) {
            return $this->_hash($passwd);
        } 
        
        /**
        * Инициализация хранилища данных.
        * 
        * @return Auth
        */
        public function init() {
            session_start();
            return $this;
        }
        
        /**
        * Установка данных пользователя.
        * 
        * @param  string $login Логин.
        * @param  string $fio   Ф.И.О.
        * @return void
        */
        public function setUser($login, $fio) {
            $_SESSION['user'] = array(
                'login' => $login,
                'fio'   => $fio
            );
        }     
        
        /**
        * Получение данных пользователя.
        * 
        * @return array
        */
        public function getUser() {
            return $_SESSION['user'];
        }
        
        /**
        * Хеширование с защитой от rainbow tables.
        * 
        * @link http://ru.wikipedia.org/wiki/Радужная_таблица
        * 
        * @param  string $string Хешируемая строка.
        * @return string
        */
        protected function _hash($string) {
            $str  = strrev($this->_salt);
            $str .= md5($string);
            $str .= $this->_salt;
            
            $str = md5($str);
            $str = md5($str);
            
            return $str;
        }
    }

?>