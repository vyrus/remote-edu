<?php

    class Controller_Education_Programs extends Mvc_Controller_Abstract {

        public function action_index () {
            $educationPrograms = Model_Education_Programs::create();
            $this->set('directions', $educationPrograms->getDirections());
            $this->set('courses', $educationPrograms->getCourses());
            $this->set('disciplines', $educationPrograms->getDirectionsDisciplines());
            $this->set('sections', $educationPrograms->getDisciplinesSections());

            //$this->set('mapsPDS', $educationPrograms->getMapsPDS());
            
            $educationMaterials=Model_Educational_Materials::create();
            $mats = $educationMaterials->getMaterialsByAdmin();
            foreach ($mats as &$ari) {
                foreach ($ari as &$ar) {
                    $ar['type_rus'] =  Model_Educational_Materials::$MATERIAL_TYPES_CAPTIONS[$ar['type']];
                    unset($ar['type']);
                }
            }
            $this->set('materials', $mats);
            
            $this->render("education_programs/index");
        }

        public function action_add_program ($params) {
            $links = Resources::getInstance()->links;

            $this->set ('buttonCaption', 'Добавить');
            $this->set ('programType', $params['program_type']);

            $request = $this->getRequest ();
            $request->set ('program_type', $params['program_type']);

            $opts = array('program_type' => $params['program_type']);
            $action = $links->get('programs.add', $opts);

            $form = Form_Program_Add::create($action);
            $this->set ('form', $form);
            $method = $form->method ();
            if (empty ($request->$method)) {
                $this->render ("education_programs/program_form");
            }

            $educationPrograms = Model_Education_Programs::create ();

            if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/program_form");
            }

            $educationPrograms->createProgram (
                $form->title->value,
                $form->labourIntensive->value,
                $params['program_type'],
                $form->paidType->value,
                $form->cost->value
            );
            $this->flash (
                (
                    ($params['program_type'] == 'direction') ?
                    ('Направление успешно добавлено') :
                    ('Курсы успешно добавлены')
                ),
                $links->get('admin.programs'),
                3
            );
        }

        public function action_add_discipline ($params) {
            $links = Resources::getInstance()->links;

            $this->set ('buttonCaption', 'Добавить');

            $request = $this->getRequest ();
            $request->set ('speciality', $params['speciality_id']);

            $opts = array('speciality_id' => $params['speciality_id']);
            $action = $links->get('disciplines.add', $opts);

            $form = Form_Discipline_Add::create ($action);
            $this->set ('form', $form);
            $method = $form->method ();
            $form->setValue ('speciality', $params['speciality_id']);

            if (empty ($request->$method)) {
                $this->render ('education_programs/discipline_form');
            }

            $educationPrograms = Model_Education_Programs::create ();

            if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/discipline_form");
            }

            $disciplines = $educationPrograms->getDirectionsDisciplines();
            $educationPrograms->createDiscipline (
                $form->speciality->value,
                $form->title->value,
                $form->coef->value,
                $form->labourIntensive->value,
                isset($disciplines[$form->speciality->value]) ? count($disciplines[$form->speciality->value]) : 0
            );
            $this->flash (
                'Дисциплина успешно добавлена',
                $links->get('admin.programs'),
                3
            );
        }

        public function action_add_section ($params) {
            $links = Resources::getInstance()->links;

            $request = $this->getRequest ();
            $request->set('discipline', $params['discipline_id']);

            $opts = array('discipline_id' => $params['discipline_id']);
            $action = $links->get('sections.add', $opts);

            $form = Form_Section_Add::create ($action);
            $this->set('form', $form);
            $method = $form->method ();

            if (empty($request->$method)) {
                $this->render('education_programs/section_form');
            }

            $educationPrograms = Model_Education_Programs::create();

            if (! $form->validate($request, $educationPrograms)) {
                $this->render("education_programs/section_form");
            }

            $educationPrograms->createSection(
                $form->discipline->value,
                $form->title->value,
                $form->number->value
            );
            $this->flash(
                'Раздел успешно добавлен',
                $links->get('admin.programs'),
                3
            );
        }

        public function action_remove_program ($params) {
            $educationPrograms = Model_Education_Programs::create ();
            $educationPrograms->removeProgram ($params['program_id'], $params['program_type']);

            $links = Resources::getInstance()->links;

            $this->flash (
                (
                    ($params['program_type'] == 'direction') ?
                    ('Направление успешно удалено') :
                    ('Курсы успешно удалены')
                ),
                $links->get('admin.programs'),
                3
            );
        }

        public function action_remove_discipline ($params) {
            $educationPrograms = Model_Education_Programs::create ();
            $educationPrograms->removeDiscipline ($params['discipline_id']);

            $links = Resources::getInstance()->links;

            $this->flash (
                'Дисциплина успешно удалена',
                $links->get('admin.programs'),
                3
            );
        }

        public function action_remove_section ($params) {
            $educationPrograms = Model_Education_Programs::create ();
            $educationPrograms->removeSection ($params['section_id']);

            $links = Resources::getInstance()->links;

            $this->flash (
                'Раздел успешно удален',
                $links->get('admin.programs'),
                3
            );
        }

        public function action_edit_program ($params) {
            $links = Resources::getInstance()->links;

            $this->set ('buttonCaption', 'Сохранить');
            $this->set ('programType', $params['program_type']);

            $request = $this->getRequest ();
            $request->set ('program', $params['program_id']);
            $request->set ('program_type', $params['program_type']);

            $opts = array('program_type' => $params['program_type'],
                          'program_id'   => $params['program_id']);
            $action = $links->get('programs.edit', $opts);

            $form = Form_Program_Edit::create($action);
            $this->set ('form', $form);
            $method = $form->method ();

            $educationPrograms = Model_Education_Programs::create ();

            if (empty ($request->$method)) {
                if (! $form->validateID ($educationPrograms, $request)) {
                    $this->render ('education_programs/program_form');
                }

                $educationPrograms->getProgram ($params['program_id'], $params['program_type'], $title, $labourIntensive, $paidType, $cost);

                $form->setValue ('title', $title);
                $form->setValue ('labourIntensive', $labourIntensive);
                $form->setValue ('paidType', $paidType);
                $form->setValue ('cost', $cost);

                $this->render ('education_programs/program_form');
            }

            if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/program_form");
            }

            $educationPrograms->editProgram (
                $params['program_id'],
                $params['program_type'],
                $form->title->value,
                $form->labourIntensive->value,
                $form->paidType->value,
                $form->cost->value
            );

            $this->flash (
                'Данные по ' . (($params['program_type'] == 'direction') ? ('направлению') : ('курсам')) . ' успешно изменены',
                $links->get('admin.programs'),
                3
            );
        }

        public function action_edit_discipline ($params) {
            $links = Resources::getInstance()->links;

            $this->set ('buttonCaption', 'Сохранить');

            $request = $this->getRequest ();
            $request->set ('discipline', $params['discipline_id']);

            $opts = array('discipline_id' => $params['discipline_id']);
            $action = $links->get('disciplines.edit', $opts);

            $form = Form_Discipline_Edit::create ($action);
            $this->set ('form', $form);
            $method = $form->method ();

            $educationPrograms = Model_Education_Programs::create ();

            if (empty ($request->$method)) {
                if (! $form->validateID ($educationPrograms, $request)) {
                    $this->render ('education_programs/discipline_form');
                }

                $educationPrograms->getDiscipline ($params['discipline_id'], $title, $labourIntensive, $coef);

                $form->setValue ('title', $title);
                $form->setValue ('labourIntensive', $labourIntensive);
                $form->setValue ('coef', $coef);

                $this->render ('education_programs/discipline_form');
            }

            if (! $form->validate ($request, $educationPrograms)) {
                $this->render ('education_programs/discipline_form');
            }

            $educationPrograms->editDiscipline (
                $params['discipline_id'],
                $form->title->value,
                $form->labourIntensive->value,
                $form->coef->value
            );

            $this->flash (
                'Данные по дисциплине успешно изменены',
                $links->get('admin.programs'),
                3
            );
        }

        public function action_edit_section ($params) {
            $links = Resources::getInstance()->links;

            $request = $this->getRequest ();
            $request->set('section', $params['section_id']);

            $opts = array('section_id' => $params['section_id']);
            $action = $links->get('sections.edit', $opts);

            $form = Form_Section_Edit::create($action);
            $this->set('form', $form);
            $method = $form->method ();

            $educationPrograms = Model_Education_Programs::create ();

            if (empty($request->$method)) {
                if (!$form->validateID($educationPrograms, $request)) {
                    $this->render('education_programs/section_form');
                }

                $educationPrograms->getSection($params['section_id'], $title, $number);

                $form->setValue('title', $title);
                //$form->setValue('number', $number);

                $checkpoint_model = Model_Checkpoint::create();
                $checkpoint = $checkpoint_model->getCheckpoint($params['section_id']);
                $action = $links->get('checkpoint.edit');
                $form_checkpoint = Form_Checkpoint_Edit::create($action);
                $form_checkpoint->setValue('active', $checkpoint['active']);
                $form_checkpoint->setValue('title', $checkpoint['title']);
                $form_checkpoint->setValue('text', $checkpoint['text']);
                $form_checkpoint->setValue('type', $checkpoint['type']);
                $form_checkpoint->setValue('test_id', $checkpoint['test_id']);

                if ('test' == $checkpoint['type']) {
                    $test = Model_Test::create();
                    $tdata = $test->get($checkpoint['test_id']);
                    $this->set('test_theme', $tdata['theme']);
                }

                $this->set('form_checkpoint', $form_checkpoint);
                $this->set('section_id', $params['section_id']);

                $educationalMaterials = Model_Educational_Materials::create();
                $this->set('materials', $educationalMaterials->getMaterials(array()));

                $this->render('education_programs/section_form');
            }

            if (!$form->validate($request, $educationPrograms)) {
                $this->render('education_programs/section_form');
            }

            $educationPrograms->editSection (
                $params['section_id'],
                $form->title->value //,
                //$form->number->value
            );

            $this->flash (
                'Данные по разделу успешно изменены',
                $links->get('admin.programs'),
                3
            );
        }
        
        /**
         * Сохранение порядка направлений/курсов
        */
        public function action_save_program_order() {
            $educationPrograms = Model_Education_Programs::create();

            $data = explode(',', $_POST['programOrderInfo']);

            for ($i = 0; $i < count($data); $i++) {
                $educationPrograms->editProgramNumber (
                    $data[$i],
                    $i
                );
            }

            $links = Resources::getInstance()->links;
            
            $this->flash (  
                'Порядок направлений/курсов успешно изменён',
                $links->get('admin.programs'),
                3   // откуда взялась эта волшебная цифра?
            );
        }
        
        /**
        * Сохранение порядка дисциплин в направлении.
        */
        public function action_save_discipline_order() {
            $educationPrograms = Model_Education_Programs::create ();

            $data = explode(',', $_POST['disciplineOrderInfo']);

            for ($i = 0; $i < count($data); $i++) {
                $educationPrograms->editDisciplineSerialNumber (
                    $data[$i],
                    $i
                );
            }

            $links = Resources::getInstance()->links;

            $this->flash (
                'Порядок дисциплин успешно изменён',
                $links->get('admin.programs'),
                3
            );
        }
        
        /**
         * Сохранение порядка разделов(секций)
        */
        public function action_save_section_order() {
            $educationPrograms = Model_Education_Programs::create();

            $data = explode(',', $_POST['sectionOrderInfo']);

            for ($i = 0; $i < count($data); $i++) {
                $educationPrograms->editSectionNumber (
                    $data[$i],
                    $i+1 // нумерация разделов с единицы
                );
            }

            $links = Resources::getInstance()->links;
            
            $this->flash (  
                'Порядок разделов успешно изменён',
                $links->get('admin.programs'),
                3   // откуда взялась эта волшебная цифра?
            );
        }

        /**
        * Отображение доступных для слушателя программ и дисциплин.
        */
        public function action_available() {
            /* Получаем данные слушателя */
            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $app     = Model_Application::create();
            $program = Model_Education_Programs::create();
            $disc    = Model_Discipline::create();
            $payment = Model_Payment::create();

            /* Список доступных направлений и их доступных дисциплин */
            $avail_programs = array();
            /* Список недоступных направлений и их дисциплин
            (не оплачены) */
            $not_avail_programs = array();
            
            /* Список доступных дисциплин (которые покупались отдельно от
            программ) */
            $avail_disciplines = array();
            /* Список недоступных дисциплин (которые покупались отдельно от
            программ и неоплачены) */
            $not_avail_disciplines = array();
            
            /**
            * @todo This needs some serious refactoring, though...
            */

            /* Получаем список заявок на образовательные программы */
            $program_apps = $app->getProcessedAppsForPrograms($udata->user_id);

            /* Перебираем его */
            foreach ($program_apps as $a)
            {
                //$a = (object) $a;

                if
                (
                    /* Если программа бесплатная */
                    Model_Education_Programs::PAID_TYPE_FREE == $a['paid_type'] &&
                    /* и заявка принята администратором, */
                    (Model_Application::STATUS_ACCEPTED == $a['status'] ||
                     Model_Application::STATUS_SIGNED == $a['status'])
                    ||
                    /* Или если программа платная */
                    Model_Education_Programs::PAID_TYPE_PAID == $a['paid_type'] &&
                    /* и договор по заявке подписан, */
                    Model_Application::STATUS_SIGNED == $a['status']
                )
                {
                    /* То получаем список доступных дисциплин */
                    /*$discs = $disc->getAllowed($a['object_id'],
                                               $a['paid_type'],
                                               $a['app_id']);*/
                    
                    $a['disciplines'] = $disc->getDisciplines($a['object_id'],
                                                              $a['paid_type'],
                                                              $a['cost'],
                                                              $a['total_sum']);
                    /* Получаем информацию о программе */
                    //$program_data = $program->getProgramInfo($a->object_id);
                    /* А также сколько за нее заплатили */
                    //$program_data['total_sum'] = $payment->getTotal($a->app_id);
                    /* Добавляем доступные дисциплины */
                    //$program_data['disciplines'] = $discs;
                    //$a['disciplines'] = $discs;
                    /* Добавляем номер заявки */
                    //$program_data['app_id'] = $a->app_id;

                    /* И вносим программу в список доступных */
                    $avail_programs[] = $a;
                }
            }

            /* Получаем список заявок на отдельные дисциплины */
            $disc_app = $app->getProcessedAppsForDisciplines($udata->user_id);

            /* Перебираем его */
            foreach ($disc_app as $a)
            {
                $a['cost'] = ((null === $a['cost']) ? 0 : $a['cost']);
                $a['total_sum'] = ((null === $a['total_sum']) ? 0 : $a['total_sum']);
                $a['disc_sum'] = ($a['cost'] / 100) * $a['coef'];
                
                /* Если программа, которой принадлежит дисциплина, платная */
                if (Model_Education_Programs::PAID_TYPE_PAID == $a['paid_type'])
                {
                    /* и договор по заявке ещё не подписан, */
                    if (Model_Application::STATUS_SIGNED !== $a['status'])
                    {
                        /* то переходим к следующей заявке */
                        continue;
                    }
                    
                    $active = (($a['disc_sum'] - $a['total_sum'] <= 0) ? true : false);
                }
                /* Если же программа бесплатная */
                elseif (Model_Education_Programs::PAID_TYPE_FREE == $a['paid_type'])
                {
                    /* и администратор ещё не принял заявку, */
                    if (Model_Application::STATUS_ACCEPTED !== $a['status'])
                    {
                        /* то переходим к следующей заявке */
                        continue;
                    }
                    $active = true;
                }
                
                /* Получаем данные дисциплины */
                //$program->getDiscipline($a->object_id, $title, $_, $_, $_);
                
                /* И заносим её в список доступных */
                $disc = array(
                    'discipline_id' => $a['object_id'],
                    'title'         => $a['title'],
                    'app_id'        => $a['app_id'],
                    'disc_sum'      => $a['disc_sum'],
                    'total_sum'     => $a['total_sum'],
                    'active'        => $active
                );
                $avail_disciplines[] = $disc;
            }

            $this->set('programs',    $avail_programs);
            $this->set('disciplines', $avail_disciplines);

            $this->render();
        }
        
    /*
     Блок работы с материалами
    */
        public function action_edit_material($params) {
            $links = Resources::getInstance()->links;

            $opts = array('material_id' => $params['material_id']);
            $action = $links->get('materials.admin.edit', $opts);
            $form = Form_Materials_Edit::create($action);

            $educationalMaterials = Model_Educational_Materials::create ();
            $this->set('form', $form);
            $request = $this->getRequest();
            $method = $form->method();
            $requestData = $request->$method;

            if (empty($requestData)) {
                if (($materialInfo = $educationalMaterials->getMaterialInfo($params['material_id'])) === FALSE) {
                    $this->flash('Учебный материал не существует или был загружен не Вами',
                                 //$links->get('admin.materials'),
                                 $links->get('admin.programs'),
                                 5
                            );
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
                             //$links->get('admin.materials'),
                             $links->get('admin.programs'),
                             5
                        );
            }

            $this->render('educational_materials/edit');
            //$this->render('education_materials/edit');
        }
        
        public function action_save_material_order() {
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
                $links->get('admin.programs'),
                3
            );
        }

        // что это еще за функции???
        // видимо, после объединения интерфейсов, они становятся ненужными

        public function action_index_material () {
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
            //$this->set('materials',	$educationalMaterials->getMaterials ($requestData));
            
            $educationMaterials=Model_Educational_Materials::create();
            $mats = $educationMaterials->getMaterialsByAdmin();
            foreach ($mats as &$ari) {
                foreach ($ari as &$ar) {
                    $ar['type_rus'] =  Model_Educational_Materials::$MATERIAL_TYPES_CAPTIONS[$ar['type']];
                    unset($ar['type']);
                }
            }
            $this->set('materials', $mats);
            
            $this->render('educational_materials/index_by_admin');
            //$this->render('education_materials/index_by_admin');
        }

        // что это еще за функции???
    
        public function action_index_by_admin_material () {
            $this->action_index_material ();
        }

        public function action_index_by_student_material () {
            $this->action_index_material ();
        }

        /*
        public function action_remove_material () {
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
                //$links->get('admin.materials'),
                $links->get('admin.programs'),
                10
            );
        }
        */
        
        public function action_remove_material () {
            $request = $this->getRequest ();
            $requestData = $request->post;
            $educationalMaterials = Model_Educational_Materials::create ();
            $removeSuccess = TRUE;
            
            //var_dump($requestData["materialDeleteInfo"]);

            if (!empty($requestData)) {
                $ar = explode (',',$requestData["materialDeleteInfo"]);
                //var_dump($ar);
                foreach ($ar as $materialID => $value) {
                    if ($materialID != 'all') {
                        //var_dump($value);
                        $removeSuccess = $removeSuccess && $educationalMaterials->removeMaterial($value);
                    }
                }
            }

            $links = Resources::getInstance()->links;

            $this->flash(
                $removeSuccess ?
                    'Материалы успешно удалены' :
                    'Некоторые материалы не были удалены(возможно, Вы предприняли попытку удалить материал, который не был загружен Вами)',
                //$links->get('admin.materials'),
                $links->get('admin.programs'),
                10
            );
        }

        public function action_upload_material () {
            $educationPrograms = Model_Education_Programs::create ();
            $this->set ('directions',	$educationPrograms->getDirections());
            $this->set ('courses',	$educationPrograms->getCourses());
            $this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines());
            $this->set ('sections', 	$educationPrograms->getDisciplinesSections());
            $this->set ('invalidMaterialsForms', array ());
            $this->set('nextDialog','materials.admin.upload');


            $links = Resources::getInstance()->links;

            $action = $links->get('materials.admin.upload');
            $form = Form_Materials_Upload::create ($action);

            $request        = $this->getRequest ();
            $method 		= $form->method ();
            $requestData	= $request->$method;
            if (empty ($requestData)) {
                //$this->render ('educational_materials/upload');
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
                //$links->get('admin.materials'),
                $links->get('admin.programs'),
                3
            );
        }

        // так, при условии, что скачиваем один материал за один щелчок
        public function action_get_material ($params) {
            $educationalMaterials = Model_Educational_Materials::create ();
            $educationalMaterials->getMaterial ($params['material_id']);
        }

        /**
        * Отображение доступных учебных материалов.
        */
        public function action_show_material(array $params = array()) {
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