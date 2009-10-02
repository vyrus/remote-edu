<?php
    class Controller_Education_Students extends Mvc_Controller_Abstract {
        public function action_index () {
            $educationStudents = Model_Education_Students::create ();
            $this->set ('student_list',  $educationStudents->getStudentList());
            
            $this->render ("education_students/index");
        }
    }
?>
