<?php
	class Form_Speciality_Abstract extends Form_Abstract {
		protected function addTitle () {
			$this
				->addField('title')
				->setValidator('/^.{3,256}$/ixu')
				->setError ('Название специальности -- последовательность символов длинной не менее 3 символов и не более 256');      
            return $this;
		}
		
		protected function addLabourIntensive () {
			$this
				->addField ('labourIntensive')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Общая трудоемкость должна быть задана целым числом');
			return $this;
		}

		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
			return (
				(parent::validate ($request)) &&
				($this->validateTitle($educationPrograms, $request))
			);
		}
			
        protected function __construct($action) {
            $this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                ->addTitle ()
                ->addLabourIntensive ();
        }
	}
?>