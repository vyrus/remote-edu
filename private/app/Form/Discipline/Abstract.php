<?php
	class Form_Discipline_Abstract extends Form_Abstract {
		protected function addSpeciality () {
			$this
				->addField ('speciality');
								
			return $this;
		}

		protected function addTitle () {
			$this
				->addField('title')
				->setValidator('/^.{3,256}$/ixu')
				->setError ('Название дисциплины -- последовательность символов длинной не менее 3 символов и не более 256');      
            return $this;
		}
		
		protected function addLabourIntensive () {
			$this
				->addField ('labourIntensive')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Общая трудоемкость должна быть задана целым числом');
			return $this;
		}
		
		protected function addCoef () {			
			$this
				->addField ('coef')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Введите коэффициент дисциплины (от 1 до 100)');
			return $this;
		}
		
		protected function __construct($action) {	
			$this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
				->addSpeciality ()
                ->addTitle ()
                ->addLabourIntensive ()
				->addCoef ();
        }
	}
?>