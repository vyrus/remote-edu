<?php

    /**
    * Модель для работы с контрольными точками.
    */
    class Model_Checkpoint extends Model_Base {

        /**
        * Создание экземпляра модели.
        *
        * @return Model_Checkpoint
        */
        public static function create() {
            return new self();
        }

        /**
        * Создаёт/обновляет контрольную точку.
        *
        * @param  int  $section_id Идентификатор раздела, к которому относится контрольная точка.
        * @param  bool $active Флаг активности (активная контрольная точка или нет).
        * @param  int  $title Заголовок контрольной точки.
        * @param  int  $text Тескт контрольной точки.
        * @param  int  $type Тип контрольной точки.
        * @param  int  $test_id Если тип контрольной точки - он-лайн тест, то этот параметр
                       должен содержать Идентификатор теста.
        * @return void
        */
        public function setCheckpoint($section_id, $active, $title, $text, $type, $test_id) {
            if (empty($test_id)) {
                $test_id = null;
            }
            $sql = '
                INSERT INTO ' . $this->_tables['checkpoints'] . ' (`section_id`, `active`, `title`, `text`, `type`, `test_id`)
                VALUES (:section_id, :active, :title, :text, :type, :test_id)
                ON DUPLICATE KEY UPDATE `active`=:active, `title`=:title, `text`=:text, `type`=:type, `test_id`=:test_id';
            $values = array(
                ':section_id' => $section_id,
                ':active' => $active,
                ':title' => $title,
                ':text' => $text,
                ':type' => $type,
                ':test_id' => $test_id
            );
            $this->prepare($sql)->execute($values);
        }

        /**
        * Возвращает контрольную точку.
        *
        * @param  int $section_id Идентификатор раздела, к которому относится контрольная точка.
        * @return array|false
        */
        public function getCheckpoint($section_id) {
            $sql = 'SELECT *
            FROM ' . $this->_tables['checkpoints'] . '
            WHERE section_id = :section_id';

            $values = array(
                ':section_id'   => $section_id
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Делает контрольную точку неактивной.
        *
        * @param  int $section_id Идентификатор раздела, к которому относится контрольная точка.
        * @return void
        */
        public function setCheckpointInactive($section_id) {
            $sql = '
                INSERT INTO ' . $this->_tables['checkpoints'] . ' (`section_id`, `active`)
                VALUES (:section_id, :active)
                ON DUPLICATE KEY UPDATE `active`=:active';
            $values = array(
                ':section_id' => $section_id,
                ':active' => false
            );
            $this->prepare($sql)->execute($values);
        }

        /**
        * Устанавливает прохождение контрольной точки для студента.
        *
        * @param  array $params Идентификатор студента и идентификатор раздела.
        * @return void
        */
        public function setCheckpointPass($params) {
            $sql = '
                INSERT INTO ' . $this->_tables['checkpoints_students'] . ' (`student_id`, `section_id`, `created`)
                VALUES (:student_id, :section_id, NOW())';
            $sql_params = array(
                ':student_id' => $params['student_id'],
                ':section_id' => $params['section_id']
            );
            $this->prepare($sql)->execute($sql_params);
        }

        /**
        * Удаляет прохождение контрольной точки для студента.
        *
        * @param  int $student_id Идентификатор студента.
        * @param  int $section_id Идентификатор раздела.
        * @return void
        */
        public function removeCheckpointPass($params) {
            $sql = '
                DELETE FROM ' . $this->_tables['checkpoints_students'] . '
                WHERE
                    student_id = :student_id AND
                    section_id = :section_id';
            $sql_params = array(
                ':student_id' => $params['student_id'],
                ':section_id' => $params['section_id'],
            );
            $this->prepare($sql)->execute($sql_params);
        }

        /**
        * Возвращает список контрольных точек дисциплины.
        *
        * @param  int $discipline_id Идентификатор дисциплины.
        * @return array
        */
        public function getCheckpointsByDiscipline($discipline_id) {
            $sql = 'SELECT u.user_id, cs.section_id, cs.created
            FROM ' . $this->_tables['checkpoints_students'] . ' cs
            LEFT JOIN ' . $this->_tables['users'] . ' u
                ON cs.student_id = u.user_id
            LEFT JOIN ' . $this->_tables['sections'] . ' s
                ON s.section_id = cs.section_id
            WHERE s.discipline_id = ?';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($discipline_id));

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Возвращает прогресс обучения студента по дисциплине (список
        * разделов дисциплины в совокупности с контрольными точками).
        *
        * @param  int $discipline_id Идентификатор дисциплины.
        * @return array
        */
        public function getCheckpointsSectionsByDiscipline($params) {
            $sql = 'SELECT s.title, cs.created
            FROM ' . $this->_tables['sections'] . ' s
            LEFT JOIN ' . $this->_tables['checkpoints_students'] . ' cs
                ON
                    s.section_id = cs.section_id
                    AND
                    cs.student_id = :student_id
            WHERE
                s.discipline_id = :discipline_id';

            $sql_params = array(
                ':student_id' => $params['student_id'],
                ':discipline_id' => $params['discipline_id'],
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($sql_params);

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

    }