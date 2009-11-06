<?php
	class Form_Program_Abstract extends Form_Abstract {
		protected function addTitle () {
			$this
				->addField('title')
				->setValidator('/^.{3,256}$/ixu')
				->setError ('Название программы -- последовательность символов длинной не менее 3 символов и не более 256');      
            return $this;
		}
		
		protected function addLabourIntensive () {
			$this
				->addField ('labourIntensive')
				->setValidator ('/^[0-9]+$/ixu')
				->setError ('Общая трудоемкость должна быть задана целым числом');
			return $this;
		}

		protected function setCostValidator (Http_Request $request) {
			$method			= $this->method ();
			$requestData	= &$request->$method;

			if ($requestData['paidType'] == 'free') {
				$requestData['cost'] = NULL;
				return;
			}
			
			$this
				->setValidator('/^[0-9]+(?:\.[0-9]{1,2})?$/ixu', Form_Abstract::VALIDATOR_REGEX, 'cost')
				->setError('Введите стоимость, например "1900.53"');
		}

		public function validate (Http_Request $request, Model_Education_Programs $educationPrograms) {
			$this->setCostValidator ($request);
			
			return (
				(parent::validate ($request)) &&
				($this->validateTitle($educationPrograms, $request))
			);
		}
			
        protected function __construct($action) {
            $this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                ->addTitle ()
                ->addLabourIntensive ()
				->addField ('paidType')
				->addField ('cost');
        }
	}
?>