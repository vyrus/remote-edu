<?php
    
    /* $Id$ */

    class Model_User extends Mvc_Model_Abstract {
        protected $_table = 'users';
        
        public static function create() {
            return new self();
        }
        
        public function showAll() {
            $sql = 'SELECT * FROM ' . $this->_table;
            $users = $this->fetchAll($sql);
            
            return $users;
        }
    }

?>