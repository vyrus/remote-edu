<?php

    /* $Id$ */
    
    /**
    * Форма добавления нового платежа по заявке.
    */
    class Form_Payment_Add extends Form_Abstract {
        /**
        * Инициализация формы.
        * 
        * @param string $action Значение атрибута "action" формы.
        * @return void
        */
        public function __construct($action) {
            $this
                /* Параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                
                /* Размер платежа */
                ->addField('amount')
                ->setValidator('/[0-9]+(?:\.[0-9]+)?/ixu')
                ->setError('Введите размер платежа, например "1900.53"')
            ;
        }
        
        /**
        * Создание экземпляра класса.
        * 
        * @return Form_Payment_Add
        */
        public static function create($action) {
            return new self($action);
        }
    }
 
?>