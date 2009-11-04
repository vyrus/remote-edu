<?php

    /* $Id$ */

    /**
    * Обработка действий, связанных с платежами.
    */
    class Controller_Payments extends Mvc_Controller_Abstract {
        /**
        * Добавление нового платежа
        */
        public function action_add(array $params = array()) {
            $request = $this->getRequest();
            
            if (!isset($params[0])) {
                $this->flash('Не указан идентификатор заявки', '/');
            }
            
            $app_id = intval($params[0]);
            
            /* Используем REDIRECT_URI, т.к. движок работает под mod_rewrite */
            $action = $request->server['REDIRECT_URL'];
            $form = Form_Payment_Add::create($action);
            $this->set('form', $form);
            
            $method = $form->method();
            if (empty($request->$method)) {
                $this->render();
            }
            
            if (!$form->validate($request)) {
                $this->render();
            }
            
            $payment = Model_Payment::create();
            $amount = $form->amount->value;
            
            if (!$payment->add($amount, $app_id)) {
                $msg = 'Не удалось добавить платёж';
            } else {
                $msg = 'Платёж успешно добавлен';
            }
            
            $this->flash($msg, '/');
        }                       
    }

?>