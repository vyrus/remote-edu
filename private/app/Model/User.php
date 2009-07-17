<?php
    
    /* $Id$ */

    class Model_User extends Mvc_Model_Abstract {
        protected $_table = 'users';
        
        public static function create() {
            return new self();
        }
        
        /**
        * @todo Check whether sql-injections are possible.
        */
        public function exists($login) {
            $sql = '
                SELECT COUNT(*)
                FROM  ' . $this->_table . '
                WHERE login = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($login));
            
            $count = current($stmt->fetch());
            
            return $count > 0;
        }
        
        public function add($login, $passwd, $fio) {
            $sql = '
                INSERT INTO ' . $this->_table . '
                (login, passwd, fio)
                VALUES
                (:login, :passwd, :fio)
            ';
            
            $values = array(
                ':login'  => $login,
                ':passwd' => $passwd,
                ':fio'    => $fio
            );
            
            return $this->prepare($sql)
                        ->execute($values);
        }
        
        public function showAll() {
            $sql = 'SELECT * FROM ' . $this->_table;
            $users = $this->fetchAll($sql, Db_Pdo::FETCH_ASSOC);
            
            return $users;
        }    
    }

?>