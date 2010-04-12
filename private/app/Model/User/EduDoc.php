<?php

    /* $Id$ */

    /**
    * Контейнер для хранения данных документа об образовании слушателя.
    */
    class Model_User_EduDoc extends Model_User_Abstract {
        /**
        * Тип документа.
        * 
        * @see Model_User::DOC_TYPE_*
        * 
        * @var mixed
        */
        public $type;
        
        /**
        * Произвольный типа документа, заданный слушателем.
        * 
        * @var string
        */
        public $customType;
        
        /**
        * Номер документа.
        * 
        * @var string
        */
        public $number;
        
        /**
        * Год окончания.
        * 
        * @var int
        */
        public $exitYear;
        
        /**
        * Специальность.
        * 
        * @var string
        */
        public $speciality;
        
        /**
        * Квалификация.
        * 
        * @var string
        */
        public $qualification;
        
        /**
        * Префикс названий столбцов в базе данных.
        * 
        * @var string
        */
        protected $_column_prefix = 'edu_doc_';
        
        /**
        * Карта для отображения названий атрибутов класса в названия 
        * соответствующих столбцов в записи БД.
        * 
        * @var array
        */
        protected $_column_map = array(
            'type'          => 'type',
            'customType'    => 'custom_type',
            'number'        => 'number',
            'exitYear'      => 'exit_year',
            'speciality'    => 'speciality',
            'qualification' => 'qualification'
        );
        
        /**
        * Карта для отображения названий атрибутов класса в названия 
        * соответствующих полей в форме.
        * 
        * @var array
        */
        protected $_form_map = array(
            'type'          => 'doc_type',
            'customType'    => 'doc_custom_type',
            'number'        => 'doc_number',
            'exitYear'      => 'exit_year',
            'speciality'    => 'speciality',
            'qualification' => 'qualification'
        );
        
        /**
        * Создание нового экземпляра контейнера.
        * 
        * @return Model_User_EduDoc
        */
        public static function create() {
            return new self();
        }
    }

?>