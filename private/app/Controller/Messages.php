<?php
    class Controller_Messages extends Mvc_Controller_Abstract {
        public function action_send() {
            $args = func_get_args();
            
            $request = $this->getRequest();            
			
			$form = new Form_Message_Send('/messages/send');
			
			if (count($args)) {
		        $form->setValue('recipient', $args[0]['to_id']);
		    }
			$this->set('form', $form);
			$method = $form->method();
			
			$messages = new Model_Messages();
			$recipients = $messages->getRecipientsList();
			$this->set('recipients', $recipients);

            $requestData = $request->$method;
			
			if (empty($requestData) || !$form->validate($request)) {
				$this->render('messages/send');
			}
						
			$messages->sendMessage($requestData['recipient'], htmlspecialchars($requestData['subject']), htmlspecialchars($requestData['message']));
			$this->flash('Сообщение отправлено', '/messages/inbox', 3);
        }
        
		public function action_inbox() {
		    $args = func_get_args();
		    $page = count($args) ? $args[0]['page'] : 0;
		    		    
		    $messages = new Model_Messages();
		    $inbox = $messages->getInbox($page, $messagesTotalNumber);

            $this->set('inbox', $inbox);
            $this->set('messagesTotalNumber', $messagesTotalNumber);
            $this->set('page', $page);

	        $this->render('messages/inbox');
	    }
		
		public function action_remove() {
		    $messages = new Model_Messages();
		    
		    $request = $this->getRequest();
	        $method = 'post';
	        $requestData = $request->$method;
	        
	        foreach($requestData['messages'] as $i => $value) {
                $messages->removeMessage($i);
            }
            
            $this->flash('Сообщения удалены', '/messages/inbox', 3);
	    }
	    
	    public function action_message($params) {
            $messages = new Model_Messages();
            $message = array();
            
            if (($message = $messages->getMessage($params['message_id'])) === FALSE) {
                $this->flash('Сообщение не найдено или же Вы не являетесь его адресатом', '/messages/inbox', 3);
            }
            
            $this->set('message', $message);
            $this->render('/messages/message');
        }
	}
?>