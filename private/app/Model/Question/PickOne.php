<?php

    /**
    * Контейнер для данных вопроса с одним правильным ответом.
    */
    class Model_Question_PickOne extends Model_Question_Abstract {
        /**
        * Тип вопроса.
        *
        * @var mixed
        */
        protected $_type = Model_Question::TYPE_PICK_ONE;

        /**
        * Текст вопроса.
        *
        * @var string
        */
        public $question;

        /**
        * Список ответов.
        *
        * @var array
        */
        public $answers;

        /**
        * Ключ правильного ответа из массива self::$answers;
        *
        * @var mixed
        */
        public $correct_answer;

        /**
        * Создание нового экзепляра контейнера.
        *
        * @return Model_Question_PickOne
        */
        public static function create() {
            return new self();
        }

        /**
        * Метод для сериализации объекта.
        *
        * @link http://www.php.net/manual/en/language.oop5.magic.php
        *
        * @return array Список атрибутов для сериализации.
        */
        public function __sleep() {
            return array('question', 'answers', 'correct_answer');
        }
    }

?>
