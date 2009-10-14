<?php
	class Form_Materials_Upload extends Form_Abstract{			
		protected function addDescription () {
			$this->addField ('description')
					->setValidator('/^.{3,256}$/ixu')
					->setError ('Длинна описания -- не менее 3 символов');      
			
			return $this;
		}
		
		protected function addFileInfo () {
			$this->addField ('filename')
				->setValidator ('/^.+$/ixu')
				->setError ('Не выбран файл');
				
			return $this;
		}
		
		protected function validateSection (Http_Request $request) {
			$educationPrograms = Model_Education_Programs::create ();

			if (! $educationPrograms->sectionIDExists ($this->section->value)) {
				$this->invalidate();
				$this->setValidationError(
					'section',
					'Раздел не существует'
				);

				return FALSE;
			}

			return TRUE;			
		}
		
		public function validate (Http_Request $request) {
			return (
				(parent::validate ($request)) && 
				($this->validateSection ($request))
			);
		}

        public function __construct($action) {	
			$this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
				->addDescription ()
				->addFileInfo ()
				->addField ('section');				
        }
		
		public static function create ($action) {
			return new self ($action);
		}
	}
?>