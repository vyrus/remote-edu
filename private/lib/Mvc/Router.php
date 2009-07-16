<?php
    
    /* $Id$ */

    /**
    * Класс маршрутизатора запросов, делегирует обработку запросов контроллерам.
    */
    class Mvc_Router {
        /**
        * Тип маршрута: статичный.
        * 
        * @var const
        */
        const ROUTE_STATIC = 'static';
        
        /**
        * Тип маршрута: на регулярных выражениях.
        * 
        * @var const
        */
        const ROUTE_REGEX  = 'regex';
        
        /**
        * Разделитель, используемый в строке запроса.
        * 
        * @var const
        */
        const URL_DELIMITER = '/';
        
        /**
        * Список маршрутов.
        * 
        * @var array
        */
        protected $_routes = array();
        
        /**
        * Перехватывать ли ошибки 404, когда не найдена запрашиваемая страница
        * (не найдены соответствующий маршрут, контроллер, или действие). Если
        * установлено в false, то будут генерироваться исключения. Если в true,
        * то управление будет передаваться соответствующему обработчичку.
        * 
        * @var boolean
        */
        protected $_catch_error_404 = true;
        
        /**
        * Обработчик для ошибки 404.
        * 
        * @var array
        */
        protected $_error_404_handler = array(
            'controller' => 'error',
            'action'     => '404'
        );
        
        /**
        * Метод-конструктор класса.
        * 
        * @param  array   $routes            Список маршрутов.
        * @param  boolean $catch_error_404   Перехватывать ли ошибку 404.
        * @param  array   $error_404_handler Обработчик для ошибки 404.
        * @return void
        */
        public function __construct
        (
            array $routes = array(), $catch_error_404 = true,
            array $error_404_handler = null
        )
        {                 
            $this->addRoutes($routes);
            $this->catchError404($catch_error_404);
            $this->setError404Handler($error_404_handler);
        }
        
        /**
        * Создание экземпляра класса.
        * 
        * @param  array   $routes            Список маршрутов.
        * @param  boolean $catch_error_404   Перехватывать ли ошибку 404.
        * @param  array   $error_404_handler Обработчик для ошибки 404.
        * @return Mvc_Router
        */
        public static function create
        (
            array $routes = array(), $catch_error_404 = true,
            array $error_404_handler = null
        )
        {
            return new self($routes, $catch_error_404, $error_404_handler);
        }
        
        /**
        * Установка параметра, перехватывать ли ошибку 404.
        * 
        * @param  boolean $value Значение параметра.
        * @return void
        */
        public function catchError404($value) {
            $this->_catch_error_404 = $value;
        }
        
        /**
        * Установка обработчика для ошибки 404.
        * 
        * @param  array $handler Обработчик.
        * @return void.
        */
        public function setError404Handler(array $handler = null) {
            if (null === $handler) {
                return;
            }
            
            $this->_error_404_handler = $handler;
        }
        
        /**
        * Добавление списка маршрутов.
        * 
        * @param  array $routes Список маршрутов.
        * @return void
        */
        public function addRoutes(array $routes = array()) {
            foreach ($routes as $route) {
                $this->addRoute($route['type'], $route['pattern'],
                                $route['handler']);
            }
        }
        
        /**
        * Добавление маршрута.
        * 
        * @param  mixed        $type Тип маршрута.
        * @param  string|array $type Шаблон строки запроса.
        * @return void
        */
        public function addRoute($type, $pattern, $handler) {
            $this->_routes[] = array(
                'type'    => $type,
                'pattern' => $pattern,
                'handler' => $handler
            );
        }
        
        /**
        * Обработка запроса, передача управления контроллеру в соответствии с 
        * заданными маршрутами.
        * 
        * @param  Http_Request $request Объект запроса.
        * @return void
        */
        public function dispatch(Http_Request $request) {
            /**
            * @todo Делать urldecode?
            */
            /* Определяем строку запроса */
            $server = $request->server;
            if (isset($server['REDIRECT_URL'])) {
                $path = strtolower($server['REDIRECT_URL']);
            } else {
                $path = '/';
            }
            
            $params = array();
            
            /* Перебираем список маршрутов на совпадение со строкой запроса */
            foreach ($this->_routes as $route)
            {
                if ($this->_match($path, $route, $params))
                {
                    /**
                    * Если для обработчика не задано параметров, инициализируем
                    * их пустым массивом
                    */
                    if (!isset($route['handler']['params'])) {
                        $route['handler']['params'] = array();
                    }
                    
                    /* Добавляем параметры, полученные из строки запроса */
                    $route['handler']['params'] += $params;
                    /* Делегируем управление контроллеру */               
                    $this->_call($route['handler'], $request);
                    
                    return;
                }
            }
            
            /* Если ни один маршрутов не подошёл, то используем стандартный */
            $this->_defaultRoute($path, $request);
        }
        
        /**
        * Сопоставление строки запроса и маршрута.
        * 
        * @param  string $path  Строка запроса.
        * @param  array $route  Параметры маршрута.
        * @param  array $params Параметры, полученные из строки запроса.
        * @return boolean
        */
        protected function _match($path, array $route, array & $params) {
            /* В зависимости от типа маршрута, вызываем соответствующий метод */
            switch ($route['type'])
            {
                case self::ROUTE_STATIC:
                    return $this->_matchStatic(
                        $path, $route['pattern'], $params
                    );
                    break;
                
                case self::ROUTE_REGEX:
                    $regex = $route['pattern']['regex'];
                    $names = $route['pattern']['params'];
                    
                    return $this->_matchRegex($path, $regex, $names, $params);
                    break;
                    
                default:
                    throw new Mvc_Router_Exception(
                        sprintf('Неизвестный тип маршрута "%s"', $route['type'])
                    );
                    break;
            }
        }
        
        /**
        * Сопоставление статичного маршрута с запросом.
        * 
        * @param  string $path    Строка запроса.
        * @param  string $pattern Шаблон запроса для маршрута.
        * @param  array  $params  Параметры, полученные из строки запроса.
        * @return boolean
        */
        protected function _matchStatic($path, $pattern, array & $params) {
            /**
            * Параметры не разбираются для статичных маршрутов, поэтому
            * возвращаем пустой массив
            */
            $params = array();
            
            /* Обрезаем с конца строки символ разделителя */
            $path = rtrim($path, self::URL_DELIMITER);
            /* Вырезаем из строки запроса подстроку с длиной шаблона */
            $pattern_len = strlen($pattern);
            $path = substr($path, 0, $pattern_len);
            
            /* Если начало запроса совпадает с шаблоном, то... */    
            if ($path == $pattern) {                         
                /* засчитываем совпадение, игнорируя оставшуюся часть запроса */
                return true;
            }
            
            return false;
        }
        
        /**
        * Сопоставление маршрута на регулярном выражении с запросом.
        * 
        * @param  string $path   Строка запроса.
        * @param  string $regex  Шаблон маршрута.
        * @param  array  $names  Имена вырезаемых из запроса параметров.
        * @param  array  $params Параметры, полученные из строки запроса.
        * @return boolean
        */
        protected function _matchRegex
        (
            $path, $regex, array $names = array(),  array & $params
        )
        {
            /* Сравниваем регулярное выражение со строкой запроса */
            $regex = '#^' . $regex . '$#i';
            $num_matches = preg_match($regex, $path, $matches);
            
            /* Если строка запроса не совпала с регулярным выражением, то... */
            if ($num_matches < 1) {
                /* не засчитываем и совпадение маршрута с запросом */
                return false;
            }
            
            $params = array();
            
            /* Заполняем массив именованных параметров */
            array_shift($matches);
            foreach ($matches as $key => $param) {
                $key = (isset($names[$key]) ? $names[$key] : $key);
                $params[$key] = $param;
            }
            
            return true;
        }
        
        /**
        * Определение обработчки для запроса в соответствии со стандартным
        * маршрутом (/контроллер/действие/параметры).
        * 
        * @param  string       $path    Строка запроса.
        * @param  Http_Request $request Объект запроса.
        * @return void
        */
        protected function _defaultRoute($path, Http_Request $request) {
            /* Удаляем разделитель с концов строки запроса */
            $path = trim($path, self::URL_DELIMITER);
            
            /* Если в ней ещё что-нибудь осталось... */
            if (strlen($path)) {
                /* разбиваем строку на части. */
                $path = explode(self::URL_DELIMITER, $path);
            } else {
                /* Иначе добавляем параметры обработчика по умолчанию */
                $path = array('index', 'index');
            }
            
            
            /** 
            * Если в массиве осталось меньше двух элементов (один - контроллер,
            * второй - действие), то вызываем обработку ошибки 404.
            */
            if (sizeof($path) < 2)
            {
                $msg = 'Контроллер/действие не определены';
                $this->_error_404($msg, $request);
            }
            
            /* Иначе, создаём массив с параметрами обработчика */
            $handler = array(
                /* Первый - контроллер */
                'controller' => array_shift($path),
                /* Второй - действие */
                'action' => array_shift($path),
                /* Третий - параметры запроса */
                'params' => $path
            );
            
            /* Вызываем обработчик */
            $this->_call($handler, $request);
        }
        
        /**
        * Вызов обработчика.
        * 
        * @param  array        $handler Параметры обработчика.
        * @param  Http_Request $request Объект запроса.
        * @return void
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
            
            /* Если такой класс не найден - ошибка 404 */
            if (!class_exists($class /* $autoload = true */))
            {
                $msg = sprintf('Не найден класс контроллера "%s"', $class);
                $this->_error_404($msg, $request);
            }
            
            /* Определяем нужный метод в классе контроллера */
            $method = 'action_' . $handler['action'];
            
            /* Если такой метод недоступен - ошибка 404 */
            if (!$this->_isCallable($class, $method))
            {
                $msg = 'Не найден метод "%s" в контроллере "%s"';
                $msg = sprintf($msg, $method, $class);
                $this->_error_404($msg, $request);
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
        * @throws Mvc_Router_Exception Если не найден обработчик.
        */
        protected function _error_404($reason, Http_Request $request) {
            if (
                /* Если выключен перехват ошибки 404 */
                !$this->_catch_error_404 ||
                /* или если она произошла при вызове обработчика самой ошибки */
                (
                    isset($request->_router) &&
                    $this->_error_404_handler === $request->_router['handler']
                )
            )
            {
                /* Генерируем исключение */
                throw new Mvc_Router_Exception($reason);
            }
            
            /* Иначе вызываем установленный обработчик для ошибки */
            $this->_call($this->_error_404_handler, $request);
        }
    }

?>