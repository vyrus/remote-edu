<?php

    /* $Id:  $ */

    /**
    * Контроллер для управления учебными программами преподавателя.
    */
    class Controller_Teacher_Courses extends Mvc_Controller_Abstract {

        /**
        * Отображение списка учебных программ преподавателя.
        */
        public function action_index() {

            $model_education_programs = Model_Education_Programs::create();
            $model_user = Model_User::create();

            $udata = (object) $model_user->getAuth();

            $this->set('disciplines', $model_education_programs->getDisciplinesByResponsibleTeacher($udata->user_id));
            $this->set('courses', $model_education_programs->getCoursesByResponsibleTeacher($udata->user_id));

            $this->render('teacher_courses/index');
        }

        /**
        * Отображение списка слушателей, изучающих дисциплину.
        *
        * @params['discipline_id'] Идентификатор дисциплины.
        */
		public function action_discipline($params) {
            $modelEducationPrograms = Model_Education_Programs::create();
            $modelOpenSect = Model_OpenSection::create();
            $modelCredit = Model_Credit::create();
            $modelControlWork = Model_ControlWork::create();
            $modelUser = Model_User::create();

            $data = array();
            $userList = $modelEducationPrograms->getStudentsByDiscipline($params['discipline_id']);
            $openSections = $modelOpenSect->getOpenSectionsByDiscipline($params['discipline_id']);
            $creditSections = $modelCredit->getCreditsByDiscipline($params['discipline_id']);
            //print_r($creditSections); die();
            $modelEducationPrograms->getDiscipline($params['discipline_id'], $title, $labourIntensive, $coef);
            $sections = $modelEducationPrograms->getSectionsByDiscipline($params['discipline_id']);

            // вот чисто философский вопрос: а этот код действительно должн быть здесь?
            //print_r($userList); die();
            foreach ($userList as &$rec) {
                $userInfo = $modelUser->getUserInfo($rec['user_id']);
                $name = $userInfo['surname'] . ' ' . $userInfo['name'] . ' ' . $userInfo['patronymic'];
                $data[$rec['user_id']] = array(
                    'name' => $name,
                    'open_sections' => array(),
                    'credit_sections' => array(
                       'ids' => array(),
                       'dates' => array()
                    ),
                );
                for ($i = 0; $i < count($openSections); $i++) {
                    if ($openSections[$i]['student_id'] == $rec['user_id']) {
                        array_push($data[$rec['user_id']]['open_sections'], $openSections[$i]['section_id']) ;
                        unset($openSections[$i][$rec['user_id']]);
                    }
                }
                $lastCreditedSection = -1;
                for ($i = 0; $i < count($creditSections); $i++) {
                    if ($creditSections[$i]['student_id'] == $rec['user_id']) {
                        $lastCreditedSection = $creditSections[$i]['section_id']; 
                        array_push($data[$rec['user_id']]['credit_sections']['ids'], $creditSections[$i]['section_id']);
                        array_push($data[$rec['user_id']]['credit_sections']['dates'], $creditSections[$i]['created_date']);
                        unset($creditSections[$i][$rec['user_id']]);
                    }
                }
                if (!$modelCredit->isDisciplineCredited($params['discipline_id'], $rec['user_id'])) { 
                    if ($lastCreditedSection == -1) {
                        $firstSectAr =  $modelEducationPrograms->getFirstSectionIdOfDiscipline($params['discipline_id']);
                        $data[$rec['user_id']]['first_uncredited_section'] = $firstSectAr;
                    } else {
                        $data[$rec['user_id']]['first_uncredited_section'] = 
                            $modelEducationPrograms->getNextSectionOfDiscipline($params['discipline_id'], $lastCreditedSection);
                    }
                    $data[$rec['user_id']]['cur_control_works'] = 
                        $modelControlWork->getStudentMarksBySection($rec['user_id'], $data[$rec['user_id']]['first_uncredited_section']);
                }
                        
            }
            //print_r($data); die();


            $this->set('discipline_id', $params['discipline_id']);
            $this->set('discipline_title', $title);
            $this->set('data', $data);
            $this->set('sections', $sections);
            $this->set('TYPE_NAMES', $modelControlWork->getControlNamesMap());
            $this->set('MARK_NAMES', $modelControlWork->getMarkNamesMap());

            $this->render('teacher_courses/discipline');
        }

        /* 
		public function action_discipline($params) {
            $model_checkpoint = Model_Checkpoint::create();
            $model_education_programs = Model_Education_Programs::create();

            $checkpoints = $model_checkpoint->getCheckpointsByDiscipline($params['discipline_id']);
            $cps = array();
            foreach ($checkpoints as $cp) {
                $cps[$cp['user_id']][$cp['section_id']] = $cp['created'];
            }

            $model_education_programs->getDiscipline($params['discipline_id'], $title, $labourIntensive, $coef);

            //$this->set('next', $model_checkpoint->setNextSectionPass(3, 4));

            $this->set('discipline_title', $title);
            $this->set('checkpoints', $cps);
            $this->set('students', $model_education_programs->getStudentsByDiscipline($params['discipline_id']));
            $this->set('sections', $model_education_programs->getSectionsByDiscipline($params['discipline_id']));

            $this->render('teacher_courses/discipline');
        }
         */
	    


    }
