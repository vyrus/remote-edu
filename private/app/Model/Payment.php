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

            return $stmt->execute($values);
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