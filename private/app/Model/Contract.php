<?php
	class Model_Contract extends Mvc_Model_Abstract {
		private $storage;
		
		public function __construct () {
			parent::__construct ();
			
			$this->storage = new Storage ('../private/contracts');
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

/*
		public function getMaterial ($materialID) {
			$sql =
<<<QUERY
SELECT
	`original_filename`,`mime_type`,`filename`
FROM
	`materials`
WHERE
	`id`=?
QUERY;

			$stmt = $this->prepare ($sql);
			$stmt->execute (array ($materialID));
			$fileInfo = $stmt->fetchAll (PDO::FETCH_ASSOC);

			header('Content-Disposition: attachment; filename="' . $fileInfo[0]['original_filename']);
			header('Content-Type: ' . $fileInfo[0]['mime_type']);
			
			echo $this->storage->getFileContent ($fileInfo[0]['filename']);
		}*/
	}
?>