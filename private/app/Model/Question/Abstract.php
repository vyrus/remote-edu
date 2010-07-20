<?php

    /**
    * Абстракатный контейнер для хранения данных вопросов к тестам.
    */
    abstract class Model_Question_Abstract {
        /**
        * Создание экземпляра контейнера.
        */
        abstract public function create();

        /**
        * @todo PHP Magic methods.
        */
        abstract public function __sleep();
    }

?>