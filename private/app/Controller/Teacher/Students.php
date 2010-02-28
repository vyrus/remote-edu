<?php
    class Controller_Teacher_Students extends Mvc_Controller_Abstract {
        public function action_index() {
            $this->render('teacher_students/index');
        }
    }
?>