<?php

    /* $Id$ */

    class Controller_Applications extends Mvc_Controller_Abstract {
        /**
        * форма для подачи заявки слушателем.
        */
        public function action_index_by_student()
        {
            $educationPrograms = Model_Education_Programs::create ();
            $this->set ('directions',	$educationPrograms->getDirections 				());
            $this->set ('courses', 		$educationPrograms->getCourses 					());
            $this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines 	());

            $this->render();
        }

        /**
        * Список поданных заявок для админа.
        */
        public function action_index_by_admin()
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            /**
            * @todo Paginator.
            */
            $app = Model_Application::create();
            /**
            * @todo Выводить только заявки, которые ещё не полностью оплачены
            * (или вообще ешё не приняты/подписаны).
            */
            $apps = $app->getAllAppsInfo();

            $this->set('applications', $apps);
            $this->set('statuses', Model_Application::getStatusMap());
            $this->set ('invalidMaterialsForms', array ());

            $contract = Model_Contract::create ();

            $request	= $this->getRequest ();
            $requestData	= $request->files;
            if (empty ($requestData)) {
                $this->render ('applications/index_by_admin');
            }
        // [ загрузка договора
            $invalidMaterialsForms = array ();

            foreach ($request->files as $key=>$item)
            {
                //list($empty_str,$app_id) = (split('fileReference',$key));
                list($empty_str,$app_id) = (explode('fileReference',$key));
            }

            $request->set (
                'get',
                array (
                    'filename'		=> $request->files['fileReference' . $app_id]['name'],
                )
            );

            $contractForm = Form_Contract_Upload::create ('');
            $contractForm->setMethod (Form_Abstract::METHOD_GET);
            if (! $contractForm->validate ($request)) {
                $invalidMaterialsForms[] = $contractForm;
            }
            else
            {
                $postman = Resources::getInstance()->postman;

                $postman->sendContractStudent(
                    $udata->email,
                    $request->files['fileReference' . $app_id]['tmp_name']
                );

                $contract->addContract ($request->files['fileReference' . $app_id],$app_id);
            }

            if (! empty ($invalidMaterialsForms)) {
                $this->set		('invalidMaterialsForms', $invalidMaterialsForms);
                $this->render	('educational_materials/upload');
            }

            $links = Resources::getInstance()->links;

            $this->flash (
                'Договор успешно загружен',
                $links->get('admin.applications'),
                3
            );
        }

        /**
        * Подача заявки на программу/дисциплину.
        */
        public function action_apply($params)
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $links = Resources::getInstance()->links;

            /**
            * @todo Раскоментировать когда поправится подключение js-функций,
            * подгружающих списки городов/областей.
            */
            /*
            if (!$user->isExtendedProfileSet($udata->user_id))
            {
                $msg = 'Заполните, пожалуйста, свой профиль';
                $this->flash($msg, $links->get('student.extended-profile'));
            }
            */

            /**
            * Получение данных от браузера.
            */
            $object_id = $params['program_id'];
            $type = $params['program_type'];

            $app = Model_Application::create();
            $app->apply($udata->user_id, $object_id, $type);

            $return_url = $links->get('student.applications');

            $msg = 'Вы успешно подали заявку на учебный курс.<p>
              Через 10 сек. Вас автоматически перенаправят на страницу просмотра
              поданых Вами <a href="' . $return_url . '" title=Мои заявки> заявок</a>.
              <p>
              Также, Вы можете, не дожидаясь перенаправления, перейти на страницу
              <a href="' . $links->get('student.apply') . '" title=Авторизация>Мой новый курс</a> и
              подать зявку на ещё один учебный курс!';

            $this->flash($msg, $return_url, 10);
        }

        /**
        * Просмотр статуса заявок слушателем.
        */
        public function action_list_by_student()
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            /**
            * @todo Paginator.
            */
            $app = Model_Application::create();
            /**
            * @todo If user is not logged in, then $udata is empty. Avoid it by
            * protecting this line with route, allowed only for logged in user.
            */
            $apps = $app->getAppsInfo($udata->user_id);

            $this->set('applications', $apps);
            $this->set('statuses', Model_Application::getStatusMap());

            $this->render();
        }

        public function action_change_app_status($params)
        {
            $links = Resources::getInstance()->links;

            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $new_status = $params['new_status'];
            $app_id = $params['app_id'];

            $app = Model_Application::create();
            $app->setAppStatus($new_status, $app_id);

            $map = Model_Application::getStatusMap();
            $this->flash('Заявка ' . $map[$new_status],
                         $links->get('admin.applications'));

            $this->render();
        }

        /**
        * Удаление заявки из базы данных.
        *
        * @todo Удалять ли платежи из базы при удалении заявки?
        */
        public function action_delete(array $params = array()) {
            $links = Resources::getInstance()->links;
            $return_url = $links->get('admin.applications');

            if (empty($params)) {
                $this->flash('Не указан номер заявки', $return_url);
            }

            $app_id = intval(array_shift($params));
            $app = Model_Application::create();

            if (!$app->delete($app_id)) {
                $msg = 'Не удалось удалить заявку с номером ' . $app_id;
            } else {
                $msg = 'Заявка успешно удалена';
            }

            $this->flash($msg, $return_url);
        }
    }

?>