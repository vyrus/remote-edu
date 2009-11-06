<?php

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
            
            /* Если платная, то берём общую сумму платажей по заявке */
            $payment = Model_Payment::create();
            $payment_total = $payment->getTotal($app_id);
            
            /* Если ещё не поступило ни одного платежа, то возвращаем пустой
            список */
            if (null == $payment_total) {
                return array();
            }
            
            /**
            * @todo Брать реальную стоимость направления.
            */
            /* Находим стоимость всей программы */
            $program = Model_Education_Programs::create();
            $cost_total = 1000;
            
            /* По коэффициентам дисциплин рассчитываем, какие из них доступны */
            $discs = $this->_getAllowed($discs, $payment_total, $cost_total);
            return $discs;
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
                ORDER BY discipline_id ASC
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
    }

?>