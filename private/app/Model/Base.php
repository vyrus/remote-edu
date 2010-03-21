<?php
    
    /* $Id$ */

    abstract class Model_Base extends Mvc_Model_Abstract {
        protected $_tables = array(
            'applications'      => 'applications',
            'apps_history'      => 'apps_history',
            'checkpoints'       => 'checkpoints',
            'disciplines'       => 'disciplines',
            'materials'         => 'materials',
            'materials_states'  => 'materials_states',
            'payments'          => 'payments',
            'programs'          => 'programs',
            'sections'          => 'sections',
            'users'             => 'users',
        );
    }

?>