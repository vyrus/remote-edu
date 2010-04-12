<?php
    
    /* $Id$ */

    /**
    * Абстрактный класс контейнера для хранения и передачи данных расширенного 
    * профиля слушателя.
    */
    abstract class Model_User_Abstract {
        /**
        * Префикс названий столбцов в базе данных.
        * 
        * @var string
        */
        protected $_column_prefix;
        
        /**
        * Карта для отображения названий атрибутов класса в названия 
        * соответствующих столбцов в записи БД.
        * 
        * @var array
        */
        protected $_column_map;
        
        /**
        * Карта для отображения названий атрибутов класса в названия 
        * соответствующих полей в форме.
        * 
        * @var array
        */
        protected $_form_map;
        
        /**
        * Создание нового экземпляра контейнера.
        */
        abstract public static function create();
        
        /**
        * Загрузка данных в контейнер из записи базы данных.
        * 
        * @param  array $row Запись в форме array(столбец => значение).
        * @return Model_User_Abstract Fluent interface.
        */
        public function fromRow(array $row) {
            /* Перебираем все элементы из карты преобразования */
            foreach ($this->_column_map as $attr => $column) {
                /* И сохраняем значения в атрибутах класса */
                $this->$attr = $row[$this->_column_prefix . $column];
            }
            
            /* Если в классе объявлен метод для пост-обработки данных формы, */
            if (method_exists($this, '_afterFromRow')) {
                /* вызываем его */
                $this->_afterFromRow();
            }
            
            return $this;
        }
        
        /**
        * Загрузка данных в контейнер из формы расширенного профиля.
        * 
        * @param  Form_Profile_Student_Extended $form Объект формы.
        * @return Model_User_Abstract Fluent interface.
        */
        public function fromForm(Form_Profile_Student_Extended $form) {
            /* Перебираем все элементы из карты преобразования */
            foreach ($this->_form_map as $attr => $field) {
                /* И сохраняем значения в атрибутах класса */
                $this->$attr = $form->$field->value;
            }
            
            /* Если в классе объявлен метод для пост-обработки данных формы, */
            if (method_exists($this, '_afterFromForm')) {
                /* вызываем его */
                $this->_afterFromForm();
            }
            
            return $this;
        }
        
        /**
        * Наполнение формы расширенного профиля данными из контейнера.
        * 
        * @param Form_Profile_Student_Extended $form Объект формajhvs/
        * @return Model_User_Abstract Fluent interface.
        */
        public function toForm(Form_Profile_Student_Extended $form) {
            /* Перебираем все элементы из карты преобразования */
            foreach ($this->_form_map as $attr => $field) {
                /* И устанавливаем значения полей в форме */
                $form->setValue($field, $this->$attr);
            }
            
            return $this;
        }
    }

?>