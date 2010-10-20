<?php
    class Controller_Teacher_Students extends Mvc_Controller_Abstract {
        public function action_index() {
            $students = Model_Education_Students::create();
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $listeners = $students->getListenerList($udata->user_id);
            $this->set('listeners', $listeners);
            $this->render('teacher_students/index');
        }
    }
?>