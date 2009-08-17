<?php
	class Form_Speciality_Add extends Form_Speciality_Abstract {
		protected function validateTitle (Model_Education_Programs $educationPrograms) {
			if ($educationPrograms->specialityExists ($this->title->value)) {
				$this->invalidate();
				$error = 'Направление с таким названием уже существует';
				$this->setValidationError('title', $error);

				return FALSE;
			}
			
			return TRUE;
		}
				
        public static function create($action) {
            return new self($action);
        }
	}
?>