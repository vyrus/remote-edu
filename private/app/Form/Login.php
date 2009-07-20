<?php
    
    /* $Id$ */

    /**
    * Класс формы авторизации.
    */
    class Form_Login extends Form_Abstract {
        /**
        * Метод-конструктор класса. Заполняет структуру формы.
        * 
        * @return void
        */
        public function __construct() {
            $this
                /* Устанавливаем параметры формы */
                ->setAction('/users/login/')
                ->setMethod(self::METHOD_POST)
                
                /* Поле "Имя пользователя" */
                ->addField('login')
                ->setValidator('/^.+$/ixu')
                ->setError('Введите имя пользователя')
                
                /* Поле "Пароль" */
                ->addField('passwd')
                ->setValidator('/^.+$/ixu')
                ->setError('Введите пароль')
            ;
        }
        
        /**
        * Создание экземпляра класса.
        * 
        * @return Form_Registration
        */
        public static function create() {
            return new self();
        }
    }

?>