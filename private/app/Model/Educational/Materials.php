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
			
			$this
				->prepare ($sql)
				->execute ($params);
		}
	}
?>