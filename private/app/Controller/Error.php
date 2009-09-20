<?php
    
    /* $Id$ */

    class Controller_Error extends Mvc_Controller_Abstract {
        public function action_403() {
            $uri = $this->getRequest()->server['REDIRECT_URL'];
            $this->set('uri', $uri);
            
            $this->render();
        }
        
        public function action_404() {
            $uri = $this->getRequest()->server['REDIRECT_URL'];
            $this->set('uri', $uri);
            
            $this->render();
        }
    }

?>