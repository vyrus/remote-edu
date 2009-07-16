<?php
    
    /* $Id$ */

    class Controller_Users extends Mvc_Controller_Abstract {
        public function action_index() {
            $this->render();
        }
        
        public function action_show() {
            $users = Model_User::create()->showAll();
            
            $this->set('users', $users);
            $this->render('users/show');
        }
        
        public function action_register() {
            $this->render();
        }
    }

?>