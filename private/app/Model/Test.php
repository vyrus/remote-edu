<?php

    /**
    * Модель для работы с данными тестов.
    */
    class Model_Test extends Model_Base {
        /**
        * Создание экземпляра модели.
        *
        * @return Model_Test
        */
        public static function create() {
            return new self();
        }

        /**
        * Добавление нескольких вопросов в тест.
        *
        * @param  mixed $test_id   Идентификатор теста, к которому добавляется вопрос.
        * @param  mixed $questions Список контейнеров вопросов.
        * @return void
        */
        public function addQuestions($test_id, array $questions) {
            $sql = '
                INSERT INTO ' . $this->_tables['questions'] . '
                (test_id, type, data)
                VALUES (:tid, :type, :data)
            ';

            $stmt = $this->prepare($sql);

            $values = array(
                ':tid'  => $test_id,
                ':type' => '',
                ':data' => ''
            );

            foreach ($questions as $q) {
                $values[':type'] = $q->type;
                $values[':data'] = serialize($q);
                $stmt->execute($values);
            }
        }

        /**
        * Получение списка всех вопросов к тесту.
        *
        * @param  int $test_id Идентификатор теста.
        * @return array array(идентификатор_вопроса => объект_вопроса).
        */
        public function getQuestions($test_id) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['questions'] . '
                WHERE test_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($test_id));

            $questions = array();

            while ($q = $stmt->fetch(Db_Pdo::FETCH_OBJ)) {
                $questions[$q->question_id] = unserialize($q->data);
            }

            return $questions;
        }

        public function start() {/*_*/}

        public function stop() {/*_*/}
    }

?>