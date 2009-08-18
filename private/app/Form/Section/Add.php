<?php
	class Form_Section_Add extends Form_Section_Abstract {
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

		public static function create($action) {
            return new self($action);
        }
	}
?>