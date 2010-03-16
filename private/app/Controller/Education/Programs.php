<?php
    class Controller_Education_Programs extends Mvc_Controller_Abstract {
        public function action_index () {
            $educationPrograms = Model_Education_Programs::create();
            $this->set('directions', $educationPrograms->getDirections());
            $this->set('courses', $educationPrograms->getCourses());
            $this->set('disciplines', $educationPrograms->getDirectionsDisciplines());
            $this->set('sections', $educationPrograms->getDisciplinesSections());

            $this->render("education_programs/index");
        }

        public function action_add_program ($params) {
            $this->set ('buttonCaption', 'Добавить');
            $this->set ('programType', $params['program_type']);

            $request = $this->getRequest ();
            $request->set ('program_type', $params['program_type']);

            $form = Form_Program_Add::create('/add_program/' . $params['program_type']);
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
                '/education_programs/index',
                3
            );
        }

        public function action_add_discipline ($params) {
            $this->set ('buttonCaption', 'Добавить');

            $request = $this->getRequest ();
            $request->set ('speciality', $params['speciality_id']);

            $form = Form_Discipline_Add::create ('/add_discipline/' . $params['speciality_id']);
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
                '/education_programs/index',
                3
            );
        }

        public function action_add_section ($params) {
            $this->set ('buttonCaption', 'Добавить');

            $request = $this->getRequest ();
            $request->set ('discipline', $params['discipline_id']);

            $form = Form_Section_Add::create ('/add_section/' . $params['discipline_id']);
            $this->set ('form', $form);
            $method = $form->method ();
            if (empty ($request->$method)) {
                $this->render ('education_programs/section_form');
            }

            $educationPrograms = Model_Education_Programs::create ();

            if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/section_form");
            }

            $educationPrograms->createSection (
                $form->discipline->value,
                $form->title->value,
                $form->number->value
            );
            $this->flash (
                'Раздел успешно добавлен',
                '/education_programs/index',
                3
            );
        }

        public function action_remove_program ($params) {
            $educationPrograms = Model_Education_Programs::create ();
            $educationPrograms->removeProgram ($params['program_id'], $params['program_type']);

            $this->flash (
                (
                    ($params['program_type'] == 'direction') ?
                    ('Направление успешно удалено') :
                    ('Курсы успешно удалены')
                ),
                '/education_programs/index',
                3
            );
        }

        public function action_remove_discipline ($params) {
            $educationPrograms = Model_Education_Programs::create ();
            $educationPrograms->removeDiscipline ($params['discipline_id']);

            $this->flash (
                'Дисциплина успешно удалена',
                '/education_programs/index',
                3
            );
        }

        public function action_remove_section ($params) {
            $educationPrograms = Model_Education_Programs::create ();
            $educationPrograms->removeSection ($params['section_id']);

            $this->flash (
                'Раздел успешно удален',
                '/education_programs/index',
                3
            );
        }

        public function action_edit_program ($params) {
            $this->set ('buttonCaption', 'Сохранить');
            $this->set ('programType', $params['program_type']);

            $request = $this->getRequest ();
            $request->set ('program', $params['program_id']);
            $request->set ('program_type', $params['program_type']);

            $form = Form_Program_Edit::create('/edit_program/' . $params['program_type'] . '/' . $params['program_id']);
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
                '/education_programs/index',
                3
            );
        }

        public function action_edit_discipline ($params) {
            $this->set ('buttonCaption', 'Сохранить');

            $request = $this->getRequest ();
            $request->set ('discipline', $params['discipline_id']);

            $form = Form_Discipline_Edit::create ('/edit_discipline/' . $params['discipline_id']);
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
                '/education_programs/index',
                3
            );
        }

        public function action_edit_section ($params) {
            $this->set ('buttonCaption', 'Сохранить');

            $request = $this->getRequest ();
            $request->set ('section', $params['section_id']);

            $form = Form_Section_Edit::create ('/edit_section/' . $params['section_id']);
            $this->set ('form', $form);
            $method = $form->method ();

            $educationPrograms = Model_Education_Programs::create ();

            if (empty ($request->$method)) {
                if (! $form->validateID ($educationPrograms, $request)) {
                    $this->render ('education_programs/section_form');
                }

                $educationPrograms->getSection ($params['section_id'], $title, $number);

                $form->setValue ('title', 	$title);
                $form->setValue ('number', 	$number);

                $this->render ('education_programs/section_form');
            }

            if (! $form->validate ($request, $educationPrograms)) {
                $this->render ('education_programs/section_form');
            }

            $educationPrograms->editSection (
                $params['section_id'],
                $form->title->value,
                $form->number->value
            );

            $this->flash (
                'Данные по разделу успешно изменены',
                '/education_programs/index',
                3
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

            $this->flash (
                'Порядок дисциплин успешно изменён',
                '/education_programs/index',
                3
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
                    /* Если программа бесплатная */
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
            /**
            * @todo Disallow this action to non-authorized users in order to 
            * prevent usage of undefined property $user_id here.
            */
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

            $this->render();
        }

        // Функция завода на инструкции
        public function action_instructions_by_student()
        {
            $this->render('users/instructions2');
        }
}
?>