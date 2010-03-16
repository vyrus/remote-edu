<?php
    
    /* $Id$ */

    /**
    * Обработка действий, связанных с пользователями.
    */
    class Controller_Users extends Mvc_Controller_Abstract {
        /**
        * Карта для отображения ролей из select'а во внутреннее обозначение.
        * 
        * @var array
        */
        protected $_roles_map = array(
            'teacher' => Model_User::ROLE_TEACHER,
            'admin'   => Model_User::ROLE_ADMIN
        );

        protected $_roles_captions = array(
            Model_User::ROLE_TEACHER => 'Преподаватель',
            Model_User::ROLE_STUDENT => 'Слушатель',
            Model_User::ROLE_ADMIN => 'Администратор',
        );

        /**
        * Инструкции для админа.
        */
        public function action_index_by_admin() {
            $links = Resources::getInstance()->links;
            $msg = 'В пункте "зарегистрированные слушатели" можно просмотреть пользователей,
                    находящихся на стадии:<br />
                    "зарегистрирован",
                    "обучается",
                    и тд';
            $this->flash($msg, $links->get('admin.users'));
        }
        
        /**
        * Регистрация нового слушателя (первичная).
        */
        public function action_register_student() {
            $links = Resources::getInstance()->links;
            $request = $this->getRequest();
            
            /* Создаём объект формы с полями первичной регистрации */
            $action = $links->get('student.register');
            $form = Form_Profile_Student_Registration::create($action);
            $this->set('form', $form);
            
            /* Если данных от формы нет, предлагаем заполнить */
            $method = $form->method();
            if (empty($request->$method)) {
                $this->render();
            }
            
            $user = Model_User::create();
            
            /* Если данные не прошли проверку, показываем ошибки */
            if (!$form->validate($request, $user)) {
                $this->render();
            }
            
            /* Берём данные из формы */
            $login  = $form->login->value;
            $passwd = $form->passwd->value;
            $email  = $form->email->value;
            
            /* Регистрируем в базе нового слушателя */
            $id = $user->register(
                $login, $passwd, Model_User::ROLE_STUDENT, $email
            );
            
            /* Генерируем код активации */
            $activation_code = $user->getActivationCode($id);
            
            /* И его отправляем на email */
            $postman = Resources::getInstance()->postman;
            $postman->sendRegLetterStudent(
                $id, $login, $email, $activation_code
            );
            
            /* Выводим сообщение об успешной регистрации */
            $this->render('users/register_student_ok');
         }
        
        /**
        * Активация аккаунта слушателя.
        * 
        * @param array $params array(user_id, code).
        */
        public function action_activate_student(array $params = array()) {
            /* Шаблон, который выводится при успешной активации */
            $tpl_ok = 'users/activate_student_ok';
            /* И шаблон для вывода ошибок */
            $tpl_error = 'users/activate_student_error';
            
            $user = Model_User::create();
            $id = $params['user_id'];            

            /* Проверяем статус пользователя */
            $status = $user->getStatus($id);

            if (false === $status) {
                $this->set('message', 'пользователь не найден');
                $this->render($tpl_error);            
            }
            
            if (Model_User::STATUS_INACTIVE !== $status) {
                $this->set('message', 'аккаунт уже активирован');
                $this->render($tpl_error);
            }
            
            /* Проверяем код активации */
            $code = $user->getActivationCode($id);

            if ($params['code'] !== $code) {
                $this->set('message', 'неправильный код активации');
                $this->render($tpl_error);
            }
            
            /* Активируем слушателя */
            $result = $user->activateStudent($id);
            
            if (false === $result) {
                $this->set('message', 'не удалось активировать аккаунт');
                $this->render($tpl_error);
            }
            
            /* Сразу же авторизуем пользователя, чтобы не заставлять его 
            вспоминать свой пароль :) */
            $auth = Resources::getInstance()->auth;
            $auth->init()
                 ->setUserId($id);
            
            /* И выводим сообшение об успешной активации */
            $this->render($tpl_ok);
        }
        
        /**
        * Регистрация сотрудника (преподавателя/администратора).
        */
        public function action_register_employee_by_admin() {
            $links = Resources::getInstance()->links;
            $request = $this->getRequest();
            
            /* Создаём объект формы с полями первичной регистрации */
            $action = $links->get('employee.register');
            $form = Form_Profile_Employee_Registration::create($action);
            $this->set('form', $form);
            
            /* Если данных от формы нет, предлагаем заполнить */
            $method = $form->method();
            if (empty($request->$method)) {
                $this->render();
            }

            /* Создаём модель пользователя - она нужна для валидации формы */
            $user = Model_User::create();
            
            /* Если данные не прошли проверку, показываем ошибки */
            if (!$form->validate($request, $user)) {
                $this->render();
            }
            
            /* Выбираем значения из формы */
            $login      = $form->login->value;
            $role       = $form->role->value;
            $email      = $form->email->value;
            $surname    = $form->surname->value;
            $name       = $form->name->value;
            $patronymic = $form->patronymic->value;
                                   
            $role = $this->_roles_map[$role];
                                        
            /* Пароль сгенерируем потом - при активации */    
            $id = $user->register(
                $login, null, $role, $email, $surname, $name, $patronymic
            );
            
            /* Генерируем код активации и отправляем на email */
            $activation_code = $user->getActivationCode($id);
            
            $postman = Resources::getInstance()->postman;
            $postman->sendRegLetterEmployee(
                $id, $login, $email, $activation_code
            );
                                                         
            $this->flash('Новый сотрудник успешно добавлен', 
                         $links->get('admin.users'));
        }
        
        /**
        * Активация аккаунта сотрудника (преподавателя/администатора).
        * 
        * @param array $params array(user_id, code).
        */
        public function action_activate_employee(array $params = array()) {
            $links = Resources::getInstance()->links;
            
            $user = Model_User::create();
            $id = $params['user_id'];
            
            /* Проверяем текущий статус */
            $status = $user->getStatus($id);

            if (false === $status) {
                $this->flash('Пользователь не найден', $redirect_link);
            }
            
            if (Model_User::STATUS_INACTIVE !== $status) {
                $this->flash('Аккаунт уже активирован', $redirect_link);
            }
            
            /* Сверяем код активации */
            $code = $user->getActivationCode($id);

            if ($params['code'] !== $code) {
                $this->flash('Неправильный код активации', $redirect_link, false);
            }
            
            /* Генерируем пароль для аккаунта и обновляем данные в БД */
            $passwd = $user->generatePassword();
            $result = $user->activateEmployee($id, $passwd);
            
            if ($result) {
                $msg = 'Аккаунт успешно активирован, ваш пароль <strong>' . 
                        $passwd .
                        '</strong>';
            } else {
                $msg = 'Не удалось активировать аккаунт';
            }
            
            $this->flash($msg, $links->get('users.login'), false);
        }
        
        /**
        * Авторизация пользователя.
        */
        public function action_login() {
            $links = Resources::getInstance()->links;
            
            /* Получаем объект запроса */
            $request = $this->getRequest();
            
            /* Инициализируем обработчик формы */
            $action = $links->get('users.login');
            $form = Form_Profile_Login::create($action);
            $this->set('form', $form);
            
            /* Если данных от формы нет, выводим страничку */
            if (empty($request->{$form->method()})) {
                $this->render();
            }
            
            /**
            * @todo Защита от брутфорса.
            */
            
            /* Если в форме что-то не заполнено, просим исправить */
            if (!$form->validate($request)) {
                $this->render();
            }
            
            $user = Model_User::create();
            
            $login  = $form->login->value;
            $passwd = $form->passwd->value;
            
            /* Проверяем, есть ли такой пользователь */          
            if (false === $user->login($login, $passwd, $result))
            {
                if (Model_User::ERR_USER_NOT_FOUND === $result) {
                    $error = 'Пользователь с такими именем и паролем не найден';
                }
                elseif (Model_User::ERR_USER_INACTIVE === $result) {
                    $error = 'Аккаунт не активирован';
                }
                
                $form->setValue('login',  '');
                $form->setValue('passwd', '');
                
                $this->set('error', $error);
                
                $this->render();
            }
            
            /**
            * @todo Куда редиректить-то надо?
            */
            $role2alias = array(
                Model_User::ROLE_STUDENT => 'student.index',
                Model_User::ROLE_TEACHER => 'index',
                Model_User::ROLE_ADMIN   => 'admin.users'
            );
            
            $udata = (object) $result;
            $alias = $role2alias[$udata->role];
            $link  = $links->get($alias);

            $msg = 'Вы успешно авторизованы';
            $this->flash($msg, $link, 0);
            /**
            * @todo Let's do normal redirects, ha? :)
            */
            //$this->render($redirect_link);              
        }

        /**
        * Отображение информации об авторизованном пользователе.
        */
        public function action_whoami() {
            $user = Model_User::create();
            
            if (false === ($udata = $user->getAuth())) {                               
                $this->flash('Вы не авторизованы', $redirect_link);
            }
            
            $msg = sprintf(
                'Вы авторизованы как %s (%s)',
                $udata['login'], $udata['role']
            );
            $this->flash($msg, '#', false);
        }

        public function action_users_list() {
            $users = Model_User::create();
            $this->set('users', $users->getUsersList());
            $this->set('rolesCaptions', $this->_roles_captions);
            $this->render('users/users_list');
        }
        
        public function action_profile_extended_by_student() {
            $links = Resources::getInstance()->links;
            
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            
            if ($user->isExtendedProfileSet($udata->user_id)) {
                $this->flash('Ваш профиль уже заполнен', 
                             $links->get('student.index'));
            }
            
            $request = $this->getRequest();
            
            $action = $links->get('student.extended-profile');
            $form = Form_Profile_Student_Extended::create($action);
            $this->set('form', $form);
            
            $method = $form->method();
            if (empty($request->$method)) {
                $this->render();
            }
            
            $region = Model_Region::create();
            $locality = Model_Locality::create();
            
            if (!$form->validate($request, $region, $locality)) {
                $this->render();
            }
            
            $profile = array(
                'general' => array(
                    'surname'    => null,
                    'name'       => null,
                    'patronymic' => null
                ),
                
                'passport' => array(
                    'birthday'   => null,
                    'series'     => 'passport_series',
                    'number'     => 'passport_number',
                    'given_by'   => 'passport_given_by',
                    'given_date' => 'passport_given_date',
                    'region_id'  => null,
                    'city_id'    => null,
                    'street'     => null,
                    'house'      => null,
                    'flat'       => null
                ),
                
                'edu_doc' => array(
                    'type'          => 'doc_type',
                    'custom_type'   => 'doc_custom_type',
                    'number'        => 'doc_number',
                    'exit_year'     => null,
                    'speciality'    => null,
                    'qualification' => null
                ),
                
                'phones' => array(
                    'mobile'     => 'phone_mobile',
                    'stationary' => 'phone_stationary'
                )
            );
            
            foreach ($profile as $section => $fields)
            {
                foreach ($fields as $field_id => $value)
                {
                    $id = (null === $value ? $field_id : $value);
                    $value = $form->$id->value;
                    
                    if (!strlen($value)) {
                        $value = null;
                    }
                    
                    $profile[$section][$field_id] = $value;
                }
            }
            
            self::_toMysqlDate($profile['passport']['birthday']);
            self::_toMysqlDate($profile['passport']['given_date']);
            
            if (!$user->setExtendedProfile($udata->user_id, $profile)) {
				$this->flash('Ошибка при сохранении профиля', 
                             $links->get('student.extended-profile'), false);
            }
                               
            
            $this->flash('Ваш профиль успешно обновлён', 
                         $links->get('student.apply'), false);
            //$this->render('applications/index_by_student');    
        }

        public function action_view_profile_extended($user_id) 
        {
            $user = Model_User::create();
            /*...*/
        }

        /**
        * Снятие авторизации.
        */
        public function action_logout() {
            $user = Model_User::create();
            $user->resetAuth();
                     
            $links = Resources::getInstance()->links;
            $this->flash('Авторизация потеряна', $links->get('index'), 0);
            //$this->render($redirect_link);
        }    
        
        protected static function _toMysqlDate(& $date) {
            $date = date('Y-m-d', strtotime($date));
        }

        public function action_edit_account($params) {            
            $form = Form_Profile_Edit::create('/users/edit_account/' . $params['user_id']); 
            $users = Model_User::create();
            $this->set('form', $form);
            $this->set('rolesCaptions', $this->_roles_captions);
            $request = $this->getRequest();            
            $method = $form->method();
            $requestData = $request->$method;
            
            if (empty($requestData)) {
                if (($userInfo = $users->getUserInfo($params['user_id'])) === FALSE) {
                    $this->flash('Пользователь не существует', '/users/users_list', 10);
                }
                
                $form->setValue('surname', $userInfo['surname']);
                $form->setValue('name', $userInfo['name']);
                $form->setValue('patronymic', $userInfo['patronymic']);
                $form->setValue('role', $userInfo['role']);
            }
            else if ($form->validate($request)) {
                $userInfo = array(
                    'name' => $requestData['name'],
                    'surname' => $requestData['surname'],
                    'patronymic' => $requestData['patronymic'],
                    'role' => $requestData['role'],
                    'user_id' => $params['user_id'],
                );
                $users->setUserInfo($userInfo);
                $this->flash('Данные пользователя успешно изменены', '/users/users_list', 10);
            }
            
            $this->render('users/edit_account');
        }
    }
?>