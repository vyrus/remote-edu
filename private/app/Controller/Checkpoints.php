<?php

    /* $Id:  $ */

    class Controller_Checkpoints extends Mvc_Controller_Abstract {

        /**
        * Создание/редактирование контрольной точки.
        */
        public function action_edit() {
            $request = $this->getRequest();
            $params = $request->__get('post');
            $server = $request->__get('server');

            if (!isset($params['active'])) {
                $params['active'] = false;
            }

            $model = Model_Education_Programs::create();

            if (!$params['active']) {
                $model->setCheckpointInactive(
                    $params['section_id']
                );
            } else {
                $model->setCheckpoint(
                    $params['section_id'],
                    $params['active'],
                    $params['title'],
                    $params['text'],
                    $params['type'],
                    $params['test_id']
                );
            }

            $msg = 'Контрольная точка успешно изменена';

            $this->flash($msg, $server['HTTP_REFERER'], 2);
        }

    }

?>