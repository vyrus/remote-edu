<?php

    /* $Id$ */
    
    class Resources extends Resources_Abstract {
        public static function create(array $config = array()) {
            $instance = new self($config);
            return self::setInstance($instance);
        }
        
        protected function get_router() {
            $config = $this->_config['routes'];
            
            $router = Mvc_Router::create($config);
            
            return $router;
        }
        
        protected function get_db() {
            $config = $this->_config['db'];
            
            $db = Db_Pdo::create(
                $config['dsn'], $config['user'], $config['passwd'],
                $config['options']
            );
            
            return $db;
        }
        
        protected function get_view() {
            $view = Mvc_View::create(VIEWS . DS, LAYOUTS . DS);
            return $view;
        }
    }
        
?>