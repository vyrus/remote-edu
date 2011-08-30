<?php

    /* $Id$ */

    abstract class Model_Base extends Mvc_Model_Abstract {

        protected $_tables = array(
            'applications'         => 'applications',
            'apps_history'         => 'apps_history',
            'checkpoints'          => 'checkpoints',
            'checkpoints_students' => 'checkpoints_students',
            'controls'             => 'controls',
            'controls_students'     => 'controls_students',
            'credited_sections_students' => 'credited_sections_students',
            'disciplines'          => 'disciplines',
            'edu_docs'             => 'edu_docs',
            'examinations'         => 'examinations',
            'extra_attempts'       => 'extra_attempts',
            'materials'            => 'materials',
            'materials_states'     => 'materials_states',
            'message'              =>  'message',
            'message_attachment'   =>  'message_attachment',
            'opened_sections_students' => 'opened_sections_students',
            'payments'             => 'payments',
            'passports'            => 'passports',
            'phones'               => 'phones',
            'programs'             => 'programs',
            'questions'            => 'questions',
            'regions'              => 'regions',
            'localities'           => 'localities',
            'sections'             => 'sections',
            'tests'                => 'tests',
            'users'                => 'users',
        );

    }
