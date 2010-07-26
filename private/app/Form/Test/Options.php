<?php

    /* $Id$ */

    /**
    * Форма параметров теста.
    */
    class Form_Test_Options extends Form_Abstract {
        /**
        * Инициализация формы.
        *
        * @return void
        */
        public function __construct() {
            $this
                /* Параметры формы */
                ->setMethod(self::METHOD_POST)

                /* Тема тестирования */
                ->addField('theme')
                ->setValidator('/^.{1,256}$/ixu')
                ->setError('Введите тему тестирования (не длиннее 256 символов)')

                /* Количество вопросов в тесте */
                ->addField('num_questions')
                ->setValidator('/^[0-9]+$/ixu')
                ->setError('Введите целое число вопросов')

                /* Допустимое количество ошибок */
                ->addField('errors_limit')
                ->setValidator('/^(?:0*[1-9]{1}|[1-9]{1}[0-9]{1}|100)$/ixu')
                ->setError('Введите число от 1 до 100')

                /* Количество попыток тестирования */
                ->addField('attempts_limit')
                ->setValidator('/^[0-9]+$/ixu')
                ->setError('Введите целое число попыток сдать тест')
            ;
        }

        /**
        * Создание экземпляра класса.
        *
        * @return Form_Payment_Add
        */
        public static function create() {
            return new self();
        }
    }

?>