<?php
	class Form_Program_Add extends Form_Program_Abstract {
		protected function validateTitle (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if ($educationPrograms->programExists ($this->title->value, $request->program_type)) {
				$this->invalidate();
				$this->setValidationError(
					'title',
					(
						($request->program_type == 'direction') ?
						('Направление с таким названием уже существует') :
						('Курсы с таким названием уже существуют')
					)					
				);

				return FALSE;
			}
			
			return TRUE;
		}
				
        public static function create($action) {
            return new self($action);
        }
	}
?>