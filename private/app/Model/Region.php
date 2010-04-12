<?php
    
    /* $Id$ */

    /**
    * Модель для работы со списком регионов.
    */
    class Model_Region extends Model_Base {
        /**
        * Создание экземпляра модели.
        * 
        * @return Model_Region
        */
        public static function create() {
            return new self();
        }
        
        /**
        * Нахождение регионов по части названия.
        * 
        * @param  string $name Название (может быть неполным).
        * @return array Список найденных названий.
        */
        public function findLike($name) {
             $sql = '
                SELECT region_id AS id, name
                FROM ' . $this->_tables['regions'] . ' 
                WHERE name LIKE ?
                LIMIT 10             
            ';
            
            $stmt = $this->prepare($sql);
            $name = '%' . $name . '%';
            $stmt->execute(array($name));
            
            $regions = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            return $regions;
        }
        
        /**
        * Проверка существования записи о региона по его идентификатору.
        * 
        * @param  int $id
        * @return boolean
        */
        public function exists($id) {
            $sql = '
                SELECT COUNT(*)
                FROM ' . $this->_tables['regions'] . '
                WHERE region_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            
            $count = $stmt->fetchColumn();
            return $count > 0;
        }
        
        /**
        * Получение названия региона.
        * 
        * @param  int $locality_id Идентификатор региона.
        * @return string
        */
        public function getName($region_id) {
            $sql = '
                SELECT name
                FROM ' . $this->_tables['regions']. '
                WHERE region_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($region_id));
            
            return $stmt->fetchColumn(0);
        }
    }

?>