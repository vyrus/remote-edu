<?php
    
    /* $Id$ */

    class Controller_Users extends Mvc_Controller_Abstract {
        public function action_index() {
            $this->render();
        }
        
        public function action_show() {
            $users = Model_User::create()->showAll();
            
            $this->set('users', $users);
            $this->render();
        }
        
        public function action_register() {
            $request = $this->getRequest();
            $form = Form_Registration::create();
            $this->set('form', $form);
            
            if (empty($request->{$form->method()})) {
                $this->render();
            } 
            
            if (!$form->validate($request)) {
                $this->render();
            }
            
            $user = Model_User::create();
            
            if ($user->exists($form->login->value)) {
                $form->invalidate();
                $error = 'Указанное имя пользователя уже занято';
                $form->setValidationError('login', $error);
                
                $this->render();
            }
            
            $user->add(
                $form->login->value,
                $form->passwd->value,
                $form->fio->value
            );
            
            $this->flash('Вы успешно зарегистрированы', '/users/index/');
        }
    }

?>