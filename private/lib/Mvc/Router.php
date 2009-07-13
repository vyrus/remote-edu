<?php
    
    /* $Id$ */

    class Mvc_Router {
        const ROUTE_STATIC = 'static';
        const ROUTE_REGEX  = 'regex';
        
        const URL_DELIMITER = '/';
        
        protected $_routes = array();
        
        protected $_catch_error_404 = true;
        
        protected $_error_404_handler = array(
            'controller' => 'Error',
            'action'     => '404',
            'params'     => array()
        );
        
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
        
        public static function create
        (
            array $routes = array(), $catch_error_404 = true,
            array $error_404_handler = null
        )
        {
            return new self($routes, $catch_error_404, $error_404_handler);
        }
        
        public function catchError404($value) {
            $this->_catch_error_404 = $value;
        }
        
        public function setError404Handler(array $handler = null) {
            if (null === $handler) {
                return;
            }
            
            $this->_error_404_handler = $handler;
        }
        
        public function addRoutes(array $routes = array()) {
            foreach ($routes as $route) {
                $this->addRoute($route['type'], $route['pattern'],
                                $route['handler']);
            }
        }
        
        public function addRoute($type, $pattern, $handler) {
            $this->_routes[] = array(
                'type'    => $type,
                'pattern' => $pattern,
                'handler' => $handler
            );
        }
        
        public function dispatch(Http_Request $request) {
            /**
            * @todo Делать urldecode?
            */
            $path = $request->server['REQUEST_URI'];
            $params = array();
            
            foreach ($this->_routes as $route)
            {
                if ($this->_match($path, $route, $params))
                {
                    $route['handler']['params'] += $params;
                    $this->_call($route['handler'], $request);
                    
                    return;
                }
            }
            
            $this->_defaultRoute($path, $request);
        }
        
        protected function _defaultRoute($path, Http_Request $request) {
            $path = trim($path, self::URL_DELIMITER);
            
            if (strlen($path)) {
                $path = explode(self::URL_DELIMITER, $path);
            } else {
                $path = array('index', 'index');
            }
            
            if (sizeof($path) < 2)
            {
                $msg = 'Контроллер/действие не определены';
                $this->_error_404($msg, $request);
            }
            
            $handler = array(
                'controller' => array_shift($path),
                'action'     => array_shift($path),
                'params'     => $path
            );
            
            $this->_call($handler, $request);
        }
        
        protected function _call(array $handler, Http_Request $request) {
            if (!isset($request->_router)) {
                $request->_router = array();
            }
            $request->_router['handler'] = $handler; 
            
            $class = 'Controller_' . ucfirst($handler['controller']);
            
            if (!class_exists($class /* $autoload = true */))
            {
                $msg = sprintf('Не найден класс контроллера "%s"', $class);
                $this->_error_404($msg, $request);
            }
            
            $method = 'action_' . $handler['action'];
            
            if (!$this->_isCallable($class, $method))
            {
                $msg = 'Не найден метод "%s" в контроллере "%s"';
                $msg = sprintf($msg, $method, $class);
                $this->_error_404($msg, $request);
            }
            
            $controller = new $class($request);
            $callback = array($controller, $method);
            $params = array();
            if (!empty($handler['params'])) {
                $params[] = $handler['params'];
            }
            
            call_user_func_array($callback, $params);
        }
        
        protected function _isCallable($class, $method) {
            return in_array($method, get_class_methods($class));
        }
        
        protected function _error_404($reason, Http_Request $request) {
            if (
                !$this->_catch_error_404 ||
                (
                    isset($request->_router) &&
                    $this->_error_404_handler === $request->_router['handler']
                )
            )
            {
                throw new Mvc_Router_Exception($reason);
            }
            
            $this->_call($this->_error_404_handler, $request);
        }
        
        protected function _match($path, array $route, & $params) {
            switch ($route['type'])
            {
                case self::ROUTE_STATIC:
                    return $this->_matchStatic($path, $route['pattern']);
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
        
        protected function _matchStatic($path, $pattern) {
            $path = rtrim($path, self::URL_DELIMITER);
            $pattern_len = strlen($pattern);
            $path = substr($path, 0, $pattern_len);
                
            if ($path == $pattern) {
                return true;
            }
            
            return false;
        }
        
        protected function _matchRegex($path, $regex, $names,  array & $params) {
            $regex = '#^' . $regex . '$#i';
            $num_matches = preg_match($regex, $path, $matches);
            
            if ($num_matches < 1) {
                return false;
            }
            
            $params = array();
            
            array_shift($matches);
            foreach ($matches as $key => $param) {
                $key = (isset($names[$key]) ? $names[$key] : $key);
                $params[$key] = $param;
            }
            
            return true;
        }
    }

?>