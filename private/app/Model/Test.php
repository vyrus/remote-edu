<?php

    /**
    * Модель для работы с данными тестов.
    */
    class Model_Test extends Model_Base {
        const TIME_PER_QUESTION = 120;

        /**
        * Создание экземпляра модели.
        *
        * @return Model_Test
        */
        public static function create() {
            return new self();
        }

        public function add(
            $theme, $num_questions, /*$time_limit, */$errors_limit,
            $attempts_limit
        ) {
            $sql = '
                INSERT INTO ' . $this->_tables['tests']  . '
                (theme, num_questions, time_limit, errors_limit, attempts_limit)
                VALUES (
                    :theme, :num_questions, :time_limit, :attempts_limit,
                    :errors_limit
                )
            ';

            $values = array(
                ':theme'          => $theme,
                ':num_questions'  => $num_questions,
                ':time_limit'     => $num_questions * self::TIME_PER_QUESTION,
                ':errors_limit'   => $errors_limit,
                ':attempts_limit' => $attempts_limit
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            return $this->lastInsertId();
        }

        public function update(
            $test_id, $theme, $num_questions, /*$time_limit, */$errors_limit,
            $attempts_limit
        ) {
            $sql = '
                UPDATE ' . $this->_tables['tests']  . '
                SET theme          = :theme,
                    num_questions  = :num_questions,
                    time_limit     = :time_limit,
                    errors_limit   = :errors_limit,
                    attempts_limit = :attempts_limit
                WHERE test_id = :tid
            ';

            $values = array(
                ':tid'            => $test_id,
                ':theme'          => $theme,
                ':num_questions'  => $num_questions,
                ':time_limit'     => $num_questions * self::TIME_PER_QUESTION,
                ':errors_limit'   => $errors_limit,
                ':attempts_limit' => $attempts_limit
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $row_count = $stmt->rowCount();
            return $row_count > 0;
        }

        public function get($test_id) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['tests'] . '
                WHERE test_id = ?
                LIMIT 1
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($test_id));

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Добавление нескольких вопросов в тест.
        *
        * @param  mixed $test_id   Идентификатор теста, к которому добавляется вопрос.
        * @param  mixed $questions Список контейнеров вопросов.
        * @return //
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

            $new_ids = new stdClass();

            foreach ($questions as $q) {
                $q = (object) $q;

                $values[':type'] = $q->obj->getType();
                $values[':data'] = $q->obj->freeze();
                $stmt->execute($values);

                /**
                * Performing string convertion in order to save all keys in
                * JSON.
                */
                $new_ids->{$q->tmp_id} = $this->lastInsertId();
            }

            return $new_ids;
        }

        public function editQuestions(array $questions) {
            $sql = '
                UPDATE ' . $this->_tables['questions'] . '
                SET data = :data
                WHERE question_id = :qid
            ';

            $stmt = $this->prepare($sql);

            foreach ($questions as $q)
            {
                $q = (object) $q;

                $values = array(
                    ':qid'  => $q->question_id,
                    ':data' => $q->obj->freeze()
                );
                $stmt->execute($values);
            }
        }

        /**
        * Получение списка всех вопросов к тесту.
        *
        * @param  int     $test_id Идентификатор теста.
        * @param  boolean $thaw    Создавать ли объекты вопросов или оставить сырые данные.
        * @return array array(идентификатор_вопроса => объект_вопроса).
        */
        public function getQuestions($test_id, $thaw = true) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['questions'] . '
                WHERE test_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($test_id));

            $questions = array();

            while ($q = $stmt->fetch(Db_Pdo::FETCH_OBJ))
            {
                if ($thaw) {
                    $q_data = Model_Question_Abstract::thaw($q->type, $q->data);
                } else {
                    $q_data = unserialize($q->data);
                    $q_data['type'] = $q->type;
                    $q_data['question_id'] = $q->question_id;
                }

                $questions[] = $q_data;
            }

            return $questions;
        }

        public function start() {/*_*/}

        public function stop() {/*_*/}
    }

?>