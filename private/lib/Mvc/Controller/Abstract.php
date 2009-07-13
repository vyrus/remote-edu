<?php
    
    /* $Id$ */

    abstract class Mvc_Controller_Abstract {
        protected $_request;
        
        protected $_view_vars = array();
        
        public function __construct(Http_Request $request) {
            $this->setRequest($request);
        }
        
        protected function getRequest() {
            return $this->_request;
        }
        
        protected function setRequest(Http_Request $request) {
            $this->_request = $request;
        }
        
        protected function set($var, $value) {
            $this->_view_vars[$var] = $value;
        }
        
        public function render
        (
            $template = null, array $vars = array(), $layout = 'default'
        )
        {   
            if (null === $template) {
                $handler = $this->getRequest()->_router['handler'];
                $template = sprintf(
                    '%s/%s', $handler['controller'], $handler['action']
                );
            }              
            
            $view_vars = array_merge($this->_view_vars, $vars);
            
            $view = Resources::getInstance()->view;
            $view->setLayout($layout)
                 ->setTemplate($template)
                 ->setVars($view_vars)
                 ->render();
        }
    }

?>