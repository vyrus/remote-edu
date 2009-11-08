<?php
    
    /* $Id$ */

    abstract class Model_Base extends Mvc_Model_Abstract {
        protected $_tables = array(
            'applications' => 'applications',
            'apps_history' => 'apps_history',
            'programs'     => 'programs',
            'disciplines'  => 'disciplines',
            'users'        => 'users',
            'payments'     => 'payments',
            'sections'     => 'sections',
            'materials'    => 'materials'
        );
    }

?>