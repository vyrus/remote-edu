<?php
	class Storage {
		private $directory;
		
		public function __construct ($directory) {
			$this->directory = $directory;
		}
		
		public function storeFile () {
			$args = func_get_args ();
			$path = array_shift ($args);
			if (count ($args)) {
				$filename = array_shift ($args);
			}
			else {
				do {
					$filename = md5 ($path . rand (0, 0xFFFFFF));
				} while (file_exists ($this->directory . '/' . $filename)); 
			}
			
			copy ($path, $this->directory . '/' . $filename);
			
			return $filename;
		}
		
		public function getFileContent ($filename) {
			return file_get_contents ($this->directory . '/' . $filename);
		}
	}
?>