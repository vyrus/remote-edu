<?php

    /* $Id$ */

    /**
    * Контейнер для хранения данных документа об образовании слушателя.
    */
    class Model_User_Phones extends Model_User_Abstract {
        /**
        * Номер стационарного телефона.
        *
        * @var int
        */
        public $stationary;

        /**
        * Номер мобильного телефона.
        *
        * @var int
        */
        public $mobile;

        /**
        * Префикс названий столбцов в базе данных.
        *
        * @var string
        */
        protected $_column_prefix = 'phone_';

        /**
        * Карта для отображения названий атрибутов класса в названия
        * соответствующих столбцов в записи БД.
        *
        * @var array
        */
        protected $_column_map = array(
            'stationary' => 'stationary',
            'mobile'     => 'mobile'
        );

        /**
        * Карта для отображения названий атрибутов класса в названия
        * соответствующих полей в форме.
        *
        * @var array
        */
        protected $_form_map = array(
            'stationary' => 'phone_stationary',
            'mobile'     => 'phone_mobile'
        );

        /**
        * Создание нового экземпляра контейнера.
        *
        * @return Model_User_Phones
        */
        public static function create() {
            return new self();
        }
    }

?>