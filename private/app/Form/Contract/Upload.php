<?php
	class Form_Contract_Upload extends Form_Abstract
	{			
		protected function addFileInfo () {
			$this->addField ('filename')
				->setValidator ('/^.+$/ixu')
				->setError ('Не выбран файл');
				
			return $this;
		}

		public function validate (Http_Request $request) {
			return (
				parent::validate ($request) 
			);
		}

        public function __construct($action) {	
			$this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
				->addFileInfo ();				
        }
		
		public static function create ($action) {
			return new self ($action);
		}
	}
?>