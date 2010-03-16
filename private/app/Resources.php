<?php

    /* $Id$ */

    /**
    * Класс менеджера ресурсов приложения.
    */
    class Resources extends Resources_Abstract {
        /**
        * Создание экземпляра класса.
        *
        * @param  array $config Массив с конфигурацией.
        * @return Resources
        */
        public static function create(array $config = array()) {
            $instance = new self($config);
            return self::setInstance($instance);
        }

        /**
        * Инициализация диспетчера запросов.
        *
        * @return Mvc_Dispatcher
        */
        protected function get_dispatcher() {
            $config = $this->_config['permissions'];

            $dispatcher = Mvc_Dispatcher::create($config);

            return $dispatcher;
        }

        /**
        * Инициализация роутера.
        *
        * @return Mvc_Router
        */
        protected function get_router() {
            $router = Mvc_Router::create();
            
            $router->addRoutes($this->_config['routes']);
            
            return $router;
        }

        /**
        * Инициализация объекта для работы с БД.
        *
        * @return Db_Pdo
        */
        protected function get_db() {
            $config = $this->_config['db'];

            $db = Db_Pdo::create(
                $config['dsn'], $config['user'], $config['passwd'],
                $config['options']
            );

            $db->exec('SET NAMES utf8');

            return $db;
        }

        /**
        * Инициализация объекта авторизации.
        *
        * @return Auth
        */
        protected function get_auth() {
            $config = $this->_config['auth'];

            $auth = Auth::create($config['salt']);

            return $auth;
        }

        /**
        * Инициализация объекта рассылки почтовых сообщений.
        *
        * @return Postman
        */
        protected function get_postman() {
            $config = $this->_config['postman'];
            
            $postman = Postman::create($config);   
            
            return $postman; 
        }

        /**
        * Инициализация обработчика шаблонов.
        *
        * @return Mvc_View
        */
        protected function get_view() {
            $view = Mvc_View::create(VIEWS . DS, LAYOUTS . DS, ELEMENTS . DS);
            return $view;
        }
        
        /**
        * Инициализация менеджера ссылок.
        * 
        * @return Mvc_Links
        */
        protected function get_links() {
            $links = Mvc_Links::create();
            
            $links->setBaseUrl($this->_config['base_url'])
                  ->addRoutes($this->_config['routes']);
            
            return $links;
        }
    }

?>