<?php
    
    /* $Id$ */

    /**
    * 
    */
    class Mvc_Dispatcher {
        /**
        * Ошибка вызова обработчика: нет ошибок.
        * 
        * @var const
        */
        const ERROR_SUCCESS = 'error-success';
        
        /**
        * Ошибка вызова обработчика: контроллер не найден.
        * 
        * @var const
        */
        const ERROR_CONTROLLER_NOT_FOUND = 'error-controller-not-found';
        
        /**
        * Ошибка вызова обработчика: действие не найдено.
        * 
        * @var const
        */
        const ERROR_ACTION_NOT_FOUND = 'error-action-not-found';
        
        /**
        * Перехватывать ли различные ошибки. Если установлено в false, то
        * будут генерироваться исключения. Если в true, то управление будет
        * передаваться соответствующему обработчичку.
        * 
        * @see self::$_error_handler
        * 
        * @var boolean
        */
        protected $_catch_errors = array(
            404 => true
        );
        
        /**
        * Обработчики для ошибок.
        * 
        * @var array
        */
        protected $_error_handlers = array
        (
            404 => array(
                'controller' => 'error',
                'action'     => '404'
            ),
        );
        
        /**
        * Метод-конструктор класса.
        * 
        * @return void
        */
        public function __construct() {/*_*/}
        
        /**
        * Создание экземпляра класса.
        * 
        * @return Mvc_Router
        */
        public static function create() {
            return new self();
        }
        
        /**
        * Установка параметра, перехватывать ли заданную ошибку.
        * 
        * @param  int     $e_code Код ошибки.
        * @param  boolean $value  Значение параметра.
        * @return void
        */
        public function catchError($e_code, $value) {
            $this->_catch_errors[$e_code] = $value;
        }
        
        /**
        * Установка обработчика для заданной ошибки.
        *
        * @param  int   $e_code  Код ошибки. 
        * @param  array $handler Обработчик.
        * @return void.
        */
        public function setErrorHandler($e_code, array $handler = null) {
            if (null === $handler) {
                return;
            }
            
            $this->_error_handlers[$e_code] = $handler;
        }
        
        /**
        * Определяет с помощью роутера обработчик и пытается вызывать его.
        * 
        * @param  $request Http_Request Объект запроса.
        * @return boolean
        */
        public function dispatch(Http_Request $request) {
            /* Получаем объект роутера */
            $router = Resources::getInstance()->router;
            
            /* Если не получается определить обработчик, то генерируем ошибку */
            if (false === ($handler = $router->dispatch($request)))
            {
                $msg = 'Контроллер/действие не определены';
                $this->_error_404($msg, $request);
                
                return false;
            }
            
            /* Пытаемся вызывать обработчик и обрабатываем возможные ошибки */
            if (self::ERROR_SUCCESS === ($r = $this->_call($handler, $request)))
            {
                return true;
            }
            /* Контроллер не найден */
            elseif (self::ERROR_CONTROLLER_NOT_FOUND === $r->error_code)
            {
                $msg = sprintf('Не найден класс контроллера "%s"', $r->class);
                $this->_error_404($msg, $request);
                
                return false;
            }
            /* Действие не найдено */
            elseif (self::ERROR_ACTION_NOT_FOUND === $r->error_code)
            {
                $msg = 'Не найден метод "%s" в контроллере "%s"';
                $msg = sprintf($msg, $r->method, $r->class);
                $this->_error_404($msg, $request);
                
                return false;
            }
        }
        
        /**
        * Вызов обработчика. При ошибке возвращает экземпляр stdClass'а с
        * параметрами ошибки. Либо self::ERROR_SUCCESS, если повезёт =).
        * 
        * @param  array        $handler Параметры обработчика.
        * @param  Http_Request $request Объект запроса.
        * @return mixed
        */
        protected function _call(array $handler, Http_Request $request) {
            /* Если в объекте-запросе нет раздела роутера, создаём его */
            if (!isset($request->_router)) {
                $request->_router = array();
            }
            /* Сохраняем для запроса назначенный обработчик */
            $request->_router['handler'] = $handler; 
            
            /* Определяем название класса контроллера */
            $class = 'Controller_' . ucfirst($handler['controller']);

            /* Если такой класс не найден, возвращаем ошибку */
            if (!class_exists($class /* $autoload = true */))
            {
                /* Заполняем объект параметрами ошибки */
                $r = new stdClass();        
                $r->error_code = self::ERROR_CONTROLLER_NOT_FOUND;
                $r->class = $class;
                
                return $r;
            }
            
            /* Определяем нужный метод в классе контроллера */
            $method = 'action_' . $handler['action'];
            
            /* Если такой метод недоступен, возвращаем ошибку */
            if (!$this->_isCallable($class, $method))
            {
                /* Заполняем объект параметрами ошибки */
                $r = new stdClass();
                $r->error_code = self::ERROR_ACTION_NOT_FOUND;
                $r->class = $class;
                $r->method = $method;
                
                return $r;
            }

            /* Инициализируем контроллер */
            $controller = new $class($request);
            
            /* Настраиваем параметры вызова */
            $callback = array($controller, $method);
            $params = array();
            
            if (!empty($handler['params'])) {
                $params[] = $handler['params'];
            }
            
            /* Вызываем обработчик */
            call_user_func_array($callback, $params);
            
            return self::ERROR_SUCCESS;
        }
        
        /**
        * Определение доступности метода в классе. Метод доступен, если он объявлен
        * в коде класса со спецификатором public.
        * 
        * @param  string $class  Название класса.
        * @param  string $method Название метода.
        * @return boolean
        */
        protected function _isCallable($class, $method) {
            return in_array($method, get_class_methods($class));
        }
        
        /**
        * Обработка ошибки 404.
        * 
        * @param  string       $reason  Причина возникновения ошибки.
        * @param  Http_Request $request Объект обрабатываемого запроса.
        * @return void
        * @throws Mvc_Router_Exception Не найден или не установлен обработчик.
        */
        protected function _error_404($reason, Http_Request $request) {
            /* Если выключен перехват ошибки */
            if (!$this->_catch_errors[404]) {
                /* Генерируем исключение */
                throw new Mvc_Router_Exception($reason);
            }
            
            /* Вызываем обработчик ошибки */
            $r = $this->_call($this->_error_handlers[404], $request);
            
            /* Если не удалось вызывать обработчик */
            if (self::ERROR_SUCCESS !== $r) {
                /* Генерируем исключение */
                throw new Mvc_Router_Exception($reason);
            }
        }
    }

?>