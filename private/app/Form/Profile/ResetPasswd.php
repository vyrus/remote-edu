<?php
    
    /* $Id$ */

    class Form_Profile_ResetPasswd extends Form_Profile_Abstract {
        public function __construct($action) {
            $this
                /* Параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                
                /* Поля */
                ->_addNewPasswd()
                ->_addPasswdCheck()
            ;
        }
        
        public static function create($action) {
            return new self($action);
        }
        
        public function validate(Http_Request $request) {
            /* Выполняем базовую проверку */
            $result = parent::validate($request);
            
            if (false === $result) {
                return $result;
            }
            
            /* Если пароль прошёл проверку, то проверяем оба поля пароля на 
            совпадение */
            if (!$this->hasError('new_passwd') &&
                $this->new_passwd->value !== $this->passwd_check->value)
            {
                $this->setValidationError('passwd_check', 
                                          'Пароли должны совпадать');
                $result = false;
            }
            
            if ($this->hasError('passwd'))
            {
                $this->setValue('passwd', '');
                $this->setValue('passwd_check', '');
            }
            
            return $result;
        }
    }

?>