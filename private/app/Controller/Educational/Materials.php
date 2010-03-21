<?php
    class Controller_Educational_Materials extends Mvc_Controller_Abstract {
        public function action_edit($params) {
            $links = Resources::getInstance()->links;

            $opts = array('material_id' => $params['material_id']);
            $action = $links->get('materials.edit', $opts);
            $form = Form_Materials_Edit::create($action);

            $educationalMaterials = Model_Educational_Materials::create ();
            $this->set('form', $form);
            $request = $this->getRequest();
            $method = $form->method();
            $requestData = $request->$method;

            if (empty($requestData)) {
                if (($materialInfo = $educationalMaterials->getMaterialInfo($params['material_id'])) === FALSE) {
                    $this->flash('Учебный материал не существует или был загружен не Вами',
                                 $links->get('admin.materials'), 5);
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
                             $links->get('admin.materials'), 5);
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

            $request = $this->getRequest ();
            $requestData = $request->post;
            $educationalMaterials = Model_Educational_Materials::create ();

            $this->set('programID', (isset ($requestData['programsSelect'])) ? ($requestData['programsSelect']) : (-1));
            $this->set('disciplineID', (isset ($requestData['disciplinesSelect'])) ? ($requestData['disciplinesSelect']) : (-1));
            $this->set('sectionID',	(isset ($requestData['sectionsSelect'])) ? ($requestData['sectionsSelect']) : (-1));
            $this->set('materials',	$educationalMaterials->getMaterials ($requestData));
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
            $removeSuccess = TRUE;

            if (!empty($requestData)) {
                foreach ($requestData as $materialID => $value) {
                    if ($materialID != 'all') {
                        $removeSuccess = $removeSuccess && $educationalMaterials->removeMaterial($materialID);
                    }
                }
            }

            $links = Resources::getInstance()->links;

            $this->flash(
                $removeSuccess ?
                    'Материалы успешно удалены' :
                    'Некоторые материалы не были удалены(возможно, Вы предприняли попытку удалить материал, который не был загружен Вами)',
                $links->get('admin.materials'), 10
            );
        }

        public function action_upload () {
            $educationPrograms = Model_Education_Programs::create ();
            $this->set ('directions',	$educationPrograms->getDirections 				());
            $this->set ('courses',		$educationPrograms->getCourses 					());
            $this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines	());
            $this->set ('sections', 	$educationPrograms->getDisciplinesSections		());
            $this->set ('invalidMaterialsForms', array ());


            $links = Resources::getInstance()->links;

            $action = $links->get('materials.upload');
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
                $links->get('admin.materials'),
                3
            );
        }

        public function action_get_material ($params) {
            $educationalMaterials = Model_Educational_Materials::create ();
            $educationalMaterials->getMaterial ($params['material_id']);
        }

        /**
        * Отображение доступных материалов.
        */
        public function action_show(array $params = array()) {
            $links = Resources::getInstance()->links;

            if (!isset($params['discipline_id']) ||
                is_int ($params['discipline_id']))
            {
                $this->flash('Не указан идентификатор дисциплины',
                             $links->get('student.programs'));
            }

            if (!isset($params['app_id']) || is_int($params['app_id']))
            {
                $this->flash('Не указан идентификатор заявки',
                             $links->get('student.programs'));
            }

            $disc_id = intval($params['discipline_id']);
            $app_id  = intval($params['app_id']);

            /**
            * @todo Сделать проверку на доступность дисциплины.
            */

            $disc = Model_Discipline::create();
            $disc_data = $disc->get($disc_id);

            $section = Model_Section::create();
            $sections = $section->getAllByDiscipline($disc_id);

            $section_ids = array();

            foreach ($sections as $section) {
                $section_ids[] = $section['section_id'];
            }

            $material = Model_Educational_Materials::create();
            $materials = $material->getAllByDiscipline($disc_id);

            $this->set('discipline', $disc_data);
            $this->set('sections', $sections);
            $this->set('materials', $materials);

            $this->render();
        }
    }

?>