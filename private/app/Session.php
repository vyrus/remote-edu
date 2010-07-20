<?php

    /**
    * Класс для работы с сессиями PHP.
    */
    class Session {
        /**
        * Создание экземляра класса.
        *
        * @return Session
        */
        public static function create() {
            return new self();
        }

        public function init() {
            /* Получаем идентификатор текущей сессии */
            $sid = session_id();

            /* Если он пустой, то есть сессия ещё не начата, начинаем сессию */
            if (empty($sid)) {
                session_start();
            }
        }

        /**
        * Установка значения переменной сессии.
        *
        * @link http://www.php.net/manual/en/language.oop5.overloading.php
        *
        * @param  mixed $name  Переменная.
        * @param  mixed $value Значение.
        * @return void
        */
        public function __set($name, $value) {
            $_SESSION[$name] = $value;
        }

        /**
        * Получение значения переменной сессии.
        *
        * @link http://www.php.net/manual/en/language.oop5.overloading.php
        *
        * @param  mixed $name Переменная.
        * @return mixed
        */
        public function __get($name) {
            return $_SESSION[$name];
        }

        /**
        * Проверка, установлено ли значение для переменной сессии.
        *
        * @link http://www.php.net/manual/en/language.oop5.overloading.php
        *
        * @param  mixed $name  Переменная.
        * @return boolean
        */
        public function __isset($name) {
            return isset($_SESSION[$name]);
        }

        /**
        * Сброс переменной сессии.
        *
        * @param  mixed $name Переменная.
        * @return void
        */
        public function __unset($name) {
            unset($_SESSION[$name]);
        }
    }

?>
