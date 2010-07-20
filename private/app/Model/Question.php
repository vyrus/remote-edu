<?php

    /**
    * Модель для работы с вопросами тестов.
    */
    class Model_Question extends Model_Base {
        /**
        * Тип вопроса: вопрос с выбором одного верного ответа.
        *
        * @var const
        */
        const TYPE_PICK_ONE = 'pick-one';

        /**
        * Создание экземпляра модели.
        *
        * @return Model_Question
        */
        public static function create() {
            return new self();
        }
    }

?>
