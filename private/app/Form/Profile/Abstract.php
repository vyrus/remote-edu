<?php
    
    /* $Id$ */

    abstract class Form_Profile_Abstract extends Form_Abstract {
        const PASSWD_REGEX_REQUIRED = '/^[a-z0-9_]+$/ixu';
        const PASSWD_REGEX_NOT_REQUIRED = '/^(?:[a-z0-9_]+)?$/ixu';
        
        protected function _validateRegistration(Http_Request $request, Model_User $user) {
            /* Если базовая проверка нашла ошибки, сразу возвращаем результат */
            if (false === ($result = parent::validate($request))) {
                return $result;
            }
            
            if (false === ($result = $this->_validateLogin($user))) {
                return $result;
            }
            
            return true;
        }
        
        protected function _validateLogin(Model_User $user) {
            /* C помощью модели проверяем наличие пользователя с таким логином */
            if ($user->exists($this->login->value))
            {
                $this->invalidate();
                $error = 'Указанное имя пользователя уже занято';
                $this->setValidationError('login', $error);
                
                return false;
            }
        }
        
        protected function _addLogin() {
            $this->addField('login')
                 ->setValidator('/^[a-z0-9_]{3,}$/ixu')
                 ->setError(
                     'Имя пользователя должно состоять из букв латинского ' . 
                     'алфавита, цифр и символа подчёркивания (минимальная' .
                     'длина - 3 символа)'
                 );      
            return $this;
        }
        
        protected function _addPasswd() {
            $this->addField('passwd')
                 ->setValidator(self::PASSWD_REGEX_REQUIRED)
                 ->setError(
                     'Пароль должен состоять из латинских букв, цифр и ' .
                     'символа подчёркивания'
                 );
            return $this;
        }
        
        protected function _addPasswdCheck() {
            $this->addField('passwd_check');
            return $this;
        }
        
        protected function _addOldPasswd() {
            $this->addField('old_passwd')
                 ->setValidator(self::PASSWD_REGEX_NOT_REQUIRED)
                 ->setError(
                     'Пароль должен состоять из латинских букв, цифр и ' .
                     'символа подчёркивания'
                 );
            return $this;
        }
        
        protected function _addNewPasswd() {
            $this->addField('new_passwd')
                 ->setValidator(self::PASSWD_REGEX_NOT_REQUIRED)
                 ->setError(
                     'Пароль должен состоять из латинских букв, цифр и ' .
                     'символа подчёркивания'
                 );
            return $this;
        }
        
        protected function _addEmail() {
            $this->addField('email')
                 ->setValidator('|[0-9a-z-]+@[0-9a-z-^\.]+\.[a-z]{2,6}|i')
                 ->setError(
                     'Некорректный адрес электронной почты'
                 );      
            return $this;
        }
        
        protected function _addRole() {
            $this->addField('role')
                 ->setValidator('/^(?:teacher|admin)$/ixu')
                 ->setError(
                     'Некорректно задана роль пользователя'
                 );
            return $this;
        }
        
        protected function _addSurname() {
            $this->addField('surname')
                 ->setValidator('/^[А-Я]{1}[а-я]{1,}$/xu')
                 ->setError(
                     'Фамилия должны состоять минимум из 2-х букв русского (первая - заглавная)'
                 );      
            return $this;
        }
        
        protected function _addName() {
            $this->addField('name')
                 ->setValidator('/^[А-Я]{1}[а-я]{1,}$/xu')
                 ->setError(
                     'Имя должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                 );
            return $this;
        }
        
        protected function _addPatronymic() {
            $this->addField('patronymic')
                 ->setValidator('/^[А-Я]{1}[а-я]{1,}$/xu')
                 ->setError(
                     'Отчество должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                 );
            return $this;
        }
    }

?>