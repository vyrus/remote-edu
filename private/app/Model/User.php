<?php
    
    /* $Id$ */

    class Model_User extends Mvc_Model_Abstract {
        public static function create() {
            return new self();
        }
        
        public function register($login, $passwd) {/*_*/}
    }

?>