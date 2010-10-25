<?php
    class Form_Checkpoint_Edit extends Form_Checkpoint_Abstract {

        public function __construct ($action) {
            parent::__construct ($action);
        }

        public static function create($action) {
            return new self($action);
        }

    }
?>