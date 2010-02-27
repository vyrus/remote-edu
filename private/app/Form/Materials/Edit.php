<?php
	class Form_Materials_Edit extends Form_Abstract{			
		protected function addDescription () {
			$this->addField('description')
                ->setValidator('/^.{3,256}$/ixu')
                ->setError('Длинна описания -- не менее 3 символов');      
			
			return $this;
		}
		
        public function __construct($action) {	
			$this->setAction($action)
                ->setMethod(self::METHOD_POST)
				->addDescription()
				->addField('type');				
        }
		
		public static function create ($action) {
			return new self($action);
		}
	}
?>