<?php

    /* $Id$ */

    class Form_Profile_RestorePasswd extends Form_Profile_Abstract {
        /**
        * Метод-конструктор класса. Заполняет структуру формы.
        *
        * @return void
        */
        public function __construct($action) {
            $this
                /* Параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)

                /* Логин */
                ->_addLogin()
                ->setValidator('/^.+$/ixu')
                ->setError('Введите имя пользователя')
            ;
        }

        public static function create($action) {
            return new self($action);
        }
        
        public function validate(Http_Request $request, Model_User $user) {
            /* Выполняем базовую проверку данных формы */
            $result = parent::validate($request);
            
            if (false === $result) {
                return $result;
            }
            
            /* Проверяем, есть ли пользователь с таким логином */
            if (!$user->exists($this->login->value)) {
                $this->setValue('login', '');
                $this->setValidationError('login', 'Пользователь не найден');
                
                return false;
            }
            
            return $result;
        }
    }

?>