<?php

    /* $Id$ */

    /**
    * Модель для работы с разделами дисциплин.
    */
    class Model_Section extends Model_Base {
        /**
        * Создание нового экземпляра модели.
        *
        * @return Model_Section
        */
        public static function create() {
            return new self();
        }

        /**
        * Получение списка всех разделов по идентификатору дисциплины.
        *
        * @param  int $disc_id Идентификатор дисциплины.
        * @return array
        */
        public function getAllByDiscipline($disc_id) {
            $sql = '
                SELECT s.*, c.test_id, t.theme AS test_theme
                FROM ' . $this->_tables['sections'] . ' s
                LEFT JOIN ' . $this->_tables['checkpoints'] . ' c
                    ON s.section_id = c.section_id
                LEFT JOIN ' . $this->_tables['tests'] . ' t
                    ON t.test_id = c.test_id
                WHERE discipline_id = ?
                ORDER BY number ASC
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($disc_id));

            $sections = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $sections;
        }
    }