<?php

    /* $Id$ */

    class Form_Profile_Login extends Form_Profile_Abstract {
        /**
        * Метод-конструктор класса. Заполняет структуру формы.
        *
        * @return void
        */
        public function __construct($action) {
            $this
                /* Параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)

                /* Логин */
                ->_addLogin()
                ->setValidator('/^.+$/ixu')
                ->setError('Введите имя пользователя')

                /* Пароль */
                ->_addPasswd()
                ->setValidator('/^.+$/ixu')
                ->setError('Введите пароль')
            ;
        }

        public static function create($action) {
            return new self($action);
        }
    }

?>