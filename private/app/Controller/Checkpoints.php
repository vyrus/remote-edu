<?php

    /* $Id: Applications.php 241 2010-08-09 10:51:48Z vyrus $ */

    class Controller_Checkpoints extends Mvc_Controller_Abstract {

        /**
        * Создание/редактирование контрольной точки.
        */
        public function action_edit() {
            //$user = Model_User::create();
            //$udata = (object) $user->getAuth();

            $request = $this->getRequest();
            $params = $request->__get('post');
            $server = $request->__get('server');
//            echo '<pre>';
//            print_r($server['HTTP_REFERER']);
//            echo '</pre>';
            

            $model = Model_Education_Programs::create();
            $model->setCheckpoint2($params['checkpoint_object_id'],
                                   $params['checkpoint_object_type'],
                                   $params['title'],
                                   $params['text'],
                                   $params['type']);

            //$links = Resources::getInstance()->links;
            //$return_url = $links->get($server['HTTP_REFERER']);

            $msg = 'Контрольная точка успешно изменена';

            $this->flash($msg, $server['HTTP_REFERER'], 2);
        }

    }

?>