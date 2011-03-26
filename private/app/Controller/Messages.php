<?php
    class Controller_Messages extends Mvc_Controller_Abstract {

        public function action_send() {
            $links = Resources::getInstance()->links;

            $args = func_get_args();

            $request = $this->getRequest();
            
            $action = $links->get('messages.send');
            $form = new Form_Message_Send($action);

            if (count($args)) {
                $form->setValue('recipient', $args[0]['to_id']);
            }

            $this->set('form', $form);
            $method = $form->method();

            $messages = new Model_Messages();
            $recipients = $messages->getRecipientsList();
            $this->set('recipients', $recipients);
            $requestData = $request->$method;
            
            //print_r($requestData);

            if (empty($requestData) || !$form->validate($request)) {
                $this->render('messages/send');
            }

            /**
            * @todo Form_Abstract automatically processes values for all defined
            * fields to protect them from XSS.
            */
            
            $recipients = explode (',',$requestData['recipient']);
            foreach ($recipients as $rec) {
                $messageId = $messages->sendMessage($rec, htmlspecialchars($requestData['subject']), htmlspecialchars($requestData['message']));
                
                // надо глянуть этот момент с точки зрения производительности
                if (isset($_FILES['attachment'])) {
                    $messages->addAttachments($messageId, $_FILES['attachment']);
                }
            }
            
            /*
            $messageId = $messages->sendMessage($requestData['recipient'], htmlspecialchars($requestData['subject']), htmlspecialchars($requestData['message']));

            if (isset($_FILES['attachment'])) {
                $messages->addAttachments($messageId, $_FILES['attachment']);
            }
            */

            $this->flash('Сообщение отправлено', $links->get('messages.inbox'), 3);
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
            
            print_r($requestData);
            

            foreach($requestData['messages'] as $i => $value) {
                $messages->removeMessage($i);
            }

            $links = Resources::getInstance()->links;
            $this->flash('Сообщения удалены', $links->get('messages.inbox'), 3);
        }

        public function action_message($params) {
            $messages = new Model_Messages();
            $message = array();

            if (($message = $messages->getMessage($params['message_id'])) === FALSE) {
                $links = Resources::getInstance()->links;
                $this->flash('Сообщение не найдено или же Вы не являетесь его адресатом',
                             $links->get('messages.inbox'), 3);
            }

            $this->set('attachments', $messages->getAttachments($params['message_id']));
            $this->set('message', $message);
            $this->render('/messages/message');
        }

        public function action_attachment($params) {
            $messages = new Model_Messages();

            if (!$messages->getAttachment($params['attachment_id'])) {
                $links = Resources::getInstance()->links;
                $this->flash('Запрошено несуществующее вложение или у Вас недостаточно прав для загрузки вложения', $links->get('messages.inbox'), 3);
            }
        }

    }