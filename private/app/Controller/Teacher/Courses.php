<?php
    class Controller_Teacher_Courses extends Mvc_Controller_Abstract {
        public function action_index() {
            $this->render('teacher_courses/index');
        }
    }
?>