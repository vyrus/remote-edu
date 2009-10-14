<?php
	class Controller_Educational_Materials extends Mvc_Controller_Abstract {
		public function action_index () {			
			$this->render ('educational_materials/index');
		}
		
		public function action_upload () {
			$educationPrograms = Model_Education_Programs::create ();
			$this->set ('directions',	$educationPrograms->getDirections 				());
			$this->set ('courses', 		$educationPrograms->getCourses 					());
			$this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines 	());
			$this->set ('sections', 	$educationPrograms->getDisciplinesSections 		());
			$this->set ('invalidMaterialsForms', array ());			
			
			$request	= $this->getRequest ();
			$form 		= Form_Materials_Upload::create ('/educational_materials/upload');
			
			$method 		= $form->method ();
			$requestData	= $request->$method;
			if (empty ($requestData)) {
				$this->render ('educational_materials/upload');
			}

			$invalidMaterialsForms = array ();
			if (count ($requestData['material'])) {
				foreach ($requestData['material'] as $i => $material) {
					$request->set (
						'get',
						array (
							'description'	=> $material['description'],
							'section'		=> $material['section'],
							'filename'		=> $request->files['fileReference' . $i]['name'],
						)
					);

					$materialForm = Form_Materials_Upload::create ('');
					$materialForm->setMethod (Form_Abstract::METHOD_GET);
					if (! $materialForm->validate ($request)) {
						$invalidMaterialsForms[] = $materialForm;
					}
				}
			}
			if (! empty ($invalidMaterialsForms)) {
				$this->set		('invalidMaterialsForms', $invalidMaterialsForms);
				$this->render	('educational_materials/upload');
			}

			$this->flash (
				'Все материалы успешно загружены',
				'/educational_materials/index',
				3
			);
		}
	}
?>