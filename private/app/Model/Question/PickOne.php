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

        public function validate() {
            $errors = array();

            $answer = & $this->answers[$this->correct_answer];
            if (!isset($answer))
            {
                $code = Model_Question_PickOne_Exception::NO_CORRECT_ANSWER;

                $errors['question'] =
                    new Model_Question_PickOne_Exception($code);
            }
            unset($answer);

            foreach ($this->answers as $key => $answer) {
                if (empty($answer)) {
                    unset($this->answers[$key]);
                }
            }

            $empty = empty($this->question) || sizeof($this->answers) < 4;
            if ($empty) {
                $code = Model_Question_PickOne_Exception::EMPTY_FIELDS;

                $errors['question'] =
                    new Model_Question_PickOne_Exception($code);
            }

            return $errors;
        }

        public function getExamData() {
            return array(
                'question' => $this->question,
                'answers'  => $this->answers
            );
        }

        public function isCorrectAnswer($answer) {
            return ($this->correct_answer == $answer);
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
