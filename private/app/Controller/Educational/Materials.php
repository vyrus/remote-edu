<?php
    class Controller_Educational_Materials extends Mvc_Controller_Abstract {
        private $templatesPostfix = '';

        public function __construct (Http_Request $request) {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            if (isset($udata->role)) {
                if (Model_User::ROLE_TEACHER == $udata->role) {
                    $this->templatePostfix = '_by_admin';
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

        public function action_edit($params) {
            $form = Form_Materials_Edit::create('/educational_materials/edit/' . $params['material_id']); 
            $educationalMaterials = Model_Educational_Materials::create ();
            $this->set('form', $form);            
            $request = $this->getRequest();            
            $method = $form->method();
            $requestData = $request->$method;
            
            if (empty($requestData)) {                
                if (($materialInfo = $educationalMaterials->getMaterialInfo($params['material_id'])) === FALSE) {
                    $this->flash('Учебный материал не существует или был загружен не Вами', '/educational_materials/index', 5);
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
                $this->flash('Данные материала были успешно изменены', '/educational_materials/index', 5);
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
            $this->render('educational_materials/index' . $this->templatePostfix);
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

            $this->flash($removeSuccess ? 'Материалы успешно удалены' : 'Некоторые материалы не были удалены(возможно, Вы предприняли попытку удалить материал, который не был загружен Вами)', '/educational_materials/index' . $this->templatesPostfix, 10);
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

        /**
        * Отображение доступных материалов.
        */
        public function action_show(array $params = array()) {
            if (!isset($params[0]) || is_int ($params[0]))
            {
                $this->flash('Не указан идентификатор дисциплины',
                             '/education_programs/available/');
            }

            if (!isset($params[1]) || is_int($params[1]))
            {
                $this->flash('Не указан идентификатор заявки',
                             '/education_programs/available/');
            }

            $disc_id = intval($params[0]);
            $app_id  = intval($params[1]);

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
            $materials = $material->getAllBySections($section_ids);

            $this->set('discipline', $disc_data);
            $this->set('sections', $sections);
            $this->set('materials', $materials);

            $this->render();
        }

        // Функция завода на инструкции
        public function action_instructions_by_student()
        {
            $this->render('users/instructions2');
        }

        // Функция завода на доступные программы
        public function action_available()
        {
            /* Получаем данные слушателя */
            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $app     = Model_Application::create();
            $program = Model_Education_Programs::create();
            $disc    = Model_Discipline::create();
            $payment = Model_Payment::create();

            /* Список доступных направлений и их доступных дисциплин */
            $avail_programs = array();
            /* Список доступных дисциплин (которые покупались отдельно от
            программ) */
            $avail_disciplines = array();

            /**
            * @todo This needs some serious refactoring, though...
            */

            /* Получаем список заявок на образовательные программы */
            $program_apps = $app->getProcessedAppsForPrograms($udata->user_id);

            /* Перебираем его */
            foreach ($program_apps as $a)
            {
                $a = (object) $a;

                if
                (
                    /* Если программы бесплатная */
                    Model_Education_Programs::PAID_TYPE_FREE == $a->paid_type &&
                    /* и заявка принята администратором, */
                    Model_Application::STATUS_ACCEPTED == $a->status
                    ||
                    /* Или если программа платная */
                    Model_Education_Programs::PAID_TYPE_PAID == $a->paid_type &&
                    /* и договор по заявке подписан, */
                    Model_Application::STATUS_SIGNED == $a->status
                )
                {
                    /* То получаем список доступных дисциплин */
                    $discs = $disc->getAllowed($a->object_id,
                                               $a->paid_type,
                                               $a->app_id);

                    /* Получаем информацию о программе */
                    $program_data = $program->getProgramInfo($a->object_id);
                    /* Добавляем доступные дисциплины */
                    $program_data['disciplines'] = $discs;
                    /* Добавляем номер заявки */
                    $program_data['app_id'] = $a->app_id;

                    /* И вносим программу в список доступных */
                    $avail_programs[] = $program_data;
                }
            }

            /* Получаем список заявок на отдельные дисциплины */
            $disc_app = $app->getProcessedAppsForDisciplines($udata->user_id);

            /* Перебираем его */
            foreach ($disc_app as $a)
            {
                $a = (object) $a;

                /* Если программа, которой принадлежит дисциплина, платная */
                if (Model_Education_Programs::PAID_TYPE_PAID == $a->paid_type)
                {
                    /* и договор по заявке ещё не подписан, */
                    if (Model_Application::STATUS_SIGNED !== $a->status)
                    {
                        /* то переходим к следующей заявке */
                        continue;
                    }

                    /* Если договор подписан, то находим общую сумму платежей */
                    $payment_total = $payment->getTotal($a->app_id);

                    /* Если ещё не поступило ни одного платежа, */
                    if (null === $payment_total) {
                        /* то переходим к следующей заявке */
                        continue;
                    }

                    /* Находим стоимость программы */
                    $program_data = $program->getProgramInfo($a->program_id);
                    $program_cost = $program_data['cost'];

                    /* Вычисляем стоимость дисциплины */
                    $total_cost = round($a->coef / 100, 3) * $program_cost;

                    /* Если оплачено меньше, чем дисциплина стоит, */
                    if ($payment_total < $total_cost) {
                        /* то переходим к следующей заявке */
                        continue;
                    }

                }
                /* Если же программа бесплатная */
                elseif (Model_Education_Programs::PAID_TYPE_FREE == $a->paid_type)
                {
                    /* и администратор ещё не принял заявку, */
                    if (Model_Application::STATUS_ACCEPTED !== $a->status)
                    {
                        /* то переходим к следующей заявке */
                        continue;
                    }
                }

                /* Получаем данные дисциплины */
                $program->getDiscipline($a->object_id, $title, $_, $_, $_);

                /* И заносим её в список доступных */
                $disc = array(
                    'discipline_id' => $a->object_id,
                    'title'         => $title,
                    'app_id'        => $a->app_id
                );
                $avail_disciplines[] = $disc;
            }

            $this->set('programs',    $avail_programs);
            $this->set('disciplines', $avail_disciplines);

            //$this->render();
            $this->render('education_programs/available');
        }
    }
?>