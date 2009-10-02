<?php
    
    /* $Id: Students.php $ */

    /**
    * Модель для работы со студентами.
    */
    class Model_Education_Students extends Mvc_Model_Abstract {
        /**
        * Название таблицы с пользователями в БД.
        * 
        * @var string
        */
        protected $_table = 'users';
        
        /**
        * Кэш данных авторизации.
        * 
        * @var array
        */
        protected static $_auth_cache = null;
        
        /**
        * Создание экземпляра модели.
        * 
        * @return Model_Education_Students
        */
        public static function create() {
            return new self();
        }
        /**
        * Получение списка студентов.
        * 
        * @param  int $id Идентификатор.
        * @return array|false
        */
        public function getStudentList() {
            $sql = '
                SELECT user_id, login
                FROM ' . $this->_table . '
                WHERE role = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array('student'));
                           
            return $stmt->fetchAll();
        }        
    }
?>
