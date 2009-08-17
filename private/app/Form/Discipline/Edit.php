<?php
	class Form_Discipline_Edit extends Form_Discipline_Abstract {
		private function addDiscipline () {
			$this->addField ('discipline');
			
			return $this;
		} 
		
		public function validateID (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if (! $educationPrograms->disciplineIDExists ($request->discipline)) {
				$this->invalidate ();
				$this->setValidationError ('discipline', 'Дисциплина не существует');
				
				return FALSE;
			}
			
			return TRUE;
		}
		
		protected function validateTitle (Model_Education_Programs $educationPrograms, Http_Request $request) {
			if ($educationPrograms->disciplineExists ($request->discipline, $this->title->value, Model_Education_Programs::CHECK_BY_OWN_ID)) {
				$educationPrograms->getDiscipline ($request->discipline, $title, $labourIntensive, $coef);
				if ($title != $this->title->value) {				
					$this->invalidate();
					$error = 'Дисциплина с таким названием уже существует';
					$this->setValidationError('title', $error);

					return FALSE;
				}
			}

			return TRUE;
		}
		
		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
            return (
				(parent::validate($request)) &&
				($this->validateID ($educationPrograms, $request)) &&
				($this->validateTitle($educationPrograms, $request))
			);
		}
		
		public function __construct ($action) {
			parent::__construct ($action);
			$this->addDiscipline ();
		} 
		
        public static function create($action) {
            return new self($action);
        }		
	}
?>