<?php
	class Controller_Educational_Materials extends Mvc_Controller_Abstract {
		public function action_index () {
			$this->render ('educational_materials/index');
		}
		
		public function action_upload () {
			print_r ($_POST);
			print_r ($_FILES);
			
			//$this->render ('educational_materials/index');
		}
	}
?>