<?php

    class Model_Test_Exception extends Exception {
        const NOT_ENOUGH_QUESTIONS = 0;

        const EXAM_IS_NOT_REGISTERED = 1;

        const EXAM_QUESTIONS_MISMATCHED = 2;

        protected $_messages_map = array(
            self::NOT_ENOUGH_QUESTIONS      => 'В базе недостаточно вопросов.',
            self::EXAM_IS_NOT_REGISTERED    => 'Сдача теста не была зарегистрирована.',
            self::EXAM_QUESTIONS_MISMATCHED => 'Вопросы, на которые были даны ответы, не совпадают с выданными.',
        );

        public function __construct(
            $code = 0, $message = null, Exception $previous = NULL
        ) {
            if (null === $message) {
                $message = $this->_messages_map[$code];
            }

            parent::__construct($message, $code);
        }
    }

?>
