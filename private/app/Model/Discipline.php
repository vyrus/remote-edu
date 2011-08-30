<?php

    /* $Id$ */

    /**
    * Класс для работы с дисциплинами.
    */
    class Model_Discipline extends Model_Base {
        /**
        * Создание экземпляра модели.
        *
        * @return Model_Discipline
        */
        public static function create() {
            return new self();
        }

        /**
        * Получение списка доступных дисциплин из программы. Доступность
        * определяет на основе тип программы (платная/бесплатная), коэффициентов
        * дисциплин и общей сумме платежей по заявке, поданной на указанную
        * программу.
        *
        * @param  int   $program_id Идентификатор программы.
        * @param  mixed $paid_type  Тип программы (платная или бесплатная).
        * @param  int   $app        Идентификатор заявки.
        * @return array Список доступных дисциплин.
        */
        public function getAllowed($program_id, $paid_type, $app_id) {
            /* Получаем список всех дисциплин в программе */
            $discs = $this->_getDisciplinesByProgramId($program_id);

            /* Если программа бесплатная, то возвращаем список всех дисциплин */
            if (Model_Education_Programs::PAID_TYPE_FREE == $paid_type) {
                return $discs;
            }

            /* Если платная, то берём общую сумму платежей по заявке */
            $payment = Model_Payment::create();
            $payment_total = $payment->getTotal($app_id);

            /* Если ещё не поступило ни одного платежа, то возвращаем пустой
            список */
            if (null == $payment_total) {
                return array();
            }

            /* Находим стоимость всей программы */
            $program = Model_Education_Programs::create();
            $program_data = $program->getProgramInfo($program_id);
            $cost_total = $program_data['cost'];

            /* По коэффициентам дисциплин рассчитываем, какие из них доступны */
            $discs = $this->_getAllowed($discs, $payment_total, $cost_total);
            return $discs;
        }
        
        /**
        * Получение списка всех дисциплин из программы. Для каждой дисциплины
        * рассчитывается, активна ли она или нет. Если нет - то сколько она стоит и сколько
        * за нее уже заплачено
        *
        * @param  int   $program_id    Идентификатор программы.
        * @param  mixed $paid_type     Тип программы (платная или бесплатная).
        * @param  int   $cost          Стоимость программы.
        * @param  int   $payment_total Сколько заплачено за программу.
        * @return array Список дисциплин.
        */
        public function getDisciplines($program_id, $paid_type, $cost, $payment_total) {
            /* Если $payment_total == null, то значит не поступило ни одного платежа */
            $payment_total = ((null == $payment_total) ? 0 : $payment_total);
            
            /* Получаем список всех дисциплин в программе */
            $discs = $this->_getDisciplinesByProgramId($program_id);
            
            /* определяем, какой процент от программы уже оплачен */
            $percent_paid = ((0 == $cost) ? 0 : round($payment_total / $cost * 100));
            
            $disciplines = array();
            
            /* если сумма заплачена полностью или программа бесплатная */
            if ($percent_paid >= 100 || Model_Education_Programs::PAID_TYPE_FREE == $paid_type) {
                foreach ($discs as $d) {
                    /* присваиваем полям стоимость дисциплины и сколько за нее заплатили соответствующие значения */
                    $d['disc_cost'] = $d['disc_paid'] = ($cost / 100) * $d['coef'];
                    $d['active'] = true;
                    $disciplines[] = $d;
                }
            }
            else {
                foreach ($discs as $d) {
                    $d['disc_cost'] = ($cost / 100) * $d['coef'];
                    /* если стоимость дисциплины меньше, чем заплатили - то она доступна */
                    if ($d['disc_cost'] <= $payment_total) {
                        $d['disc_paid'] = $d['disc_cost'];
                        /* дисциплина доступна */
                        $d['active'] = true;
                    }
                    /* в ином случае - если заплатили хоть что-то - то заносим это в disc_paid,
                    чтобы потом выводить пользователю, например, заплачено 20р из 40р */
                    elseif ($payment_total >= 0) {
                        $d['disc_paid'] = $payment_total;
                        /* дисциплина недоступна */
                        $d['active'] = false;
                    }
                    /* в ином случае заносим в disc_paid 0 */
                    else {
                        $d['disc_paid'] = 0;
                        /* дисциплина недоступна */
                        $d['active'] = false;
                    }
                    
                    $payment_total -= $d['disc_cost'];
                    $disciplines[] = $d;
                }
            }
            
            return $disciplines;
        }

        /**
        * Полученные данных о дисциплине.
        *
        * @param  int $disc_id Идентификатор дисциплины.
        * @return array
        */
        public function get($disc_id) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['disciplines'] . '
                WHERE discipline_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($disc_id));

            $data = $stmt->fetch(Db_Pdo::FETCH_ASSOC);
            return $data;
        }

        /**
        * Получение списка всех дисциплин из заданной программы.
        *
        * @param  int $program_id Идентификатор программы.
        * @return array
        */
        protected function _getDisciplinesByProgramId($program_id) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['disciplines'] . '
                WHERE program_id = ?
                ORDER BY serial_number ASC
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($program_id));

            $discs = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $discs;
        }

        /**
        * Возвращает список всех дисциплин, входящих в образовательную
        * программу.
        *
        * @param  int $program_id Идентификатор программы.
        * @return array
        */
        public function getDisciplinesIdByProgramId($program_id) {
            $sql = '
                SELECT discipline_id
                FROM ' . $this->_tables['disciplines'] . '
                WHERE program_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($program_id));

            $discs = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $discs;
        }

        /**
        * Выбирает из списка переданных дисциплин те, которые в соответствии с
        * их коэффициентами в рамках программы и общей суммы оплаты по заявке
        * считаются доступными для слушателя.
        * В списке дисциплин по ключу "coef" должен присутствовать коэффициент
        * дисциплины.
        *
        * @param array     $disciplines Список дисциплин.
        * @param int|float $payment     Обшая сумма оплаты.
        * @param int|float $cost        Стоимость программы.
        */
        protected function _getAllowed
        (
            array $disciplines = array(), $payment, $cost
        )
        {
            /* Определяем, какой процент из общей стомости программы оплачен */
            $percent_paid = round($payment / $cost * 100);

            /* Список доступных дисциплин */
            $allowed = array();
            /* Накопленный процент стоимости нескольких дисциплин */
            $percent_accum = 0;

            /* Беребираем список дисциплин */
            foreach ($disciplines as $disc)
            {
                /* Прибавляем коэффициент текущей дисциплины */
                $percent_accum += $disc['coef'];

                /* Если оплачен больший процент программы, чем накоплен, */
                if ($percent_paid >= $percent_accum) {
                    /* то добавляем текущую дисциплину к списку доступных */
                    $allowed[] = $disc;
                } else {
                    break;
                }
            }

            return $allowed;
        }

	    /**
		* Открыта ли дисциплина для студента
		*
		* @param int $disciplineId
		* @param int $studentId
		* @param array $statuses Статусы заявок студента
		* @return bool
	    */
        public function isDisciplineOpenForStudent($disciplineId, $studentId, $statuses = false) {
            $result = false;

            $modelApps = Model_Application::create();
            if (!$statuses) {
                $statuses = $modelApps->getAppsStatus($disciplineId, $studentId);
            }

            if (array_key_exists(Model_Application::STATUS_PREPAID, $statuses) || array_key_exists(Model_Application::STATUS_FINISHED, $statuses)) {
                $result = true;
            } elseif ($k = array_search(Model_Application::STATUS_SIGNED, $statuses) !== false) {

                foreach ($statuses[$k] as $appId) {
                    $appInfo = $modelApps->getAppInfo($appId);
                    $modelEducationPrograms = Model_Education_Programs::create();

                    if ($appInfo['type'] == 'discipline') {
                        $programId = $modelEducationPrograms->getProgramIdByDiscipline($appInfo['object_id']);
                    } elseif ($appInfo['type'] == 'program') {
                        $programId = $appInfo['object_id'];
                    }

                    $programInfo = $modelEducationPrograms->getProgramInfo($programId);
                    if ($programInfo['paid_type'] == Model_Education_Programs::PAID_TYPE_FREE) {
                        $result = true;
                        break;
                    } else {
                        $modelPayment = Model_Payment::create();
                        $total_payment = $modelPayment->getTotal($appId);
                        $cost_total = $program_data['cost'];
                        $ar = $this->_getAllowed(array($disciplineId), $payment_total, $cost_total);
                        if (!empty($ar)) {
                            $result = true;
                            break;
                        }
                    }
                }
            }
            return $result;
        }


    }

?>
