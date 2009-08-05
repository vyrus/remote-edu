<?php
    
    /* $Id$ */

    /**
    * Форма регистрации пользователей.
    */
    class Form_Registration extends Form_Abstract {
        /**
        * Реализует дополнительную проверку на незанятость логина.
        * 
        * @param  Http_Request $request Объект запроса.
        * @param  Model_User   $user    Модель работы с пользователями.
        * @return
        */
        public function validate(Http_Request $request, Model_User $user) {
            /* Если базовая проверка нашла ошибки, сразу возвращаем результат */
            if (false === ($result = parent::validate($request))) {
                return $result;
            }
            
            /* C помощью модели проверяем наличие пользователя с таким логином */
            if ($user->exists($this->login->value))
            {
                $this->invalidate();
                $error = 'Указанное имя пользователя уже занято';
                $this->setValidationError('login', $error);
                
                return false;
            }
            
            return true;
        }
    }

?>