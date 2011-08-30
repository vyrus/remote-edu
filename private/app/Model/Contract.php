<?php
    class Model_Contract extends Mvc_Model_Abstract
    {
        private $storage;

        public function __construct () {
            parent::__construct ();

            //$this->storage = new Storage ('../private/contracts');
            $this->storage = Resources::getInstance()->contracts_storage;
        }

        public static function create() {
            return new self();
        }

        public function addContract ($originalFileInfo,$appId)
        {
            $filename = $this->storage->storeFile ($originalFileInfo['tmp_name']);

            $sql = '
                UPDATE applications a
                SET contract_filename = :new_contract_filename
                WHERE app_id = :app_id
            ';

            $values = array(
                ':app_id'     			 => $appId,
                ':new_contract_filename' => $filename
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            $row_count = $stmt->rowCount();
            return $row_count > 0;
        }
    }
?>
