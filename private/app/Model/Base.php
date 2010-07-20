<?php

    /* $Id$ */

    abstract class Model_Base extends Mvc_Model_Abstract {
        protected $_tables = array(
            'applications'      => 'applications',
            'apps_history'      => 'apps_history',
            'checkpoints'       => 'checkpoints',
            'disciplines'       => 'disciplines',
            'edu_docs'          => 'edu_docs',
            'materials'         => 'materials',
            'materials_states'  => 'materials_states',
            'payments'          => 'payments',
            'passports'         => 'passports',
            'phones'            => 'phones',
            'programs'          => 'programs',
            'questions'         => 'questions',
            'regions'           => 'regions',
            'localities'        => 'localities',
            'sections'          => 'sections',
            'users'             => 'users',
        );
    }

?>