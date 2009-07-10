<?php
    
    /* $Id$ */

    abstract class Mvc_Controller_Abstract {
        protected $_request;
        
        public function __construct(Http_Request $request) {
            $this->setRequest($request);
        }
        
        protected function setRequest(Http_Request $request) {
            $this->_request = $request;
        }
        
        public function render(
            $view, array $vars = array(), $layout = 'default'
        ) {                                          
            Mvc_View::create($view)
                ->setLayout($layout)
                ->setVars($vars)
                ->render();
        }
    }

?>