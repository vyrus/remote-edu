<?php
    
    /* $Id$ */

    class Model_Locality extends Mvc_Model_Abstract {
        public static function create() {
            return new self();
        }
        
        public function findLike($name, $region_id) {
             $sql = '
                SELECT locality_id AS id, name, type
                FROM localities 
                WHERE name LIKE :name AND
                      region_id = :region_id
                ORDER BY CHAR_LENGTH(name) ASC
                LIMIT 10
            ';
            
            $values = array(
                ':name'      => '%' . $name . '%',
                ':region_id' => $region_id
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            
            $localities = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            return $localities;
        }
        
        public function exists($id) {
            $sql = '
                SELECT COUNT(*)
                FROM localities
                WHERE locality_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            
            $count = $stmt->fetchColumn();
            return $count > 0;
        }
    }

?>