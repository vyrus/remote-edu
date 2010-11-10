<?php

    /* $Id:  $ */

    /**
    * Контроллер для работы с интерфейсом слушателя "Зачётная книжка".
    */
    class Controller_Student_RecordBook extends Mvc_Controller_Abstract {

        /**
        * Отображение списка дисциплин слушателя.
        */
        public function action_index() {
            $model_education_students = Model_Education_Students::create();
            $model_user = Model_User::create();

            $udata = (object) $model_user->getAuth();

            $disciplines = $model_education_students->getDisciplines($udata->user_id);
            $disciplines_programs = $model_education_students->getDisciplinesPrograms($udata->user_id);

            $this->set('disciplines', $disciplines);
            $this->set('disciplines_programs', $disciplines_programs);
            $this->set('user_id', $udata->user_id);

            $this->render('student_recordbook/index');
        }

        /**
        * Отображение слушателю успеваемости по дисциплине.
        *
        * @params int discipline_id Идентификатор дисциплины.
        */
        public function action_discipline($params) {
            $model_checkpoint = Model_Checkpoint::create();
            $model_education_programs = Model_Education_Programs::create();
            $model_education_students = Model_Education_Students::create();
            $model_user = Model_User::create();

            $udata = (object) $model_user->getAuth();

            $checkpoints = $model_checkpoint->getCheckpointsSectionsByDiscipline(
                array(
                    'student_id' => $udata->user_id,
                    'discipline_id' => $params['discipline_id']
                )
            );
            $model_education_programs->getDiscipline($params['discipline_id'], $title, $labourIntensive, $coef);
            $user_info = $model_user->getUserInfo($udata->user_id);

            $this->set('discipline_title', $title);
            $this->set('checkpoints', $checkpoints);
            $this->set('students', $model_education_programs->getStudentsByDiscipline($params['discipline_id']));
            $this->set('sections', $model_education_programs->getSectionsByDiscipline($params['discipline_id']));
            $this->set('user_id', $udata->user_id);

            $this->render('student_recordbook/discipline');
        }

    }