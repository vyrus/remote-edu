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
        * @param  float $amount Рзамер платежа.
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
    }

?>