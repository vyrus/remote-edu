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
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $programs->getDiscipline($params['discipline_id'], $title, $labourIntensive, $coef);
            $this->set('discipline_title', $title);
            $this->set('students', $programs->getStudentsByDiscipline($params['discipline_id']));

            $this->render('teacher_courses/discipline');
        }

        public function action_course($params) {
            $programs = Model_Education_Programs::create();
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $programs->getProgram($params['program_id'], 'course', $title, $labourIntensive, $paidType, $cost);
            $this->set('course_title', $title);
            $this->set('students', $programs->getStudentsByCourse($params['program_id']));

            $this->render('teacher_courses/course');
        }
    }
?>