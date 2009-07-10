<?php
    
    /* $Id$ */

    class Controller_Index extends Mvc_Controller_Abstract {
        public function action_index(array $params = array()) {
            $msg = 'Йа контроллерчег =) ';
            if (sizeof($params)) $msg .= print_r($params, true);
            
            $vars = array('message' => $msg);
            $this->render('index/index', $vars);
        }
        
        public function action_static(array $params = array()) {
            $msg = 'I\'m totally static... :-(';
            
            $vars = array('message' => $msg);
            $this->render('index/index', $vars);
        }
        
        public function action_regex(array $params = array()) {
            $msg = 'Regex route with following params: ';
            $msg .= print_r($params, true);
            
            $vars = array('message' => $msg);
            $this->render('index/index', $vars);
        }
        
        public function action_action(array $params = array()) {
            $msg = 'Action! ';
            $msg = print_r($params, true);
            
            $vars = array('message' => $msg);
            $this->render('index/index', $vars);
        }
    }

?>