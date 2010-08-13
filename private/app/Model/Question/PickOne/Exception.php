<?php

    class Model_Question_PickOne_Exception extends Exception {
        const NO_CORRECT_ANSWER = 0;

        const EMPTY_FIELDS = 1;

        const INVALID_HTML = 2;

        public $errors_map = array(
            self::NO_CORRECT_ANSWER  => 'Не указан правильный вариант ответа',
            self::EMPTY_FIELDS       => 'Заполнены не все поля формы вопроса',
            self::INVALID_HTML       => 'Недопустимый HTML-код'
        );

        public function __construct(
            $code = 0, $message = '', Exception $previous = NULL
        ) {
            parent::__construct($message, $code);
        }

        public function getErrorMessage() {
            $code = $this->getCode();
            $error = $this->errors_map[$code];

            $msg = $this->getMessage();
            if ($msg) {
                $error .= $msg;
            }

            return $error;
        }
    }

?>