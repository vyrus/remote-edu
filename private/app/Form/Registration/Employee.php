<?php
    
    /* $Id$ */

    /**
    * Класс формы регистрации сотрудника.
    */
    class Form_Registration_Employee extends Form_Registration {
        /**
        * Метод-конструктор класса. Заполняет структуру формы.
        * 
        * @return void
        */
        public function __construct($action) {
            $this
                /* Устанавливаем параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                
                /* Имя пользователя */
                ->addField('login')
                ->setValidator('/^[a-z0-9_]{3,}$/ixu')
                ->setError(
                    'Имя пользователя должно состоять из букв латинского ' . 
                    'алфавита, цифр и символа подчёркивания (минимальная' .
                    'длина - 3 символа)'
                )
                
                /* Роль */
                ->addField('role')
                ->setValidator('/^(?:teacher|admin)$/ixu')
                ->setError(
                    'Некорректно задана роль пользователя'
                )
                                 
                /* Email */
                ->addField('email')
                ->setValidator('|[0-9a-z-]+@[0-9a-z-^\.]+\.[a-z]{2,6}|i')
                ->setError(
                    'Некорректный адрес электронной почты'
                )
                
                /* Фамилия */
                ->addField('surname')
                ->setValidator('/^[А-Я]{1}[а-я]{1,}$/xu')
                ->setError(
                    'Фамилия должны состоять минимум из 2-х букв русского (первая - заглавная)'
                )
                
                /* Имя */
                ->addField('name')
                ->setValidator('/^[А-Я]{1}[а-я]{1,}$/xu')
                ->setError(
                    'Имя должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                )
                
                /* Отчество */
                ->addField('patronymic')
                ->setValidator('/^[А-Я]{1}[а-я]{1,}$/xu')
                ->setError(
                    'Отчество должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                )
            ;
        }
        
        /**
        * Создание экземпляра класса.
        * 
        * @return Form_Registration
        */
        public static function create($action) {
            return new self($action);
        }
    }

?>