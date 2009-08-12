<?php
    
    /* $Id$ */

    class Form_Profile_Student_Minimal extends Form_Profile_Student_Abstract {
        public function __construct($action) {
            $this
                /* Параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                
                /* Поля */
                ->_addNewPasswd()
                ->_addPasswdCheck()
                ->_addEmail()
                ->_addOldPasswd()
            ;
        }
        
        public static function create($action) {
            return new self($action);
        }
        
        public function validate(Http_Request $request) {
            /* Выполняем базовую проверку */
            $result = parent::validate($request, $user);
            
            /* Если пароль прошёл проверку, то проверяем оба поля пароля на совпадение */
            if (!$this->hasError('passwd') &&
                $this->passwd->value !== $this->passwd_check->value)
            {
                $this->setValidationError('passwd', 'Пароли должны совпадать');
                $result = false;
            }
            
            if ($this->hasError('passwd'))
            {
                $this->setValue('passwd', '');
                $this->setValue('passwd_check', '');
            }
            
            if (false === $result) {
                return false;
            }
            
            return $result;
        }
    }

?>