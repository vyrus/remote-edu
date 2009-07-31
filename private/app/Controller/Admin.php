<?php
    
    /* $Id:  $ */

    class Controller_Admin extends Mvc_Controller_Abstract {
        public function action_index() {
            $admins = Model_User::create()->showAdmins();
            
            $this->set('admins', $admins);
            $this->render();
        }
                
        /**
        * Регистрация администратора или преподавателя.
        */
        public function action_register() {
            /* Получаем объект запроса */
            $request = $this->getRequest();
            /* Инициализируем обработчик формы */
            $form = Form_Registration::create();
            $form->setAction('/admin/register/'); 
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
                $error = 'Указанное имя пользователя (логин) уже занято';
                $form->setValidationError('login', $error);
                
                $this->render();
            }
            
            /* Берём данные из формы */
            $login  = $form->login->value;
            $passwd = $form->passwd->value;
            //$role  = $form->role->value;
            $role  = 'admin';
            $email = $form->email->value;
            //$surname  = $form->surname->value;
            //$name = $form->name->value;
            //$patronymic = $form->patronymic->value;
            $surname  = '';
            $name = '';
            $patronymic = '';
            
            /* Добавляем в базу */
            $user->register($login, $passwd, $role, $email, $surname, $name, $patronymic);
            
            /* Выводим сообщение */
            $this->flash('Новый пользователь успешно зарегистрирован', '/admin/index/');
        }
    }

?>