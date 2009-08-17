<?php
	class Form_Section extends Form_Abstract {
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

		private function validateDiscipline (Model_Education_Programs $educationPrograms) {
			if (! $educationPrograms->disciplineIDExists ($this->discipline->value)) {
				$this->invalidate();
				$this->setValidationError('discipline', 'Дисциплины с таким ID не существует');
				
				return FALSE;
			}
			
			return TRUE;
		}

		private function validateTitle (Model_Education_Programs $educationPrograms) {
			if ($educationPrograms->sectionExists ($this->discipline->value, $this->title->value)) {
				$this->invalidate();
				$this->setValidationError(
					'title',
					'В рамках данной дисциплины существует раздел с таким названием'
				);

				return false;
			}
			
			return true;
		}
		
		private function validateNumber (Model_Education_Programs $educationPrograms) {
			if ($educationPrograms->sectionNumberExists ($this->discipline->value, $this->number->value)) {
				$this->invalidate();
				$this->setValidationError(
					'number',
					'В рамках данной дисциплины существует раздел с таким номером'
				);

				return false;
			}
			
			return true;
		}		
		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
			return (
				(parent::validate($request)) &&
				($this->setValue ('discipline', $request->discipline)) &&
				($this->validateDiscipline ($educationPrograms)) &&
				($this->validateTitle($educationPrograms)) &&
				($this->validateNumber ($educationPrograms))
			);
		}
		
        public function __construct($action) {	
			$this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
				->addDiscipline ()
                ->addTitle ()
				->addNumber ();
        }
        
        public static function create($action) {
            return new self($action);
        }		
	}
?>