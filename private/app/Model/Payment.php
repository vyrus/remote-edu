<?php

    /* $Id$ */

    class Model_Payment extends Model_Base {
        /**
        * Создание экземляра класса.
        *
        * @return Model_Payment
        */
        public static function create() {
            return new self();
        }

        /**
        * Возвращает ответ на вопрос: "Оплачена ли заявка?" на основе суммирования оплат
        *
        * @param  int   $app_id Идентификатор заявки.
        * @return boolean
        */
		public function isAppPrepaid ($app_id) { 
			$app = Model_Application::create();
			$paym = Model_Payment::create();
			$a = $app->getAppInfo($app_id); 
			//print_r($a); die();
			if ($a['type'] == 'program') {
				//товарищ учится по всему направлению
				$prog = $app->getProgram($a['object_id']);
				$paid_money = $paym->getTotal($a['app_id']);
				$rest = $prog['cost'] - $paid_money; // (program price - paid already)
			} elseif  ($a['type'] == 'discipline') {
				//учится по дисциплине
				$disc = $app->getDiscipline($a['object_id']);
				$upper_prog = $app->getProgram($disc['program_id']);
				$paid_money = $paym->getTotal($a['app_id']);
				$rest = ($upper_prog['cost']*$disc['coef'])/100 - $paid_money; // (program price - paid already)
			}
			return $rest <= 0;
		}

        /**
        * Добавление нового платежа по заявке.
        *
        * @param  float $amount Размер платежа.
        * @param  int   $app_id Идентификатор заявки.
        * @return boolean
        */
        public function add($amount, $app_id) {
            $sql = '
                INSERT INTO ' . $this->_tables['payments'] . '
                (app_id, amount, created)
                VALUES
                (:app_id, :amount, NOW())
            ';

            $values = array(
                ':app_id' => $app_id,
                ':amount' => $amount
            );

            $stmt = $this->prepare($sql);
			$exRes = $stmt->execute($values);

			// типа триггер: проверка на то, что новый платеж сделает дисциплину/программу полностью оплаченной

			if ($this->isAppPrepaid($app_id)) {
				$app = Model_Application::create();
				$app->setAppStatus(Model_Application::STATUS_PREPAID, $app_id);
			}

			return $exRes;
        }

        /**
        * Получение общей суммы платежей по заявке.
        *
        * @param  int $app_id Идентификатор заявки.
        * @return float
        */
        public function getTotal($app_id) {
            $sql = '
                SELECT SUM(amount)
                FROM ' . $this->_tables['payments'] . '
                WHERE app_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($app_id));

            $total = $stmt->fetchColumn(0);
            return $total;
        }
    }

?>
