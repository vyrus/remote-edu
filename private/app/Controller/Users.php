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
        * Запрос восстановление пароля.
        */
        public function action_restore_passwd() {
            /* Берём менеджер ссылок */
            $links = Resources::getInstance()->links;
            
            /* Получаем ссылку на страницу восстановления пароля */
            $action = $links->get('users.restore-passwd');
            /* Инициализируем форму */
            $form = Form_Profile_RestorePasswd::create($action);
            
            /* Передаём форму в шаблон */
            $this->set('form', $form);
            
            $request = $this->getRequest();
            $method = $form->method();
            
            /* Если данных от формы нет, выводим страницу */
            if (empty($request->$method)) {
                $this->render();
            }

            /* Создаём модель для работы с пользователями */
            $user = Model_User::create();
            
            /* Если в форме что-то не заполнено, просим исправить */
            if (!$form->validate($request, $user)) {
                $this->render();
            }
            
            /* Получаем данные пользователя */
            $udata = (object) $user->getInfoByLogin($form->login->value);
            
            /* Генерируем код восстановления */
            $code = $user->getPasswdRestoreCode($udata->user_id);

            /* И отправляем его пользователю */
            $postman = Resources::getInstance()->postman;
            $postman->sendPasswdRestore(
                $udata->user_id, $udata->email, $code
            );
            
            
            $msg = 'Ссылка для восстановления пароля отправлена на указанный ' .
                   'при регистрации адрес электронной почты. Она будет ' . 
                   'действовать только в течении текущего дня.';
            $link = $links->get('users.login');
            
            $this->flash($msg, $link, false);
        }
        
        /**
        * Смена пароля.
        */
        public function action_reset_passwd(array $params) {
            $params = (object) $params;
            
            /* Берём менеджер ссылок */
            $links = Resources::getInstance()->links;
            
            /* Создаём модель пользователя */
            $user = Model_User::create();
            /* Генерируем защитный код */
            $code = $user->getPasswdRestoreCode($params ->user_id);
            
            /* Проверяем код */
            if ($code != $params->code) {
                $msg = 'Ошибка: недейстительная ссылка для смены пароля'; 
                $link = $links->get('users.restore-passwd');
                
                $this->flash($msg, $link, false);
            }
            
            $opts = array('user_id' => $params->user_id,
                          'code'    => $params->code);
            
            /* Получаем ссылку на страницу смены пароля */
            $action = $links->get('users.reset-passwd', $opts);
            
            /* Инициализируем форму */
            $form = Form_Profile_ResetPasswd::create($action);
            $this->set('form', $form);
            
            $request = $this->getRequest();
            $method  = $form->method();
            
            /* Если данных от формы нет, выводим страницу */
            if (empty($request->$method)) {
                $this->render();
            }
            
            /* Если данные не прошли проверку, выводим ошибки */
            if (!$form->validate($request)) {
                $this->render();
            }
            
            $uid    = $params->user_id;
            $passwd = $form->new_passwd->value;
            
            /* Обновляем пароль */
            $result = $user->setPasswd($uid, $passwd);
            
            if ($result) {
                $msg = 'Пароль успешно изменён';
                $link = $links->get('users.login');
            } else {
                $msg = 'Не удалось изменить пароль';
                $link = $links->get('users.reset-passwd', $opts);
            }
            
            /* Выводим сообщение, удалось ли сменить пароль или нет */
            $this->flash($msg, $link, false);
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
        
        /**
        * Редактирование расширенного профиля слушателя.
        */
        public function action_profile_extended_by_student() {
            /* Подгружаем менеджер ссылок */
            $links = Resources::getInstance()->links;
            
            /* Создаём экземпляры необходимых моделей */
            $user     = Model_User::create();
            $region   = Model_Region::create();
            $locality = Model_Locality::create();
            
            /* Получаем данные пользователя */
            $udata = (object) $user->getAuth();
            
            /* Создаём объект формы расширенного профиля */
            $action = $links->get('student.extended-profile');
            $form = Form_Profile_Student_Extended::create($action);
            $this->set('form', $form);
            
            $request = $this->getRequest();
            $method = $form->method();
            
            /* Если данных формы нет в запросе, */
            if (empty($request->$method))
            {
                /* и профиль слушателя уже заполнен, */
                if ($user->isExtendedProfileSet($udata->user_id))
                {
                    /* то загружаем данные базового профиля */
                    $info = $user->getUserInfo($udata->user_id);
                    
                    /* И выводим их в форме */
                    $form->setValue('surname',    $info['surname']);
                    $form->setValue('name',       $info['name']);
                    $form->setValue('patronymic', $info['patronymic']);
                    
                    /* Загружаем расширенный профиль */
                    $profile = $user->getExtendedProfile($udata->user_id);
                    
                    /* И наполняем форму данными из него */
                    $profile->passport->toForm($form);
                    $profile->edu_doc->toForm($form);
                    $profile->phones->toForm($form);
                    
                    /* Получаем по идентификаторам название региона и города */
                    $r_name = $region->getName($profile->passport->regionId);
                    $l_name = $locality->getFullName(
                        $profile->passport->cityId
                    );
                    
                    /* Передаём их в форму */
                    $form->setValue('region', $r_name);
                    $form->setValue('city',   $l_name);
                }
                
                /* Отображаем страничку с формой */
                $this->render();
            }
            
            /* Если же в запросе содержатся заполненные поля формы, */
            if (!$form->validate($request, $region, $locality)) {
                /* проверяем их и выводим ошибки */
                $this->render();
            }
            
            /* Если данные прошли проверку, заполняем ими контейнеры */
            $snp = (object) array('surname'    => $form->surname->value, 
                                  'name'       => $form->name->value, 
                                  'patronymic' => $form->patronymic->value);
            
            $passport = Model_User_Passport::create()->fromForm($form);
            $edu_doc  = Model_User_EduDoc::create()->fromForm($form);
            $phones   = Model_User_Phones::create()->fromForm($form);
            
            /* Обновляем фамилию-имя-отчество */
            $user->updateSNP($udata->user_id, $snp);
            
            /* Сохраняем паспортные данные */
            if(!$user->savePassport($udata->user_id, $passport)) {
                $msg = 'Ошибка при сохранении паспортных данных';
                $alias = 'student.extended-profile';
                
                $this->flash($msg, $links->get($alias), false);
            }
            
            /* Если в форме нет данных о документе об образовании, */
            if (empty($edu_doc->type)) {
                /* то удаляем возможные записи, если раньше они были внесены */
                $user->deleteEduDoc($udata->user_id);
            }
            /* Если же есть данные о документе, сохраняем их */
            elseif (!$user->saveEduDoc($udata->user_id, $edu_doc)) {
                $msg = 'Ошибка при сохранении данных документа об образовании';
                $alias = 'student.extended-profile';
                
                $this->flash($msg, $links->get($alias), false);
            }
            
            /* Если в форме нет данных о телефонах */
            if (empty($phones->mobile) && empty($phones->stationary)) {
                /* удаляем возможные записи из базы */
                $user->deletePhones($udata->user_id);
            }
            /* Иначе сохраняем новые телефоны */
            elseif (!$user->savePhones($udata->user_id, $phones))
            {
                $msg = 'Ошибка при сохранении телефонов';
				$alias = 'student.extended-profile';
                
                $this->flash($msg, $links->get($alias), false);
            }
                               
            /* Выводим сообщение об успешном редактировании профиля */
            $this->flash('Ваш профиль успешно обновлён', 
                         $links->get('student.apply'), false);
        }

        /**
        * Просмотр расширенного профиля слушателя.
        */
        public function action_view_profile($params) {
            /* Получаем из параметров запроса идентификатор пользователя */
            $user_id = $params['user_id'];
            
            /* Получаем базовый профиль пользователя */
            $user = Model_User::create();
            $base_profile = (object) $user->getUserInfo($user_id);
            
            /* Если пользователь - не слушатель, */
            if (Model_User::ROLE_STUDENT !== $base_profile->role) {
                /* отказываемся выводить профиль :) */
                $msg  = 'Невозможно отобразить профиль, так как указанный ' . 
                        'пользователь не является слушателем';
                $alias = 'admin.applications';
                
                $link = Resources::getInstance()->links->get($alias);
                $this->flash($msg, $link, false);
            }
            
            /* Получаем данные расширенного профиля */
            $ex_profile = $user->getExtendedProfile($user_id);
            
            /* И скармливаем всё представлению */
            $this->set('base_profile', $base_profile);
            $this->set('ex_profile',   $ex_profile);
            $this->render();
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
        
        public function action_edit_account($params) { 
            $links = Resources::getInstance()->links;

            $opts = array('user_id' => $params['user_id']);
            $action = $links->get('users.edit', $opts);
            $form = Form_Profile_Edit::create($action);

            $users = Model_User::create();
            $this->set('form', $form);
            $this->set('rolesCaptions', $this->_roles_captions);
            $request = $this->getRequest();
            $method = $form->method();
            $requestData = $request->$method;

            if (empty($requestData)) {
                if (($userInfo = $users->getUserInfo($params['user_id'])) === FALSE) {
                    $this->flash('Пользователь не существует',
                                 $links->get('users.list'), 10);
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
                $this->flash('Данные пользователя успешно изменены',
                             $links->get('users.list'), 10);
            }

            $this->render('users/edit_account');
        }
    }
?>