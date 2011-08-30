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
                    a.user_id = :uid
                    AND
                    a.type = "discipline"
                    AND
                    (a.status = :signed OR a.status = :prepaid OR status = :finished)
                ';

            $values = array (
                ':uid'  => $student_id,
                ':signed' => Model_Application::STATUS_SIGNED,
                ':prepaid' => Model_Application::STATUS_PREPAID,
                ':finished' => Model_Application::STATUS_FINISHED
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

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
                SELECT d.discipline_id AS id, d.title AS d_title, p.title AS p_title
                FROM ' . $this->_tables['applications'] . ' a
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON a.object_id = d.program_id
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON d.program_id = p.program_id
                WHERE
                    a.user_id = :uid
                    AND
                    a.type = \'program\'
                    AND 
                    (a.status = :signed OR a.status = :prepaid OR status = :finished)
				ORDER BY d.serial_number
                ';

            $values = array (
                ':uid'  => $student_id,
                ':signed' => Model_Application::STATUS_SIGNED,
                ':prepaid' => Model_Application::STATUS_PREPAID,
                ':finished' => Model_Application::STATUS_FINISHED
            );


            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

	    /**
        * Получить список доступных дисциплин из программ для студента
        * Идентификаторы доступных дисциплин сохраняются в сессии в массив availDisciplines
		*
		* @param int $student_id
		* @return array
	    */
        public function getAvailDisciplinesForPrograms($student_id) {
            $result = array();

            $disc = Model_Discipline::create();
            $session = Resources_Abstract::getInstance()->session;

            $app = Model_Application::create();
            // Получаем список заявок на образовательные программы
            $program_apps = $app->getProcessedAppsForPrograms($student_id);

            foreach ($program_apps as $a) {
                if (
                    /* Если программа бесплатная */
                    Model_Education_Programs::PAID_TYPE_FREE == $a['paid_type'] &&
                    /* и статус заявки подобает, */
                    ( Model_Application::STATUS_SIGNED == $a['status'] ||
                     Model_Application::STATUS_FINISHED == $a['status'] )
                    ||
                    /* Или если программа платная */
                    Model_Education_Programs::PAID_TYPE_PAID == $a['paid_type'] &&
                    /* и статус заявки подобает, */
                    (Model_Application::STATUS_SIGNED == $a['status'] || 
                    Model_Application::STATUS_PREPAID == $a['status'] || 
                    Model_Application::STATUS_FINISHED == $a['status'])
                ) {
                    // получаем список доступных дисциплин
                    $a['disciplines'] = $disc->getDisciplines($a['object_id'], $a['paid_type'], $a['cost'], $a['total_sum']);
                    $result[] = $a;
                    // сохраняем в сессии как доступную
                    foreach ($a['disciplines'] as $rec) {
                        $session->availDisciplines[] = $rec['discipline_id'];
                    }
                }
            }
            return $result;        
        }


	    /**
        * Получить список доступных отдельных дисциплин для студента
        * Идентификаторы доступных дисциплин сохраняются в сессии в массив availDisciplines
		*
		* @param int $student_id
		* @return array
	    */
        public function getAvailDisciplinesSeparate($student_id) {
            $result = array();

            $disc = Model_Discipline::create();
            $session = Resources_Abstract::getInstance()->session;

            $app = Model_Application::create();
            // Получаем список заявок на отдельные дициплины
            $disc_app = $app->getProcessedAppsForDisciplines($student_id);

            foreach ($disc_app as $a) {
                $a['cost'] = ((null === $a['cost']) ? 0 : $a['cost']);
                $a['total_sum'] = ((null === $a['total_sum']) ? 0 : $a['total_sum']);
                $a['disc_sum'] = ($a['cost'] / 100) * $a['coef'];
                
                /* Если программа, которой принадлежит дисциплина, платная */
                if (Model_Education_Programs::PAID_TYPE_PAID == $a['paid_type']) {
                    /* и статус заявки подобает, */
                    if (Model_Application::STATUS_SIGNED !== $a['status'] && 
                        Model_Application::STATUS_PREPAID !== $a['status'] && 
                        Model_Application::STATUS_FINISHED !== $a['status']) { 
                        continue;
                    }
                    
                    $active = true;
                    // сохраняем в сессии как доступную
                    $session->availDisciplines[] = $a['object_id'];

                }
                /* Если же программа бесплатная */
                elseif (Model_Education_Programs::PAID_TYPE_FREE == $a['paid_type']) {
                    /* и статус заявки подобает, */
                    if (Model_Application::STATUS_SIGNED !== $a['status'] &&
                        Model_Application::STATUS_FINISHED !== $a['status'] ) {
                        continue;
                    }

                    $active = true;
                    // сохраняем в сессии как доступную
                    $session->availDisciplines[] = $a['object_id'];
                }
                
                /* И заносим её в список доступных */
                $disc = array(
                    'discipline_id' => $a['object_id'],
                    'title'         => $a['title'],
                    'app_id'        => $a['app_id'],
                    'disc_sum'      => $a['disc_sum'],
                    'total_sum'     => $a['total_sum'],
                    'active'        => $active
                );
                $result[] = $disc;
            }
            return $result;
        }


    }
