<?php
    
    /* $Id$ */

    class Controller_Applications extends Mvc_Controller_Abstract {
        const RETURN_URL = '/applications/index/';
        
        public function action_index()
        {
			$educationPrograms = Model_Education_Programs::create ();
			$this->set ('directions',	$educationPrograms->getDirections 				());
			$this->set ('courses', 		$educationPrograms->getCourses 					());
			$this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines 	());

            /* Создаём объект формы с полями первичной регистрации */
            $action = '/applications/apply/';
            $form = Form_Profile_Student_Registration::create($action);
            $this->set('form', $form);
			
            $this->render();
        }
        
        /**
        * Подача заявки на программу/дисциплину.
        */
        public function action_apply($params)
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
/*раскоментировать когда поправится подключение js-функций, подгружающих списки городов/областей
            if (!$user->isExtendedProfileSet($udata->user_id))
            {
                $msg = 'Заполните, пожалуйста, свой профиль';
                $link = '/users/profile_extended/';
                $this->flash($msg, $link);
            }
*/
            /**
            * Получение данных от браузера.
            */
            $object_id = $params['program_id'];
            $type = $params['program_type'];
            
            $app = Model_Application::create();       
            $app->apply($udata->user_id, $object_id, $type);
            
            $this->flash('Заявка подана', self::RETURN_URL);
        }
        
        /**
        * Просмотр статуса заявок слушателем.
        */
        public function action_list() {
            
            /* Управление прходит сюда после диспетчера */
            
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            
            /* Выполняем здесь какие-нибудь общественно-полезные действия =)*/
            
            /**
            * @todo Paginator.
            */
            $app = Model_Application::create();
            $apps = $app->getAppsInfo($udata->user_id);
            
            /* По ходу дейтсвия формируем набор переменных для шаблона */
            $this->set('applications', $apps);
            
            $this->set('test', 'true');
            
            /* А потом уже вызываем рендеринг самого шаблона */
            $this->render();
        }
    }
    
?>