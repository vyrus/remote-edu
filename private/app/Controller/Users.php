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

        /**
        * Инструкции для слушателя.
        */
        public function action_index_by_student() {
            $msg = 'После регистрации и оформления договора вы можете подать заявку.Для этого
                    в пункте меню "заявки" выберите интересующее вас направление ';
            $this->flash($msg, '/users/index_by_student/');

            $this->render();
        }

        /**
        * Инструкции для преподавателя.
        */
        public function action_index_by_teacher() {
            $msg = 'В пункте "учебные материалы" вы можете управлять загруженными материалами и
                    добавлять новые';
            $this->flash($msg, '/users/index_by_teacher/');

            $this->render();
        }

        /**
        * Инструкции для админа.
        */
        public function action_index_by_admin() {
            $msg = 'В пункте "зарегистрированные слушатели" можно просмотреть пользователей,
                    находящихся на стадии:<br />
                    "зарегистрирован",
                    "обучается",
                    и тд';
            $this->flash($msg, '/users/index_by_admin/');

            $this->render();
        }
        
        /**
        * Регистрация нового слушателя (первичная).
        */
        public function action_register_student() {
            $request = $this->getRequest();
            
            /* Создаём объект формы с полями первичной регистрации */
            $action = '/users/register_student/';
            $form = Form_Profile_Student_Registration::create($action);
            $this->set('form', $form);
            
            /* Если данных от формы нет, предлагаем заполнить */
            $method = $form->method();
            if (empty($request->$method)) {
                $this->render();
            }
            
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            if (isset($udata->role))
            {
                if (Model_User::ROLE_TEACHER == $udata->role)
                {
                    $redirect_link = '/users/index_by_teacher/';
                }elseif (Model_User::ROLE_ADMIN == $udata->role)
                {
                    $redirect_link = '/users/index_by_admin/';
                }elseif (Model_User::ROLE_STUDENT == $udata->role)
                {
                    $redirect_link = '/users/index_by_student/';
                }
            }else
            {
                $redirect_link = '/index/index/';
            }
            
            /* Если данные не прошли проверку, показываем ошибки */
            if (!$form->validate($request, $user)) {
                $this->render();
            }
            
            $login  = $form->login->value;
            $passwd = $form->passwd->value;
            $email  = $form->email->value;
            
            /* Регистрируем в базе нового слушателя */
            $id = $user->register(
                $login, $passwd, Model_User::ROLE_STUDENT, $email
            );
            
            /* Генерируем код активации и отправляем на email */
            $activation_code = $user->getActivationCode($id);
            
            $postman = Resources::getInstance()->postman;
            $postman->sendRegLetterStudent(
                $id, $login, $email, $activation_code
            );
            
            $msg = 'Вы успешно зарегистрированы, письмо для активации ' .
                   'отправлено на ваш email';

            $this->flash($msg, $redirect_link);
        }
        
        /**
        * Активация аккаунта слушателя.
        * 
        * @param array $params array(user_id, code).
        */
        public function action_activate_student(array $params = array()) {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            if (isset($udata->role))
            {
                if (Model_User::ROLE_TEACHER == $udata->role)
                {
                    $redirect_link = '/users/index_by_teacher/';
                }elseif (Model_User::ROLE_ADMIN == $udata->role)
                {
                    $redirect_link = '/users/index_by_admin/';
                }elseif (Model_User::ROLE_STUDENT == $udata->role)
                {
                    $redirect_link = '/users/index_by_student/';
                }
            }else
            {
                $redirect_link = '/index/index/';
            }
            
            $id = $params['user_id'];
            $this->flash($msg, $redirect_link);            

            /* Проверяем статус пользователя */
            $status = $user->getStatus($id);

            if (false === $status) {
                $this->flash('Пользователь не найден', $redirect_link);            
            }
            
            if (Model_User::STATUS_INACTIVE !== $status) {
                $this->flash('Аккаунт уже активирован', $redirect_link);            
            }
            
            /* Проверяем код активации */
            $code = $user->getActivationCode($id);

            if ($params['code'] !== $code) {
                $this->flash('Неправильный код активации', $redirect_link, false);
            }
            
            /* Активируем слушателя */
            $result = $user->activateStudent($id);
            
            if ($result) {
                $msg = 'Аккаунт успешно активирован';
            } else {
                $msg = 'Не удалось активировать аккаунт';
            }
            
            $this->flash($msg, $redirect_link, false);
        }
        
        /**
        * Регистрация сотрудника (преподавателя/администратора).
        */
        public function action_register_employee_by_admin() {
            $request = $this->getRequest();
            
            /* Создаём объект формы с полями первичной регистрации */
            $action = '/users/register_employee_by_admin/';
            $form = Form_Profile_Employee_Registration::create($action);
            $this->set('form', $form);
            /* Если данных от формы нет, предлагаем заполнить */
            $method = $form->method();
            if (empty($request->$method)) {
                $this->render();
            }

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
                                                         
            $this->flash('Новый сотрудник успешно добавлен', $redirect_link);
        }
        
        /**
        * Активация аккаунта сотрудника (преподавателя/администатора).
        * 
        * @param array $params array(user_id, code).
        */
        public function action_activate_employee(array $params = array()) {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            if (isset($udata->role))
            {
                if (Model_User::ROLE_TEACHER == $udata->role)
                {
                    $redirect_link = '/users/index_by_teacher/';
                }elseif (Model_User::ROLE_ADMIN == $udata->role)
                {
                    $redirect_link = '/users/index_by_admin/';
                }elseif (Model_User::ROLE_STUDENT == $udata->role)
                {
                    $redirect_link = '/users/index_by_student/';
                }
            }else
            {
                $redirect_link = '/index/index/';
            }

            $id = $params['user_id'];
            
            /* Проверяем текущйи статус */
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
            
            $this->flash($msg, $redirect_link, false);
        }
        
        /**
        * Авторизация пользователя.
        */
        public function action_login() {
            /* Получаем объект запроса */
            $request = $this->getRequest();
            
            /* Инициализируем обработчик формы */
            $action = '/users/login/';
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
            
            $udata = (object) $user->getAuth();
            if (isset($udata->role))
            {
                if (Model_User::ROLE_TEACHER == $udata->role)
                {
                    $redirect_link = '/users/index_by_teacher/';
                }elseif (Model_User::ROLE_ADMIN == $udata->role)
                {
                    $redirect_link = '/users/index_by_admin/';
                }elseif (Model_User::ROLE_STUDENT == $udata->role)
                {
                    $redirect_link = '/users/index_by_student/';
                }
            }else
            {
                $redirect_link = '/index/index/';
            }

            /* Если всё удачно, выводим сообщение об успешной авторизации */
            $msg = 'Вы успешно авторизованы';
            $this->flash($msg, $redirect_link);                                         
        }
        
        /**
        * Отображение информации об авторизованном пользователе.
        */
        public function action_whoami() {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            if (isset($udata->role))
            {
                if (Model_User::ROLE_TEACHER == $udata->role)
                {
                    $redirect_link = '/users/index_by_teacher/';
                }elseif (Model_User::ROLE_ADMIN == $udata->role)
                {
                    $redirect_link = '/users/index_by_admin/';
                }elseif (Model_User::ROLE_STUDENT == $udata->role)
                {
                    $redirect_link = '/users/index_by_student/';
                }
            }else
            {
                $redirect_link = '/index/index/';   
            }
            if (false === ($udata = $user->getAuth())) {                               
                $this->flash('Вы не авторизованы', $redirect_link);
            }
            
            $msg = sprintf(
                'Вы авторизованы как %s (%s)',
                $udata['login'], $udata['role']
            );
            $this->flash($msg, $redirect_link, false);
        }
        
        public function action_profile_extended_by_student() {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            
            if ($user->isExtendedProfileSet($udata->user_id)) {
                $this->flash('Ваш профиль уже заполнен', '/users/index_by_student/');
            }
            
            $request = $this->getRequest();
            
            $action = '/users/profile_extended_by_student/';
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
            
            function _toMysqlDate(& $date) {
                $date = date('Y-m-d', strtotime($date));
            }
            
            _toMysqlDate($profile['passport']['birthday']);
            _toMysqlDate($profile['passport']['given_date']);
            
            if (!$user->setExtendedProfile($udata->user_id, $profile)) {
                $this->flash('Ошибка при сохранении профиля', '/users/index_by_student/');
            }
            
            $this->flash('Ваш профиль успешно обновлён', '/users/index_by_student/');
        }
        
        /**
        * Снятие авторизации.
        */
        public function action_logout() {
            $user = Model_User::create();
            
            $user->resetAuth();
            $udata = (object) $user->getAuth();
            if (isset($udata->role))
            {
                if (Model_User::ROLE_TEACHER == $udata->role)
                {
                    $redirect_link = '/users/index_by_teacher/';
                }elseif (Model_User::ROLE_ADMIN == $udata->role)
                {
                    $redirect_link = '/users/index_by_admin/';
                }elseif (Model_User::ROLE_STUDENT == $udata->role)
                {
                    $redirect_link = '/users/index_by_student/';
                }
            }else
            {
                $redirect_link = '/index/index/';
            }            
            $this->flash('Авторизация потеряна', $redirect_link);
        }   
    }

?>