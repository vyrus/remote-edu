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
                SELECT *
                FROM ' . $this->_tables['sections'] . '
                WHERE discipline_id = ?
                ORDER BY number ASC
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($disc_id));

            $sections = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $sections;
        }
    }

?>