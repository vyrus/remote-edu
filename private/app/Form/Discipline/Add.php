<?php
	class Form_Discipline_Add extends Form_Discipline_Abstract {
		private function validateSpeciality (Model_Education_Programs $educationPrograms) {
			if (! $educationPrograms->programIDExists ($this->speciality->value, 'direction')) {
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
			return (
				(parent::validate($request)) &&
				($this->setValue ('speciality', $request->speciality)->validateSpeciality ($educationPrograms)) &&
				($this->validateTitle($educationPrograms))
			);
		}

        public static function create($action) {
            return new self($action);
        }
	}
?>