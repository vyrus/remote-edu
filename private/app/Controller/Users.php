<?php
    
    /* $Id$ */

    /**
    * Обработка действий, связанных с пользователями.
    * 
    * @param
    * @return
    */
    class Controller_Users extends Mvc_Controller_Abstract {
        /**
        * Карта для отображения роллей из select'а во внутреннее обозначение.
        * 
        * @var array
        */
        protected $_roles_map = array(
            'teacher' => Model_User::ROLE_TEACHER,
            'admin'   => Model_User::ROLE_ADMIN
        );
        
        public function action_index() {
            $this->render();
        }
        
        /**
        * Регистрация нового слушателя (первичная).
        */
        public function action_register_student() {
            $request = $this->getRequest();
            
            /* Создаём объект формы с полями первичной регистрации */
            $action = '/users/register_student/';
            $form = Form_Registration_Student::create(
                $action, Form_Registration_Student::TYPE_MINIMAL
            );
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
            $this->flash($msg, '/users/index/');
        }
        
        /**
        * Активация аккаунта слушателя.
        * 
        * @param array $params array(user_id, code).
        */
        public function action_activate_student(array $params = array()) {
            $user = Model_User::create();
            $id = $params['user_id'];
            
            /* Проверяем статус пользователя */
            $status = $user->getStatus($id);

            if (Model_User::STATUS_INACTIVE !== $status) {
                $this->flash('Аккаунт уже активирован', '/users/index/');
            }
            
            /* Проверяем код активации */
            $code = $user->getActivationCode($id);

            if ($params['code'] !== $code) {
                $this->flash('Неправильный код активации', '/users/index/', false);
            }
            
            /* Активируем слушателя */
            $user->activateStudent($id);
            
            $msg = 'Аккаунт успешно активирован';
            $this->flash($msg, '/users/index/', false);
        }
        
        /**
        * Регистрация сотрудника (преподавателя/администратора).
        */
        public function action_register_employee() {
            $request = $this->getRequest();
            
            /* Создаём объект формы с полями первичной регистрации */
            $action = '/users/register_employee/';
            $form = Form_Registration_Employee::create($action);
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
                                                         
            $this->flash('Новый сотрудник успешно добавлен', '/users/index/');
        }
        
        /**
        * Активация аккаунта сотрудника (преподавателя/администатора).
        * 
        * @param array $params array(user_id, code).
        */
        public function action_activate_employee(array $params = array()) {
            $user = Model_User::create();
            $id = $params['user_id'];
            
            /* Проверяем текущйи статус */
            $status = $user->getStatus($id);

            if (Model_User::STATUS_INACTIVE !== $status) {
                $this->flash('Аккаунт уже активирован', '/users/index/');
            }
            
            /* Сверяем код активации */
            $code = $user->getActivationCode($id);

            if ($params['code'] !== $code) {
                $this->flash('Неправильный код активации', '/users/index/', false);
            }
            
            /* Генерируем пароль для аккаунта и обновляем данные в БД */
            $passwd = $user->generatePassword();
            $user->activateEmployee($id, $passwd);
            
            $msg = 'Аккаунт успешно активирован, ваш пароль <strong>' . $passwd . '</strong>';
            $this->flash($msg, '/users/index/', false);
        }
        
        /**
        * Авторизация пользователя.
        */
        public function action_login() {
            /* Получаем объект запроса */
            $request = $this->getRequest();
            /* Инициализируем обработчик формы */
            $form = Form_Login::create();
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
            
            /* Если всё удачно, выводим сообщение об успешной авторизации */
            $msg = 'Вы успешно авторизованы';
            $this->flash($msg, '/users/index/');                                         
        }
        
        /**
        * Отображение информации об авторизованном пользователе.
        */
        public function action_whoami() {
            $user = Model_User::create();
                    
            if (false === ($udata = $user->getAuth())) {                               
                $this->flash('Вы не авторизованы', '/users/index/');
            }
            
            $msg = sprintf(
                'Вы авторизованы как %s (%s)',
                $udata['login'], $udata['role']
            );
            $this->flash($msg, '/users/index/', false);
        }
        
        /**
        * Снятие авторизации.
        */
        public function action_logout() {
            $user = Model_User::create();
            $user->resetAuth();
            
            $this->flash('Авторизация потеряна', '/users/index/');
        }   
    }

?>