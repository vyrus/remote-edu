<?php
    
    /* $Id$ */

    class Controller_Applications extends Mvc_Controller_Abstract {
        const RETURN_URL = '/applications/index/';
        
        public function action_index() {
            $this->render();
        }
        
        /**
        * Подача заявки на программу/дисциплину.
        */
        public function action_apply() {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            
            if (!$user->isExtendedProfileSet($udata->user_id))
            {
                $msg = 'Заполните, пожалуйста, свой профиль';
                $link = '/users/profile_extended/';
                $this->flash($msg, $link);
            }
            
            $object_id = 2;
            $type = Model_Application::TYPE_DISCIPLINE;
            
            $app = Model_Application::create();       
            $app->apply($udata->user_id, $object_id, $type);
            
            $this->flash('Заявка подана', self::RETURN_URL);
        }
        
        /**
        * Просмотр статуса заявок слушателем.
        */
        public function action_list() {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            
            /**
            * @todo Paginator.
            */
            $app = Model_Application::create();
            $apps = $app->getAppsInfo($udata->user_id);
            $this->set('applications', $apps);
            
            $this->render();
        }
    }
    
?>