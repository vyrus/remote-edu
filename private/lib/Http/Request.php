<?php
    
    /* $Id$ */

    class Http_Request {
        protected $_storage = array();
        
        public function __construct(array $values = array()) {
            $this->setMulti($values);
        }
        
        public static function create(array $values = array()) {
            return new self($values);
        }
        
        public function set($key, $value) {
            $this->_storage[$key] = $value;
            return $this;
        }
        
        public function setMulti(array $values = array()) {
            foreach ($values as $key => $value) {
                $this->set($key, $value);
            }
            
            return $this;
        }
        
        public function /* & */__get($name) {
            if (!isset($this->$name))
            {
                throw new InvalidArgumentException(
                    sprintf('Параметр "%s" не определён', $name)
                );
            }
            
            return $this->_storage[$name];
        }
        
        public function __isset($name) {
            return isset($this->_storage[$name]);
        }
        
        public function __toString() {
            return '"' . print_r($this, true) . '"';
        }
    }

?>