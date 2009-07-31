<?php
    
    /* $Id$ */

    /**
    * Класс формы регистрации.
    */
    class Form_Registration extends Form_Abstract {
        /**
        * Метод-конструктор класса. Заполняет структуру формы.
        * 
        * @return void
        */
        public function __construct() {
            $this
                /* Устанавливаем параметры формы */
                ->setAction('/users/register/')
                ->setMethod(self::METHOD_POST)
                
                /* Поле "Имя пользователя" */
                ->addField('login')
                ->setValidator('/^[a-z0-9_]{3,}$/ixu')
                ->setError(
                    'Имя пользователя должно состоять из букв латинского ' . 
                    'алфавита, цифр и символа подчёркивания (минимальная' .
                    'длина - 3 символа)'
                )
                
                /* Поле "Пароль" */
                ->addField('passwd')
                ->setValidator('/^[a-z0-9_]+$/ixu')
                ->setError(
                    'Пароль должен состоять из латинских букв, цифр и ' .
                    'символа подчёркивания'
                )
                                 
                /* Поле "e-mail" */
                ->addField('email')
                ->setValidator('|[0-9a-z-]+@[0-9a-z-^\.]+\.[a-z]{2,6}|i')
                ->setError(
                    'Некорректный адрес электронной почты'
                )
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