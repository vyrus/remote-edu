<?php
	class Form_Speciality_Add extends Form_Abstract {
		private function addTitle () {
			$this
				->addField('title')
				->setValidator('/^.{3,256}$/ixu')
				->setError ('Название специальности -- последовательность символов длинной не менее 3 символов и не более 256');      
            return $this;
		}
		
		private function addLabourIntensive () {
			$this
				->addField ('labourIntensive')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Общая трудоемкость должна быть задана целым числом');
			return $this;
		}
		
		private function validateTitle (Model_Education_Programs $educationPrograms) {
			if ($educationPrograms->specialityExists ($this->title->value)) {
				$this->invalidate();
				$error = 'Направление с таким названием уже существует';
				$this->setValidationError('title', $error);

				return false;
			}
			
			return true;
		}
		
		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
            if (false === ($result = parent::validate($request))) {
                return $result;
            }
            
            if (false === ($result = $this->validateTitle($educationPrograms))) {
                return $result;
            }
            
            return true;			
		}
		
        public function __construct($action) {
            $this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                ->addTitle ()
                ->addLabourIntensive ();
        }
        
        public static function create($action) {
            return new self($action);
        }		
	}
?>