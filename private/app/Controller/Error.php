<?php
    
    /* $Id$ */

    class Controller_Error extends Mvc_Controller_Abstract {
        public function action_404() {
            $uri = $this->getRequest()->server['REQUEST_URI'];
            $this->set('uri', $uri);
            
            $this->render();
        }
    }

?>