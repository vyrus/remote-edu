<?php
    
    /* $Id$ */

    class Model_Region extends Mvc_Model_Abstract {
        public static function create() {
            return new self();
        }
        
        /**
        * @todo Move tables to parent app model.
        */
        public function findLike($name) {
             $sql = '
                SELECT region_id AS id, name
                FROM regions 
                WHERE name LIKE ?
                LIMIT 10             
            ';
            
            $stmt = $this->prepare($sql);
            $name = '%' . $name . '%';
            $stmt->execute(array($name));
            
            $regions = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            return $regions;
        }
        
        public function exists($id) {
            $sql = '
                SELECT COUNT(*)
                FROM regions
                WHERE region_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            
            $count = $stmt->fetchColumn();
            return $count > 0;
        }
    }

?>