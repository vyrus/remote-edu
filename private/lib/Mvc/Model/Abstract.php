<?php
    
    /* $Id$ */

    abstract class Mvc_Model_Abstract {
        protected $_db;
        
        public function __construct() {
            $this->_db = Resources::getInstance()->db;
        }
        
        protected function execute($sql) {
            return $this->_db->execute($sql);
        }
        
        protected function query($sql) {
            return $this->_db->query($sql);
        }
        
        protected function fetchAll($sql, $fetch_style = Db_Pdo::FETCH_BOTH) {
            return $this->query($sql)->fetchAll($fetch_style);
        }
        
        protected function quote($string, $parameter_type = Db_Pdo::PARAM_STR) {
            return $this->_db->quote($string, $parameter_type);
        }
    }

?>