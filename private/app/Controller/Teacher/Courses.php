<?php

    /* $Id:  $ */

    class Controller_Teacher_Courses extends Mvc_Controller_Abstract {

        public function action_index() {
            $model_programs = Model_Education_Programs::create();
            $model_user = Model_User::create();
            $udata = (object) $model_user->getAuth();
            $this->set('disciplines', $model_programs->getDisciplinesByResponsibleTeacher($udata->user_id));
            $this->set('courses', $model_programs->getCoursesByResponsibleTeacher($udata->user_id));
            $this->render('teacher_courses/index');
        }

        public function action_discipline($params) {
            $model_programs = Model_Education_Programs::create();
            $model_checkpoint = Model_Checkpoint::create();

            $checkpoints = $model_checkpoint->getCheckpointsByDiscipline($params['discipline_id']);
            $cps = array();
            foreach ($checkpoints as $cp) {
                $cps[$cp['user_id']][$cp['section_id']] = $cp['created'];
            }

            $model_programs->getDiscipline($params['discipline_id'], $title, $labourIntensive, $coef);
            $this->set('discipline_title', $title);
            $this->set('checkpoints', $cps);
            $this->set('students', $model_programs->getStudentsByDiscipline($params['discipline_id']));
            $this->set('sections', $model_programs->getSectionsByDiscipline($params['discipline_id']));

            $this->render('teacher_courses/discipline');
        }

    }