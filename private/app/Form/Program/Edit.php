<?php
	class Form_Program_Edit extends Form_Program_Abstract {
		private function addProgram () {
			$this->addField ('program');
			
			return $this;
		}
		
		public function validateID (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if (! $educationPrograms->programIDExists ($request->program, $request->program_type)) {
				$this->invalidate ();
				$this->setValidationError (
					'program', 
					(
						($request->program_type == 'direction') ?
						('Направление не существует') :
						('Курсы не существуют')
					)
				);
				
				return FALSE;
			}
			
			return TRUE;
		}
		
		protected function validateTitle (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if ($educationPrograms->programExists ($this->title->value, $request->program_type)) {
				$educationPrograms->getProgram ($request->program, $request->program_type, $title, $labourIntensive);
				if ($title != $this->title->value) {				
					$this->invalidate();
					$this->setValidationError (
						'title', 
						(
							($request->program_type == 'direction') ?
							('Направление с таким названием уже существует') :
							('Курсы с таким названием уже существуют')
						)
					);

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
			
			$this->addProgram ();
		}
		
        public static function create($action) {
            return new self($action);
        }
	}
?>