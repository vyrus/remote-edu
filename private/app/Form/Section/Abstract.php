<?php
	class Form_Section_Abstract extends Form_Abstract {
		private function addDiscipline () {
			$this
				->addField ('discipline');
								
			return $this;
		}

		private function addTitle () {
			$this
				->addField('title')
				->setValidator('/^.{3,256}$/ixu')
				->setError ('Название дисциплины -- последовательность символов длинной не менее 3 символов и не более 256');      
            return $this;
		}
		
		private function addNumber () {
			$this
				->addField ('number')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Номер раздела должен быть задан целым числом');
			return $this;
		}
				
        public function __construct($action) {	
			$this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
				->addDiscipline ()
                ->addTitle ()
				->addNumber ();
        }        
	}
?>