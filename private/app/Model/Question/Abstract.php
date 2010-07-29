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

        abstract public function validate();

        abstract public function getExamData();

        abstract public function freeze();

        public static function thaw($type, $data) {
            $pattern = '/-([a-z0-9]{1})/e';
            $replace = 'strtoupper("$1")';

            $class = preg_replace($pattern, $replace, $type);
            $class = 'Model_Question_' . ucfirst($class);

            $callback = array($class, __FUNCTION__);
            return call_user_func($callback, $data);
        }
    }

?>