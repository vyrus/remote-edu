<?php

    /* $Id: $ */

    /**
    * Модель для работы со студентами.
    */
    class Model_Education_Students extends Model_Base {

        /**
        * Создание экземпляра модели.
        *
        * @return Model_Education_Students
        */
        public static function create() {
            return new self();
        }

        /**
        * Получение списка всех студентов.
        *
        * @return array|false
        */
        public function getStudentList() {
            $sql = '
                SELECT user_id, login
                FROM ' . $this->_tables['users'] . '
                WHERE role = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array('student'));

            return $stmt->fetchAll();
        }

        /**
        * Получение списка слушателей, куратором которых является указанный преподаватель.
        *
        * @param  int $curator_id Идентификатор преподавателя.
        * @return array|false
        */
        public function getListenerList($curator_id) {
            $sql = '
                SELECT user_id, login, surname, name, patronymic
                FROM ' . $this->_tables['users'] . '
                WHERE
                    role = :role
                    AND
                    curator = :curator
            ';

            $values = array(
                ':curator' => $curator_id,
                ':role'    => 'student'
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Получение списка отдельных дисциплин, изучаемых слушателем.
        *
        * @param  int $student_id Идентификатор слушателя.
        * @return array|false
        */
        public function getDisciplines($student_id) {
            $sql = '
                SELECT a.object_id AS id, d.title
                FROM ' . $this->_tables['applications'] . ' a
                JOIN ' . $this->_tables['disciplines'] . ' d
                    ON a.object_id = d.discipline_id
                WHERE
                    a.user_id = ?
                    AND
                    a.type = \'discipline\'
                    AND
                    a.status = \'signed\'
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($student_id));

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Получение списка дисциплин, входящих в программы,
        * изучаемые слушателем.
        *
        * @param  int $student_id Идентификатор слушателя.
        * @return array|false
        */
        public function getDisciplinesPrograms($student_id) {
            /*
            $sql = '
                SELECT d.discipline_id AS id, d.title
                FROM ' . $this->_tables['applications'] . ' a
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON a.object_id = d.program_id
                WHERE
                    a.user_id = ?
                    AND
                    a.type = \'program\'
                    AND (
                        a.status = \'accepted\'
                        OR
                        a.status = \'signed\'
                    )
            ';
            */
            
            $sql = '
                SELECT d.discipline_id AS id, d.title AS d_title, p.title as p_title
                FROM ' . $this->_tables['applications'] . ' a
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON a.object_id = d.program_id
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON d.program_id = p.program_id
                WHERE
                    a.user_id = ?
                    AND
                    a.type = \'program\'
                    AND (
                        a.status = \'accepted\'
                        OR
                        a.status = \'signed\'
                    )
				ORDER BY d.serial_number
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($student_id));
            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

    }
