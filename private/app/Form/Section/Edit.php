<?php
	class Form_Section_Edit extends Form_Section_Abstract {
		private function addSection () {
			$this->addField ('section');
			
			return $this;
		}
		
		public function validateID (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if (! $educationPrograms->sectionIDExists ($request->section)) {
				$this->invalidate ();
				$this->setValidationError ('section', 'Раздел не существует');
								
				return FALSE;
			}
			
			return TRUE;
		}

		private function validateTitle (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if ($educationPrograms->sectionExists ($request->section, $this->title->value, Model_Education_Programs::CHECK_BY_OWN_ID)) {
				$educationPrograms->getSection ($request->section, $title, $number);
				if ($title != $this->title->value) {
					$this->invalidate();
					$error = 'Раздел с таким названием уже существует';
					$this->setValidationError('title', $error);

					return FALSE;
				}
			}

			return TRUE;
		}
		
		private function validateNumber (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if ($educationPrograms->sectionNumberExists ($request->section, $this->number->value, Model_Education_Programs::CHECK_BY_OWN_ID)) {				
				$educationPrograms->getSection ($request->section, $title, $number);
				if ($number != $this->number->value) {				
					$this->invalidate();
					$error = 'Раздел с таким номером уже существует';
					$this->setValidationError('number', $error);

					return FALSE;
				}
			}

			return TRUE;
		}
		
		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
			return (
				(parent::validate($request)) &&
				($this->validateTitle($educationPrograms, $request)) &&
				($this->validateNumber ($educationPrograms, $request))
			);
		}		
		
		public function __construct ($action) {
			parent::__construct ($action);
			$this->addSection ();
		}
		
		public static function create($action) {
            return new self($action);
        }		
	}
?>