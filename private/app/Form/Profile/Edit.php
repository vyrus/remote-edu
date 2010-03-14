<?php
	class Form_Profile_Edit extends Form_Abstract{
        public function __construct($action) {
            $this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                ->_addRole()
                ->_addSurname()
                ->_addName()
                ->_addPatronymic();
        }	    
	    			
        protected function _addRole() {
            return
            $this->addField('role')
                 ->setValidator('/^(?:teacher|admin|student)$/ixu')
                 ->setError(
                     'Некорректно задана роль пользователя'
                 );
        }

        protected function _addSurname() {
            return
            $this->addField('surname')
                 ->setValidator('/^[А-ЯЁ]{1}[а-яё]{1,}$/xu')
                 ->setError(
                     'Фамилия должны состоять минимум из 2-х букв русского (первая - заглавная)'
                 );
        }

        protected function _addName() {
            return
            $this->addField('name')
                 ->setValidator('/^[А-ЯЁ]{1}[а-яё]{1,}$/xu')
                 ->setError(
                     'Имя должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                 );
        }

        protected function _addPatronymic() {
            return
            $this->addField('patronymic')
                 ->setValidator('/^[А-ЯЁ]{1}[а-яё]{1,}$/xu')
                 ->setError(
                     'Отчество должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                 );
        }
		
		public static function create ($action) {
			return new self($action);
		}
	}
?>    