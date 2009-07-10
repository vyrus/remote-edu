<?php
    
    /* $Id$ */

    abstract class Mvc_Controller_Abstract {
        protected $_db;
        
        public function __construct() {
            $this->_db = Db_Mysql::getInstance();
        }
    }

?>