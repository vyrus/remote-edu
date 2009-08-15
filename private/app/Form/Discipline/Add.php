<?php
	class Form_Discipline_Add extends Form_Abstract {
		private function addSpeciality () {
			$this
				->addField ('speciality');
								
			return $this;
		}

		private function addTitle () {
			$this
				->addField('title')
				->setValidator('/^.{3,256}$/ixu')
				->setError ('Название дисциплины -- последовательность символов длинной не менее 3 символов и не более 256');      
            return $this;
		}
		
		private function addLabourIntensive () {
			$this
				->addField ('labourIntensive')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Общая трудоемкость должна быть задана целым числом');
			return $this;
		}
		
		private function addCoef () {
			$this
				->addField ('coef')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Хз что тут пока написать,потом допишем');
			return $this;
		}

		private function validateSpeciality (Model_Education_Programs $educationPrograms) {
			if (! $educationPrograms->specialityIDExists ($this->speciality->value)) {
				$this->invalidate();
				$this->setValidationError('speciality', 'Направления с таким ID не существует');
				
				return FALSE;
			}
			
			return TRUE;
		}

		private function validateTitle (Model_Education_Programs $educationPrograms) {
			if ($educationPrograms->disciplineExists ($this->speciality->value, $this->title->value)) {
				$this->invalidate();
				$this->setValidationError(
					'title',
					'В рамках данного направления уже существует дисциплина с таким названием уже существует'
				);

				return false;
			}
			
			return true;
		}
		
		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
            if (false === ($result = parent::validate($request))) {
                return $result;
            }

			$this->setValue ('speciality', $request->speciality);
			if ($this->validateSpeciality ($educationPrograms) === FALSE) {
				return FALSE;
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
				->addSpeciality ()
                ->addTitle ()
                ->addLabourIntensive ()
				->addCoef ();
        }
        
        public static function create($action) {
            return new self($action);
        }		
	}
?>