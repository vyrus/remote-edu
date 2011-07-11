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
            $links = Resources::getInstance()->links;

            $request = $this->getRequest();

            if (empty($params)) {
                $this->flash('Не указан идентификатор заявки',
                             $links->get('admin.applications', array ('sort_field' => 'fio', 'sort_direction' => 'asc')));
            }

            $app_id = intval(array_shift($params));

            $opts = array('app_id' => $app_id);
            $action = $links->get('payments.add', $opts);

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

            $this->flash($msg, $links->get('admin.applications',array ('sort_field' => 'fio', 'sort_direction' => 'asc')));
        }
    }

?>
