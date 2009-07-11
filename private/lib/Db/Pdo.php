<?php

    /* $Id$ */

    class Db_Pdo extends PDO {
        public function __construct
        (
            $dsn, $username = null, $password = null,
            array $driver_options = array()
        )
        {
            $driver_options[self::ATTR_STATEMENT_CLASS] = array(
                'Db_Pdo_Statement'
            );
            parent::__construct($dsn, $username, $password, $driver_options);
        }
        
        public static function create
        (
            $dsn, $username = null, $password = null,
            array $driver_options = array()
        )
        {
            return new self($dsn, $username, $password, $driver_options);
        }
    }

?>