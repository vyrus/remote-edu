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
                                 
                /* Поле "Ф.И.О." */
                ->addField('fio')
                ->setValidator('/^(?:[а-я]{2,}[\s]{1}){2}[а-я]{2,}$/ixu')
                ->setError(
                    'Ф.И.О. должно состоять из трёх слов, разделённых пробелами'
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