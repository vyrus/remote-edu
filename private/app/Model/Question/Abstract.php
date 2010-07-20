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
        protected $_type;

        /**
        * Создание экземпляра контейнера.
        */
        abstract public static function create();

        /**
        * Возвращает тип вопроса.
        *
        * @return mixed
        */
        public function getType() {
            return $this->_type;
        }

        /**
        * @todo PHP Magic methods.
        */
        abstract public function __sleep();
    }

?>