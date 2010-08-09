<?php

    class Model_Question_PickOne_Exception extends Exception {
        const NO_CORRECT_ANSWER = 0;

        const EMPTY_FIELDS = 1;

        public $errors_map = array(
            self::NO_CORRECT_ANSWER => 'Не указан правильный вариант ответа',
            self::EMPTY_FIELDS      => 'Заполнены не все поля формы вопроса'
        );

        public function __construct(
            $code = 0, $message = '', Exception $previous = NULL
        ) {
            parent::__construct($message, $code);
        }
    }

?>