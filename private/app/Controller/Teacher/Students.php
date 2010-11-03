<?php

    /* $Id:  $ */

    /**
    * Контроллер для управления слушателями преподавателя.
    */
    class Controller_Teacher_Students extends Mvc_Controller_Abstract {

        /**
        * Отображение списка слушателей преподавателя.
        */
        public function action_index() {
            $model_education_students = Model_Education_Students::create();
            $model_user = Model_User::create();

            $udata = (object) $model_user->getAuth();
            $listeners = $model_education_students->getListenerList($udata->user_id);

            $this->set('listeners', $listeners);
            $this->render('teacher_students/index');
        }

        /**
        * Отображение списка дисциплин слушателя.
        */
        public function action_disciplines($params) {
            $model_checkpoint = Model_Checkpoint::create();
            $model_education_students = Model_Education_Students::create();
            $model_user = Model_User::create();

            $disciplines = $model_education_students->getDisciplines($params['student_id']);
            $disciplines_programs = $model_education_students->getDisciplinesPrograms($params['student_id']);

            $user_info = $model_user->getUserInfo($params['student_id']);

/*            $checkpoints = $model_checkpoint->getCheckpointsByDiscipline($params['discipline_id']);
            $cps = array();
            foreach ($checkpoints as $cp) {
                $cps[$cp['user_id']][$cp['section_id']] = $cp['created'];
            }
*/
            $this->set('user_info', $user_info);
            $this->set('disciplines', $disciplines);
            $this->set('disciplines_programs', $disciplines_programs);
            //$this->set('checkpoints', $cps);

            $this->render('teacher_students/disciplines');
        }

    }