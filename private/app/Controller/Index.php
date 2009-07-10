<?php
    
    /* $Id$ */

    class Controller_Index extends Mvc_Controller_Abstract {
        public function action_index() {
            echo 'Йа контроллерчег =)';
        }
        
        public function action_static(array $params = array()) {
            echo 'I\'m totally static... :-(';
        }
        
        public function action_regex(array $params = array()) {
            echo 'Regex route with following params: ';
            print_r($params);
        }
        
        public function action_action(array $params = array()) {
            echo 'Action! ';
            print_r($params);
        }
    }

?>