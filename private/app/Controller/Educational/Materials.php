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
                /*
                if (($materialInfo = $educationalMaterials->getMaterialInfo($params['material_id'])) === FALSE) {
                    $this->flash('Учебный материал не существует или был загружен не Вами',
                        $links->get('teacher.materials'), 5);
                }
                 */

                $materialInfo = $educationalMaterials->getMaterialInfo($params['material_id']); 
                $form->setValue('description', $materialInfo['description']);
                $form->setValue('type', $materialInfo['type']);
            }
            else if ($form->validate($request)) {
                $materialInfo = array(
                    'id' => $params['material_id'],
                    'description' => $requestData['description'],
                    'type' => $requestData['type'],
                );
                $result = $educationalMaterials->updateMaterialInfo($materialInfo);
                if ($result) {
                    $this->flash('Данные материала были успешно изменены',
                        $links->get('teacher.materials'), 5);
                } else {
                    $this->flash('Данные материала не были изменены. Возможно, Вы предприняли попытку удалить материал, который принадлежит дисциплине, за которую Вы не назначены ответственным',
                        $links->get('teacher.materials'), 5);
                }
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
                    $ar['type_rus'] =  Model_Educational_Materials::$MATERIAL_TYPES_CAPTIONS[$ar['type']];
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

            if (!array_key_exists('back',$requestData)) 
                $requestData['back'] = 'teacher.materials';

            $links = Resources::getInstance()->links;
            $this->flash(
                $removeSuccess ?
                    'Материалы успешно удалены' :
                    'Некоторые материалы не были удалены. Возможно, Вы предприняли попытку удалить материал, который  принадлежит дисциплине, за которую Вы не назначены ответственным',
                $links->get($requestData['back']), 10
            );
            
        }

        public function action_upload () {
            $result = false;

            $educationPrograms = Model_Education_Programs::create ();
            $this->set ('directions',	$educationPrograms->getDirections 				());
            $this->set ('courses',		$educationPrograms->getCourses 					());
            $this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines	());
            $this->set ('sections', 	$educationPrograms->getDisciplinesSections		());
            $this->set ('invalidMaterialsForms', array ());

            $links = Resources::getInstance()->links;

            $action = $links->get('materials.teacher.upload');
            $form = Form_Materials_Upload::create ($action);

            $request        = $this->getRequest ();
            $method 		= $form->method ();
            $requestData	= $request->$method;

            if (!array_key_exists('back', $requestData)) 
                $requestData['back'] = 'teacher.materials';
            $this->set('back',$requestData['back']);
                
            if (count($requestData) == 1) {

                $this->render ('educational_materials/upload');
            }
            
            //print_r($requestData); die();
            
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
                        $result = $educationalMaterials->addMaterial($material['description'], $material['section'], $material['type'], $request->files['fileReference' . $i]);
                    }
                }
            }

            if (! empty ($invalidMaterialsForms)) {
                $this->set('invalidMaterialsForms', $invalidMaterialsForms);
                $this->render('educational_materials/upload');
            }

            $links = Resources::getInstance()->links;

            if ($result) {
                $this->flash ('Все материалы успешно загружены', $links->get($requestData['back']), 3);
            } else {
                $this->flash ('Во время загрузке материалов произошли ошибки. Возможно Вы пытаетесь загрузить матераил в дисциплину, за которую не назначены ответственным', $links->get($requestData['back']), 3);
            }
        }

        public function action_get_material ($params) {
            $educationalMaterials = Model_Educational_Materials::create ();
            $educationalMaterials->getMaterial ($params['material_id']);
        }
        
        public function action_save_order() {
            $result = true;
            $mat = Model_Educational_Materials::create ();
            $request = $this->getRequest ();
            $requestData = $request->post;

            $data = explode(',', $requestData['materialOrderInfo']);

            for ($i = 0; $i < count($data); $i++) {
                $result = $result && $mat->editMaterialNumber (
                    $data[$i],
                    $i
                );
            }

            $links = Resources::getInstance()->links;

            if (!array_key_exists('back',$requestData)) 
                $requestData['back'] = 'teacher.materials';

            if ($result) {
                $this->flash (
                    'Порядок материалов успешно изменён',
                    $links->get($requestData['back']),
                    3
                );
            } else {
                $this->flash (
                    'Во время изменения порядка материалов произощли ошибки. Возможно Вы пытаетесь изменить порядок в  дисциплине, за которую не назначены ответственным',
                    $links->get($requestData['back']),
                    3
                );
            
            }
        }

        /**
        * Отображение доступных учебных материалов.
        */
        public function action_show(array $params = array()) {

            $a = Model_Test::create();
            $a->test();

            $links = Resources::getInstance()->links;

            if (!isset($params['discipline_id']) ||
                is_int ($params['discipline_id'])) {
                $this->flash(
                    'Не указан идентификатор дисциплины',
                    $links->get('student.programs')
                );
            }

            $discipline_id = intval($params['discipline_id']);

            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $session = Resources_Abstract::getInstance()->session;

            // если данных о доступных дисциплинах нет - вычислить
            if (!isset($session->availDisciplines)) {
                $student = Model_Education_Students::create();
                $avail_programs = $student->getAvailDisciplinesForPrograms($udata->user_id);
                $avail_disciplines = $student->getAvailDisciplinesSeparate($udata->user_id);
            }

            $discipline_open = in_array($discipline_id, $session->availDisciplines);
            
            if ($discipline_open) { // дисциплина доступна
                
                $disc = Model_Discipline::create();
                $discipline_data = $disc->get($discipline_id);

                $section = Model_Section::create();
                $sections = $section->getAllByDiscipline($discipline_id);

                $modelApps = Model_Application::create();
                $statuses = $modelApps->getAppsStatus($discipline_id, $udata->user_id); //== Model_Application::STATUS_FINISHED;
                $statusFinished = array_key_exists(Model_Application::STATUS_FINISHED, $statuses);

                $material = Model_Educational_Materials::create();
                $materials = $material->getAllByDiscipline($discipline_id, $statusFinished);

                $control_work = Model_ControlWork::create();
                $tests = $control_work->getTestsByDiscipline($discipline_id);

                $this->set('discipline', $discipline_data);
                $this->set('sections', $sections);
                $this->set('materials', $materials);
                $this->set('tests', $tests);
                $this->set('user_id', $udata->user_id);
                
                //print_r($materials);

                $this->render();
            } else {
                $this->flash(
                    'Данная дисциплина на данный Вам недоступна. Возможно Вы не полностью оплатили заявку по данной дисциплине или программе, содержащей данную дисциплину, либо вообще не подавали заявку на ее изучение.',
                    $links->get('student.programs'),
                    5
                );
            }
        }

    }
