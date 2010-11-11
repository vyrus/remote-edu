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
        * Устанавливает прохождение контрольной точки для слушателя.
        *
        * @param  int student_id Идентификатор слушателя
        * @param  int section_id Идентификатор раздела.
        * @return void
        */
        public function setCheckpointPass($student_id, $section_id) {
            $sql = '
                INSERT INTO ' . $this->_tables['checkpoints_students'] . ' (`student_id`, `section_id`, `created`)
                VALUES (:student_id, :section_id, NOW())';
            $sql_params = array(
                ':student_id' => $student_id,
                ':section_id' => $section_id
            );
            return $this->prepare($sql)->execute($sql_params);
        }

        /**
        * Удаляет прохождение контрольной точки для слушателя.
        *
        * @param  int student_id Идентификатор слушателя.
        * @param  int section_id Идентификатор раздела.
        * @return void
        */
        public function removeCheckpointPass($student_id, $section_id) {
            $sql = '
                DELETE FROM ' . $this->_tables['checkpoints_students'] . '
                WHERE
                    student_id = :student_id AND
                    section_id = :section_id';
            $sql_params = array(
                ':student_id' => $student_id,
                ':section_id' => $section_id
            );
            return $this->prepare($sql)->execute($sql_params);
        }

        /**
        * Открывает доступ к разделу дисциплины, следующему за указанным, если он существует.
        * Иначе открывает доступ к первому разделу следующей дисциплины, если она существует.
        *
        * @param  int student_id Идентификатор слушателя
        * @param  int section_id Идентификатор раздела.
        * @return void
        */
        public function setNextSectionPass($student_id, $section_id) {
            $sql = 'SELECT s.discipline_id, s.number
            FROM ' . $this->_tables['sections'] . ' s
            WHERE
                s.section_id = ?';
            $stmt = $this->prepare($sql);
            $stmt->execute(array($section_id));
            $section = $stmt->fetch(Db_Pdo::FETCH_ASSOC);

            $sql = 'SELECT s.section_id
            FROM ' . $this->_tables['sections'] . ' s
            WHERE
                s.discipline_id = :discipline_id
                AND
                s.number = :number';
            $sql_params = array(
                ':discipline_id' => $section['discipline_id'],
                ':number' => $section['number'] + 1
            );
            $stmt = $this->prepare($sql);
            $stmt->execute($sql_params);
            $next_section = $stmt->fetch(Db_Pdo::FETCH_ASSOC);

            if (!$next_section) {
                $sql = 'SELECT d.program_id, d.serial_number
                FROM ' . $this->_tables['disciplines'] . ' d
                WHERE
                    d.discipline_id = ?';
                $stmt = $this->prepare($sql);
                $stmt->execute(array($section['discipline_id']));
                $discipline = $stmt->fetch(Db_Pdo::FETCH_ASSOC);

                $sql = 'SELECT d.discipline_id
                FROM ' . $this->_tables['disciplines'] . ' d
                WHERE
                    d.program_id = :program_id
                    AND
                    d.serial_number = :serial_number';
                $sql_params = array(
                    ':program_id' => $discipline['program_id'],
                    ':serial_number' => $discipline['serial_number'] + 1
                );
                $stmt = $this->prepare($sql);
                $stmt->execute($sql_params);
                $next_discipline = $stmt->fetch(Db_Pdo::FETCH_ASSOC);

                $model_education_programs = Model_Education_Programs::create();
                $next_section = $model_education_programs->getFirstSectionOfDiscipline($next_discipline['discipline_id']);
            }

            if ($next_section) {
                return $this->setCheckpointPass($student_id, $next_section['section_id']);
            }

            return false;
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
        * @param  int $student_id Идентификатор слушателя.
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