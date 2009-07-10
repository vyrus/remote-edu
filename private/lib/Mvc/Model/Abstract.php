<?php
    
    /* $Id$ */

    abstract class Mvc_Model_Abstract {
        protected $_db;
        
        public function __construct() {
            $this->_db = Db_Mysql::getInstance();
        }
    }

?>