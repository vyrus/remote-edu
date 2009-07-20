<?php
    
    /* $Id$ */

    class Controller_Users extends Mvc_Controller_Abstract {
        public function action_index() {
            $this->render();
        }
                
        public function action_show() {
            $users = Model_User::create()->showAll();
            
            $this->set('users', $users);
            $this->render();
        }
        
        /**
        * Регистрация цользователя.
        */
        public function action_register() {
            /* Получаем объект запроса */
            $request = $this->getRequest();
            /* Инициализируем обработчик формы */
            $form = Form_Registration::create();
            $this->set('form', $form);
            
            /* Если данные для формы не пришли, то просто показываем форму */
            if (empty($request->{$form->method()})) {
                $this->render();
            } 
            
            /**
            * Если форма заполнена некорректно, снова показываем форму и просим
            * исправить
            */
            if (!$form->validate($request)) {
                $this->render();
            }
              
            $user = Model_User::create();
            
            /* Если такой пользователь уже есть, просим выбрать другой логин */
            if ($user->exists($form->login->value)) {
                $form->invalidate();
                $error = 'Указанное имя пользователя уже занято';
                $form->setValidationError('login', $error);
                
                $this->render();
            }
            
            /* Берём данные из формы */
            $login  = $form->login->value;
            $passwd = $form->passwd->value;
            $fio    = $form->fio->value;
            
            /* Добавляем в базу */
            $user->register($login, $passwd, $fio);
            
            /* Выводим сообщение */
            $this->flash('Вы успешно зарегистрированы', '/users/index/');
        }
        
        /**
        * Авторизация.
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
            if (false === ($user_data = $user->login($login, $passwd))) {
                /* А если нет, выводим соответствующую ошибку */
                $form->setValue('login',  '');
                $form->setValue('passwd', '');
                
                $error = 'Пользователь с такими именем и паролем не найден';
                $this->set('error', $error);
                
                $this->render();
            }
            
            /* Если всё удачно, выводим сообщение об успешной авторизации */
            $msg = 'Вы авторизованы как пользователь ' . $user_data['fio'];
            $this->flash($msg, '/users/index/');
        }
        
        /**
        * Отображение информации об авторизованном пользователе.
        */
        public function action_whoami() {
            $auth = Resources::getInstance()->auth;
            $user = $auth->init()->getUser();
            
            $msg = vsprintf('Вы авторизованы как %s, %s', $user);
            $this->flash($msg, '/users/index/', false);
        }
    }

?>