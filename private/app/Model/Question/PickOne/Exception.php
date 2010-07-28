<?php

    class Model_Question_PickOne_Exception extends Exception {
        const NO_CORRECT_ANSWER = 0;

        public $errors_map = array(
            self::NO_CORRECT_ANSWER => 'Не указан правильный вариант ответа'
        );

        public function __construct(
            $code = 0, $message = '', Exception $previous = NULL
        ) {
            parent::__construct($message, $code);
        }
    }

?>