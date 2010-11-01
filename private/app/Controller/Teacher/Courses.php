<?php
    class Controller_Teacher_Courses extends Mvc_Controller_Abstract {
        public function action_index() {
            $programs = Model_Education_Programs::create();
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $this->set('disciplines', $programs->getDisciplinesByResponsibleTeacher($udata->user_id));
            $this->set('courses', $programs->getCoursesByResponsibleTeacher($udata->user_id));
            $this->render('teacher_courses/index');
        }

        public function action_discipline($params) {
            $programs = Model_Education_Programs::create();
            $checkpoint = Model_Checkpoint::create();
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $programs->getDiscipline($params['discipline_id'], $title, $labourIntensive, $coef);
            $this->set('discipline_title', $title);
            $cps = array();
            $checkpoints = $checkpoint->getCheckpointsByDiscipline($params['discipline_id']);
            foreach ($checkpoints as $cp) {
                $cps[$cp['user_id']][$cp['section_id']] = $cp['created'];
            }

            $this->set('checkpoints', $cps);
            $this->set('students', $programs->getStudentsByDiscipline($params['discipline_id']));
            $this->set('sections', $programs->getSectionsByDiscipline($params['discipline_id']));

            $this->render('teacher_courses/discipline');
        }

    }
?>