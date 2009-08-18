<?php
    
    /* $Id$ */

    class Controller_Ajax extends Mvc_Controller_Abstract {
        public function render
        (
            $template = null, array $vars = array(), $layout = 'ajax'
        )
        {
            return parent::render($template, $vars, $layout);
        }
        
        public function action_autocomplete_region() {
            $request = $this->getRequest();
            $region = Model_Region::create();
            
            if (!isset($request->get['query'])) {
                return;
            }
                 
            $query = $request->get['query'];
            $regions = $region->findLike($query);
            
            $this->set('query', $query);
            $this->set('regions', $regions);
            
            $this->render();
        }
        
        public function action_autocomplete_city() {
            $request = $this->getRequest();
            $locality = Model_Locality::create();
            
            if (!isset($request->get['query'])) {
                return;
            }
            
            $query = $request->get['query'];
            $region_id = $request->get['region_id'];
            $cities = $locality->findLike($query, $region_id);
            
            $this->set('query', $query);
            $this->set('cities', $cities);
            
            $this->render();
        }
    }

?>