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

        /**
        * Добавление нескольких вопросов в тест.
        *
        * @param mixed $test_id   Идентификатор теста, к которому добавляется вопрос.
        * @param mixed $questions Список контейнеров вопросов.
        */
        public function addMulti($test_id, array $questions) {
            $sql = '
                INSERT INTO ' . $this->_tables['questions'] . '
                (test_id, type, data)
                VALUES (:tid, :type, :data)
            ';

            $stmt = $this->prepare($sql);

            $values = array(
                ':tid'  => $test_id,
                ':type' => self::TYPE_PICK_ONE,
                ':data' => ''
            );

            foreach ($questions as $q) {
                $values[':data'] = serialize($q);
                $stmt->execute($values);
            }
        }
    }

?>