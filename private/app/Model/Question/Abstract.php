<?php

    /**
    * Абстракатный контейнер для хранения данных вопросов к тестам.
    */
    abstract class Model_Question_Abstract {
        /**
        * Тип вопроса.
        *
        * @var mixed
        */
        public $type;

        /**
        * Создание экземпляра контейнера.
        */
        abstract public static function create();

        /**
        * @todo PHP Magic methods.
        */
        abstract public function __sleep();
    }

?>