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

        public function freeze() {
            return serialize(array(
                'question'       => $this->question,
                'answers'        => $this->answers,
                'correct_answer' => $this->correct_answer
            ));
        }

        public static function thaw($data) {
            $data = (object) unserialize($data);

            $q = self::create();

            $q->question        = $data->question;
            $q->answers         = $data->answers;
            $q->correct_answer  = $data->correct_answer;

            return $q;
        }
    }

?>
