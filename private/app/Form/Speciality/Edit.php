<?php
	class Form_Speciality_Edit extends Form_Speciality_Abstract {
		private function addSpeciality () {
			$this->addField ('speciality');
			
			return $this;
		}
		
		public function validateID (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if (! $educationPrograms->specialityIDExists ($request->speciality)) {
				$this->invalidate ();
				$this->setValidationError ('speciality', 'Направление не существует');
				
				return FALSE;
			}
			
			return TRUE;
		}
		
		protected function validateTitle (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if ($educationPrograms->specialityExists ($this->title->value)) {
				$educationPrograms->getSpeciality ($request->speciality, $title, $labourIntensive);
				if ($title != $this->title->value) {				
					$this->invalidate();
					$error = 'Направление с таким названием уже существует';
					$this->setValidationError('title', $error);

					return FALSE;
				}
			}
			
			return TRUE;
		}
		
		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
			return (
				(parent::validate ($request, $educationPrograms)) &&
				($this->validateID ($educationPrograms, $request))
			);
		}
		
		public function __construct ($action) {
			parent::__construct ($action);
			
			$this->addSpeciality ();
		}
		
        public static function create($action) {
            return new self($action);
        }
	}
?>