<?php
	class Controller_Educational_Materials extends Mvc_Controller_Abstract {
		private $templatesPostfix = '';
		
		public function __construct (Http_Request $request) {
			$user = Model_User::create();
			$udata = (object) $user->getAuth();
			
            if (isset($udata->role)) {
				if (Model_User::ROLE_TEACHER == $udata->role) {
					$this->templatePostfix = '_by_teacher';
				}
				elseif (Model_User::ROLE_ADMIN == $udata->role) {
					$this->templatePostfix = '_by_admin';
				}
				elseif (Model_User::ROLE_STUDENT == $udata->role) {
					$this->templatePostfix = '_by_student';
				}
            }

			parent::__construct ($request);
		}
				
		public function action_index () {			
			$educationPrograms = Model_Education_Programs::create ();
			$this->set ('directions',	$educationPrograms->getDirections 				());
			$this->set ('courses', 		$educationPrograms->getCourses 					());
			$this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines 	());
			$this->set ('sections', 	$educationPrograms->getDisciplinesSections 		());
			$this->set ('invalidMaterialsForms', array ());

			$request				= $this->getRequest ();
			$requestData			= $request->post;
			$educationalMaterials	= Model_Educational_Materials::create ();

			$this->set		('programID', 		(isset ($requestData['programsSelect'])) ? ($requestData['programsSelect']) : (-1));
			$this->set		('disciplineID',	(isset ($requestData['disciplinesSelect'])) ? ($requestData['disciplinesSelect']) : (-1));
			$this->set		('sectionID',		(isset ($requestData['sectionsSelect'])) ? ($requestData['sectionsSelect']) : (-1));			
			$this->set		('materials',		$educationalMaterials->getMaterials ($requestData));
			$this->render	('educational_materials/index' . $this->templatePostfix);
		}
		
		public function action_index_by_admin () {
			$this->action_index ();
		}

		public function action_index_by_teacher () {			
            $msg = 'Тут будут учебные материалы, добавленные залогиненным преподавателем';
            $this->flash($msg, '/educational_materials/index_by_teacher/');

            $this->render();
		}

		public function action_index_by_student () {
			/*		
            $msg = 'Тут будут учебные материалы, доступные для слушателя';
            $this->flash($msg, '/educational_materials/index_by_student/');

            $this->render();
			*/
			
			$this->action_index ();
		}

		public function action_remove () {
			$request		= $this->getRequest ();
			$requestData	= $request->post;

			$educationalMaterials	= Model_Educational_Materials::create ();
			if (! empty ($requestData)) {
				foreach ($requestData as $materialID => $value) {
					if ($materialID != 'all') {
						$educationalMaterials->removeMaterial ($materialID);
					}
				}
			}
			
			$this->flash (
				'Материалы успешно удалены',
				'/educational_materials/index' . $this->templatesPostfix,
				3
			);			
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
				$educationalMaterials = Model_Educational_Materials::create ();
				
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
					else {
						$educationalMaterials->addMaterial ($material['description'], $material['section'], $request->files['fileReference' . $i]);
					}
				}
			}

			if (! empty ($invalidMaterialsForms)) {
				$this->set		('invalidMaterialsForms', $invalidMaterialsForms);
				$this->render	('educational_materials/upload');
			}

			$this->flash (
				'Все материалы успешно загружены',
				'/educational_materials/index' . $this->templatesPostfix,
				3
			);
		}
		
		public function action_get_material ($params) {
			$educationalMaterials = Model_Educational_Materials::create ();
			$educationalMaterials->getMaterial ($params['material_id']);
		}
	}
?>