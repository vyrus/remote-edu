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
            $model_checkpoint = Model_Checkpoint::create();
            $model_education_programs = Model_Education_Programs::create();

            $checkpoints = $model_checkpoint->getCheckpointsByDiscipline($params['discipline_id']);
            $cps = array();
            foreach ($checkpoints as $cp) {
                $cps[$cp['user_id']][$cp['section_id']] = $cp['created'];
            }

            $model_education_programs->getDiscipline($params['discipline_id'], $title, $labourIntensive, $coef);

            $this->set('discipline_title', $title);
            $this->set('checkpoints', $cps);
            $this->set('students', $model_education_programs->getStudentsByDiscipline($params['discipline_id']));
            $this->set('sections', $model_education_programs->getSectionsByDiscipline($params['discipline_id']));

            $this->render('teacher_courses/discipline');
        }

    }