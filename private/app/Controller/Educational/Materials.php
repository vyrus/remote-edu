<?php

    class Controller_Educational_Materials extends Mvc_Controller_Abstract {

        public function action_edit($params) {
            $links = Resources::getInstance()->links;

            $opts = array('material_id' => $params['material_id']);
            $action = $links->get('materials.teacher.edit', $opts);
            $form = Form_Materials_Edit::create($action);

            $educationalMaterials = Model_Educational_Materials::create ();
            $this->set('form', $form);
            $request = $this->getRequest();
            $method = $form->method();
            $requestData = $request->$method;

            if (empty($requestData)) {
                if (($materialInfo = $educationalMaterials->getMaterialInfo($params['material_id'])) === FALSE) {
                    $this->flash('Учебный материал не существует или был загружен не Вами',
                                 $links->get('teacher.materials'), 5);
                }

                $form->setValue('description', $materialInfo['description']);
                $form->setValue('type', $materialInfo['type']);
            }
            else if ($form->validate($request)) {
                $materialInfo = array(
                    'id' => $params['material_id'],
                    'description' => $requestData['description'],
                    'type' => $requestData['type'],
                );
                $educationalMaterials->updateMaterialInfo($materialInfo);
                $this->flash('Данные материала были успешно изменены',
                             $links->get('teacher.materials'), 5);
            }

            $this->render('educational_materials/edit');
        }

        public function action_index () {
            $educationPrograms = Model_Education_Programs::create ();
            $this->set('directions', $educationPrograms->getDirections());
            $this->set('courses', $educationPrograms->getCourses());
            $this->set('disciplines', $educationPrograms->getDirectionsDisciplines());
            $this->set('sections', $educationPrograms->getDisciplinesSections());
            $this->set('invalidMaterialsForms', array ());
            
            $this->set('mapProgramDiscipline', $educationPrograms->getMapProgramDiscipline());
            $this->set('mapDisciplineSection', $educationPrograms->getMapDisciplineSection());
            
            /*$request = $this->getRequest ();
            $requestData = $request->post;
            $educationalMaterials = Model_Educational_Materials::create ();*/
            
            $educationMaterials=Model_Educational_Materials::create();
            $mats = $educationMaterials->getMaterialsByAdmin();
            foreach ($mats as &$ari) {
                foreach ($ari as &$ar) {
                    $ar['type_rus'] =  $educationMaterials::$MATERIAL_TYPES_CAPTIONS[$ar['type']];
                    unset($ar['type']);
                }
            }
            $this->set('materials', $mats);
            $this->set('programID', (isset ($requestData['programsSelect'])) ? ($requestData['programsSelect']) : (-1));
            $this->set('disciplineID', (isset ($requestData['disciplinesSelect'])) ? ($requestData['disciplinesSelect']) : (-1));
            $this->set('sectionID',	(isset ($requestData['sectionsSelect'])) ? ($requestData['sectionsSelect']) : (-1));
            //$this->set('materials',	$educationalMaterials->getMaterials ($requestData));
            
            $this->render('educational_materials/index_by_admin');
        }

        public function action_index_by_admin () {
            $this->action_index ();
        }

        public function action_index_by_student () {
            $this->action_index ();
        }

        public function action_remove () {
            $request = $this->getRequest ();
            $requestData = $request->post;
            $educationalMaterials = Model_Educational_Materials::create ();
            $removeSuccess = true;

            if (!empty($requestData)) {
                $ar = explode (',',$requestData["materialDeleteInfo"]);
                //print_r($ar);
                foreach ($ar as $materialID) {
                    if ($materialID != 'all') {
                        //var_dump($materialID);
                        $removeSuccess = $removeSuccess && $educationalMaterials->removeMaterial($materialID);
                    }
                }
            }

            $links = Resources::getInstance()->links;
            $this->flash(
                $removeSuccess ?
                    'Материалы успешно удалены' :
                    'Некоторые материалы не были удалены(возможно, Вы предприняли попытку удалить материал, который не был загружен Вами)',
                $links->get('teacher.materials'), 10
            );
            
        }

        public function action_upload () {
            $educationPrograms = Model_Education_Programs::create ();
            $this->set ('directions',	$educationPrograms->getDirections 				());
            $this->set ('courses',		$educationPrograms->getCourses 					());
            $this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines	());
            $this->set ('sections', 	$educationPrograms->getDisciplinesSections		());
            $this->set ('invalidMaterialsForms', array ());
            $this->set('nextDialog','materials.teacher.upload');


            $links = Resources::getInstance()->links;

            $action = $links->get('materials.teacher.upload');
            $form = Form_Materials_Upload::create ($action);

            $request        = $this->getRequest ();
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
                            'description' => $material['description'],
                            'section' => $material['section'],
                            'filename' => $request->files['fileReference' . $i]['name'],
                            'type' => $material['type'],
                        )
                    );

                    $materialForm = Form_Materials_Upload::create ('');
                    $materialForm->setMethod (Form_Abstract::METHOD_GET);
                    if (! $materialForm->validate ($request)) {
                        $invalidMaterialsForms[] = $materialForm;
                    }
                    else {
                        $educationalMaterials->addMaterial($material['description'], $material['section'], $material['type'], $request->files['fileReference' . $i]);
                    }
                }
            }

            if (! empty ($invalidMaterialsForms)) {
                $this->set('invalidMaterialsForms', $invalidMaterialsForms);
                $this->render('educational_materials/upload');
            }

            $links = Resources::getInstance()->links;

            $this->flash (
                'Все материалы успешно загружены',
                $links->get('teacher.materials'),
                3
            );
        }

        public function action_get_material ($params) {
            $educationalMaterials = Model_Educational_Materials::create ();
            $educationalMaterials->getMaterial ($params['material_id']);
        }
        
        public function action_save_order() {
            $mat = Model_Educational_Materials::create ();

            $data = explode(',', $_POST['materialOrderInfo']);

            for ($i = 0; $i < count($data); $i++) {
                $mat->editMaterialNumber (
                    $data[$i],
                    $i
                );
            }

            $links = Resources::getInstance()->links;

            $this->flash (
                'Порядок материалов успешно изменён',
                $links->get('teacher.materials'),
                3
            );
        }

        /**
        * Отображение доступных учебных материалов.
        */
        public function action_show(array $params = array()) {
            $links = Resources::getInstance()->links;

            if (!isset($params['discipline_id']) ||
                is_int ($params['discipline_id'])) {
                $this->flash(
                    'Не указан идентификатор дисциплины',
                    $links->get('student.programs')
                );
            }

            if (!isset($params['app_id']) || is_int($params['app_id'])) {
                $this->flash(
                    'Не указан идентификатор заявки',
                    $links->get('student.programs')
                );
            }

            $discipline_id = intval($params['discipline_id']);
            $app_id  = intval($params['app_id']);

            /**
            * @todo Сделать проверку на доступность дисциплины.
            */
            
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            
            $disc = Model_Discipline::create();
            $discipline_data = $disc->get($discipline_id);

            $section = Model_Section::create();
            $sections = $section->getAllByDiscipline($discipline_id);

            $material = Model_Educational_Materials::create();
            $materials = $material->getAllByDiscipline($discipline_id);

            $this->set('discipline', $discipline_data);
            $this->set('sections', $sections);
            $this->set('materials', $materials);
            $this->set('user_id', $udata->user_id);

            $this->render();
        }

    }