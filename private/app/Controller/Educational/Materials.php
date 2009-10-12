<?php
	class Controller_Educational_Materials extends Mvc_Controller_Abstract {
		public function action_index () {
			$educationPrograms = Model_Education_Programs::create ();
			$this->set ('directions',	$educationPrograms->getDirections 				());
			$this->set ('courses', 		$educationPrograms->getCourses 					());
			$this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines 	());
			$this->set ('sections', 	$educationPrograms->getDisciplinesSections 		());			
			
			$this->render ('educational_materials/index');
		}
		
		public function action_upload () {
			print_r ($_POST);
			print_r ($_FILES);
			
			//$this->render ('educational_materials/index');
		}
	}
?>