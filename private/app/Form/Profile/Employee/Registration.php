<?php
    
    /* $Id$ */

    class Form_Profile_Employee_Registration extends Form_Profile_Employee_Abstract {
        public function __construct($action) {
            $this
                /* Устанавливаем параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                
                /* Поля */
                ->_addLogin()
                ->_addRole()
                ->_addEmail()
                ->_addSurname()
                ->_addName()
                ->_addPatronymic()
            ;
        }
        
        public static function create($action) {
            return new self($action);
        }
        
        public function validate(Http_Request $request, Model_User $user) {
            return $this->_validateRegistration($request, $user);
        }
    }

?>