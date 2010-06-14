<?php

    /* $Id$ */

    /**
    * Контейнер для хранения паспортных данных слушателя.
    */
    class Model_User_Passport extends Model_User_Abstract {
        /**
        * Серия паспорта.
        *
        * @var int
        */
        public $series;

        /**
        * Номер паспорта.
        *
        * @var int
        */
        public $number;

        /**
        * Дата рождения (в формате дд.мм.гггг).
        *
        * @var string
        */
        public $birthday;

        /**
        * Кем выдан.
        *
        * @var string
        */
        public $givenBy;

        /**
        * Когда выдан.
        *
        * @var string
        */
        public $givenDate;

        /**
        * Прописка: идентификатор региона.
        *
        * @var int
        */
        public $regionId;

        /**
        * Прописка: идентификатор населённого пункта.
        *
        * @var int
        */
        public $cityId;

        /**
        * Прописка: улица.
        *
        * @var string
        */
        public $street;

        /**
        * Прописка: дом.
        *
        * @var string
        */
        public $house;

        /**
        * Прописка: квартира.
        *
        * @var string
        */
        public $flat;

        /**
        * Префикс названий столбцов в базе данных.
        *
        * @var string
        */
        protected $_column_prefix = 'passport_';

        /**
        * Карта для отображения названий атрибутов класса в названия
        * соответствующих столбцов в записи БД.
        *
        * @var array
        */
        protected $_column_map = array(
            'series'    => 'series',
            'number'    => 'number',
            'birthday'  => 'birthday',
            'givenBy'   => 'given_by',
            'givenDate' => 'given_date',
            'regionId'  => 'region_id',
            'cityId'    => 'city_id',
            'street'    => 'street',
            'house'     => 'house',
            'flat'      => 'flat',
        );

        /**
        * Карта для отображения названий атрибутов класса в названия
        * соответствующих полей в форме.
        *
        * @var array
        */
        protected $_form_map = array(
            'series'    => 'passport_series',
            'number'    => 'passport_number',
            'birthday'  => 'birthday',
            'givenBy'   => 'passport_given_by',
            'givenDate' => 'passport_given_date',
            'regionId'  => 'region_id',
            'cityId'    => 'city_id',
            'street'    => 'street',
            'house'     => 'house',
            'flat'      => 'flat'
        );

        /**
        * Создание нового экземпляра контейнера.
        *
        * @return Model_User_Passport
        */
        public static function create() {
            return new self();
        }

        /**
        * Преобразования формата дат дня рождения и даты выдачи паспорта к виду
        * дд.мм.гггг после заполнения контейнера из записи БД.
        *
        * @return void
        */
        protected function _afterFromRow() {
            if (!empty($this->birthday))
                $this->_fromMysqlDate($this->birthday);

            if (!empty($this->givenDate))
                $this->_fromMysqlDate($this->givenDate);
        }

        /**
        * Преобразования формата дат дня рождения и даты выдачи паспорта к виду
        * гггг-мм-дд после заполнения контейнера из формы.
        *
        * @return void
        */
        protected function _afterFromForm() {
            $this->_toMysqlDate($this->birthday);
            $this->_toMysqlDate($this->givenDate);
        }

        /**
        * Преобразования даты к виду дд.мм.гггг.
        *
        * @param  string $date Дата (передаётся по ссылке).
        * @return void
        */
        protected function _fromMysqlDate(& $date) {
            $date = date('d.m.Y', strtotime($date));
        }

        /**
        * Преобразования даты к виду гггг-мм-дд.
        *
        * @param  string $date Дата (передаётся по ссылке).
        * @return void
        */
        protected function _toMysqlDate(& $date) {
            $date = date('Y-m-d', strtotime($date));
        }
    }

?>