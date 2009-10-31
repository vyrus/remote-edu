<?php
    
    /* $Id$ */

    class Controller_Applications extends Mvc_Controller_Abstract {
        const RETURN_URL = '/applications/index/';
        
        /**
        * форма для подачи заявки слушателем.
        */
        public function action_index_by_student()
        {
			$educationPrograms = Model_Education_Programs::create ();
			$this->set ('directions',	$educationPrograms->getDirections 				());
			$this->set ('courses', 		$educationPrograms->getCourses 					());
			$this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines 	());

            /* Создаём объект формы с полями первичной регистрации */
            $action = '/applications/apply/';
            $form = Form_Profile_Student_Registration::create($action);
            $this->set('form', $form);
			
            $this->render();
        }

        /**
        * Список поданных заявок для админа.
        */
        public function action_index_by_admin()
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            /**
            * @todo Paginator.
            */
            $app = Model_Application::create();
            $apps = $app->getAllAppsInfo();
            
            $this->set('applications', $apps);
            $this->set('statuses', Model_Application::getStatusMap());
			$this->set ('invalidMaterialsForms', array ());
			
    		$contract = Model_Contract::create ();
            
			$request	= $this->getRequest ();
			$requestData	= $request->files;
			if (empty ($requestData)) {
				$this->render ('/applications/index_by_admin');
			}
                                                                                                    // [ загрузка договора
			$invalidMaterialsForms = array ();

            foreach ($request->files as $key=>$item)
            {
                list($empty_str,$app_id) = (split('fileReference',$key));
            }

            $request->set (
                'get',
                array (
                    'filename'		=> $request->files['fileReference' . $app_id]['name'],
                )
            );

            $contractForm = Form_Contract_Upload::create ('');
            $contractForm->setMethod (Form_Abstract::METHOD_GET);
            if (! $contractForm->validate ($request)) {
                $invalidMaterialsForms[] = $contractForm;
            }
            else {
                $contract->addContract ($request->files['fileReference' . $app_id],$app_id);
            }
			
			if (! empty ($invalidMaterialsForms)) {
				$this->set		('invalidMaterialsForms', $invalidMaterialsForms);
				$this->render	('educational_materials/upload');
			}

			$this->flash (
				'Договор успешно загружен',
				'/applications/index_by_admin',
				3
			);
        }
        
        /**
        * Подача заявки на программу/дисциплину.
        */
        public function action_apply($params)
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            
            /**
            * @todo Раскоментировать когда поправится подключение js-функций,
            * подгружающих списки городов/областей.
            */
            /*
            if (!$user->isExtendedProfileSet($udata->user_id))
            {
                $msg = 'Заполните, пожалуйста, свой профиль';
                $link = '/users/profile_extended_by_student/';
                $this->flash($msg, $link);
            }
            */
            
            /**
            * Получение данных от браузера.
            */
            $object_id = $params['program_id'];
            $type = $params['program_type'];
            
            $app = Model_Application::create();       
            $app->apply($udata->user_id, $object_id, $type);
            
            $this->flash('Заявка подана', '/applications/list_by_student/');
        }
        
        /**
        * Просмотр статуса заявок слушателем.
        */
        public function action_list_by_student()
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            /**
            * @todo Paginator.
            */
            $app = Model_Application::create();
            $apps = $app->getAppsInfo($udata->user_id);
            
            $this->set('applications', $apps);
            $this->set('statuses', Model_Application::getStatusMap());
            
            $this->render();
        }
        
        public function action_change_app_status($params)
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $new_status = $params['new_status'];
            $app_id = $params['app_id'];
            
            $app = Model_Application::create();       
            $app->setAppStatus($new_status, $app_id);
            
            $map = Model_Application::getStatusMap();
            $this->flash('Заявка ' . $map[$new_status], '/applications/index_by_admin/');
          
            $this->render();            
        }
        
		public function action_upload_agreement ()
        {
			$app = Model_Application::create();       
			
			$request	= $this->getRequest ();
			$form 		= Form_Materials_Upload::create ('/applications/upload_agreement');
			
			$method 		= $form->method ();
			$requestData	= $request->$method;
			if (empty ($requestData)) {
				$this->render ('/applications/upload_agreement');
			}

			$invalidMaterialsForms = array ();
			if (count ($requestData['material'])) {
				$educationalMaterials = Model_Educational_Materials::create ();
				
				foreach ($requestData['material'] as $i => $material) {					
					$request->set (
						'get',
						array (
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
        
    }
    
?>