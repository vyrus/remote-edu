<?php
    
    /* $Id$ */

    class Controller_Index extends Mvc_Controller_Abstract {
        public static function create() {
            return new self();
        }
        
        public function action_index() {
            echo 'Ай контроллерчег =)';
        }
    }

?>