<?php
    
    /* $Id$ */

    class Controller_Applications extends Mvc_Controller_Abstract {
        const RETURN_URL = '/applications/index/';
        
        public function action_index() {
            $this->render();
        }
        
        public function action_apply() {
            $user = Model_User::create();
                    
            if (false === ($udata = $user->getAuth())) {                               
                $this->flash('Вы не авторизованы', self::RETURN_URL);
            }
            
            $udata = (object) $udata;
            
            /**
            * @todo ACL.
            */
            if (Model_User::ROLE_STUDENT !== $udata->role) {
                $this->flash('Вы не являетесь слушателем', self::RETURN_URL);
            }
            
            if (!$user->isExtendedProfileSet($udata->user_id)) {
                $this->flash('А у вас профиль не заполнен :-p', self::RETURN_URL);
            }
            
            $this->flash('Ок', self::RETURN_URL);
        }
    }
    
?>