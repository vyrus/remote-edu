<?php
	class Model_Educational_Materials extends Mvc_Model_Abstract {
		private $storage;
		
		public function __construct () {
			parent::__construct ();
			
			$this->storage = new Storage ('../private/materials');
		}
		
		public static function create() {
            return new self();
        }

		public function addMaterial ($description, $section, $originalFileInfo) {
			$filename = $this->storage->storeFile ($originalFileInfo['tmp_name']);
			
			$sql =
<<<QUERY
INSERT INTO `materials`(`description`,`original_filename`,`mime_type`,`filename`,`section`)
VALUES (:description,:original_filename,:mime_type,:filename,:section)
QUERY;
			$params = array (
				'description'		=> $description,
				'original_filename'	=> $originalFileInfo['name'],
				'mime_type'			=> $originalFileInfo['type'],
				'filename'			=> $filename,
				'section'			=> $section,
			);
			
			$this->prepare ($sql)
				->execute ($params);
		}
		
		public function removeMaterial ($materialID) {
			$sql =
<<<QUERY
SELECT `filename`
FROM `materials`
WHERE `id`=?
QUERY;
			$stmt = $this->prepare ($sql);
			$stmt->execute (array ($materialID));
			$filename = $stmt->fetchAll (PDO::FETCH_ASSOC);
			
			$this->storage->removeFile ($filename[0]['filename']);
			
			$sql =
<<<QUERY
DELETE FROM `materials`
WHERE `id`=?
QUERY;
			$this->prepare ($sql)
				->execute (array ($materialID));
		}
		
		public function getMaterials ($filter) {
			$sql =
<<<QUERY
SELECT
	`materials`.`id` as `id`,`materials`.`description` as `description`
FROM
	`materials`
QUERY;

			
			do {
				if ((empty ($filter)) || ($filter['programsSelect'] == -1)) {
					$tables			= '';
					$condition		= '';
					$queryParams	= array ();
					
					break;
				}
				
				if ($filter['disciplinesSelect'] == -1) {
					$tables 		= ',`disciplines`,`sections`';
					$condition		= '`materials`.`section`=`sections`.`section_id` AND `sections`.`discipline_id`=`disciplines`.`discipline_id` AND `disciplines`.`program_id`=?'; 
					$queryParams	= array ($filter['programsSelect']);
					
					break;
				}
				
				if ($filter['sectionsSelect'] == -1) {
					$tables 		= ',`sections`';
					$condition		= '`materials`.`section`=`sections`.`section_id` AND `sections`.`discipline_id`=?';
					$queryParams	= array ($filter['disciplinesSelect']);
					
					break;
				}

				$tables 		= '';
				$condition		= '`materials`.`section`=?';
				$queryParams	= array ($filter['sectionsSelect']);
			} while (0);

			$sql .= $tables . (($condition != '') ? (' WHERE ' . $condition) : (''));

			$stmt = $this->prepare ($sql);
			$stmt->execute ($queryParams);

			return $stmt->fetchAll (PDO::FETCH_ASSOC);
		}
		
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
		}
	}
?>