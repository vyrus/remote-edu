<?php

    /* $Id$ */

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
        * Объект для работы с сессией.
        *
        * @var mixed
        */
        protected $_session;

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
        * Вычисление кода активации по идентификатору.
        *
        * @param  int $id Идентификатор пользователя.
        * @return string
        */
        public function getActivationCode($id) {
            return $this->_hash($id);
        }

        /**
        * Генерация кода для восстановления пароля. Код генерируется с
        * использованием текущей даты, поэтому он будет действовать только в
        * течении дня, когда был сгенерирован.
        *
        * @param  int $id Идентификатор пользователя.
        * @return string
        */
        public function getPasswdRestoreCode($id) {
            return $this->_hash($id . date('d.m.Y'));
        }

        /**
        * Инициализация хранилища данных.
        *
        * @return Auth
        */
        public function init() {
            /* Инициализируем сессию */
            $this->_session = Resources::getInstance()->session;
            $this->_session->init();

            return $this;
        }

        /**
        * Сохранение идентификатора авторизованного пользователя в сессии.
        *
        * @param  int $id
        * @return void
        */
        public function setUserId($id) {
            $this->_session->user_id = $id;
        }

        /**
        * Получение идентификатора авторизованного пользователя.
        *
        * @return int|boolean False, если не данные не найдены, иначе id.
        */
        public function getUserId() {
            return (isset($this->_session->user_id) ?
                        $this->_session->user_id :
                        false);
        }

        /**
        * Снятие авторизации, удаление идентификатора из сессии.
        */
        public function unsetUserId() {
            unset($this->_session->user_id);
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