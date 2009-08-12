<?php
    
    /* $Id$ */

    class Form_Profile_Student_Registration extends Form_Profile_Student_Abstract {
        public function __construct($action) {
            $this
                /* Устанавливаем параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                
                /* Поля */
                ->_addLogin()
                ->_addPasswd()
                ->_addPasswdCheck()
                ->_addEmail()
            ;
        }
        
        public static function create($action) {
            return new self($action);
        }
        
        public function validate(Http_Request $request, Model_User $user) {
            $result = $this->_validateRegistration($request, $user);
            
            /* Если пароль прошёл проверку, то оба поля пароля на совпадение */
            if (!$this->hasError('passwd') &&
                $this->passwd->value !== $this->passwd_check->value)
            {
                $this->setValidationError('passwd', 'Пароли должны совпадать');
                $result = false;
            }
            
            if ($this->hasError('passwd')) {
                $this->setValue('passwd', '');
                $this->setValue('passwd_check', '');
            }
            
            return $result;
        }
    }

?>