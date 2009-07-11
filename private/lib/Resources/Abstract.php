<?php

    /* $Id$ */
    
    class Resources_Abstract {
        protected static $_instance = null;
        
        protected $_resources = array();
        
        protected $_config;
        
        public function __construct(array $config = array()) {
            $this->_config = $config;
        }
        
        public static function create(array $config = array()) {
            return self::$_instance = new self($config);
        }
        
        public static function getInstance() {
            return self::$_instance;
        }
        
        protected static function setInstance(Resources_Abstract $resources) {
            return self::$_instance = $resources;
        }
        
        public function __get($name) {
            if (isset($this->$name)) {
                return $this->_resources[$name];
            }
            
            $method = 'get_' . $name;
            
            if (!method_exists($this, $method))
            {
                throw new InvalidArgumentException(
                    'Ресурс не найден: ' . $name
                );
            }
            
            $callback = array($this, $method);
            $resource = call_user_func($callback);
            $this->_resources[$name] = $resource;
            
            return $resource;
        }
        
        public function __isset($resource) {
            return array_key_exists($resource, $this->_resources);
        }
    }
        
?>