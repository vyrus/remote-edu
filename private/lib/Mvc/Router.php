<?php
    
    /* $Id$ */

    class Mvc_Router {
        const ROUTE_STATIC = 'static';
        const ROUTE_REGEX  = 'regex';
        
        const URL_DELIMITER = '/';
        
        protected $_routes = array();
        
        public function __construct(array $routes = array()) {
            $this->addRoutes($routes);
        }
        
        public static function create(array $routes = array()) {
            return new self($routes);
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
                throw new Mvc_Router_Exception(
                    'Контроллер/действие не определены'
                );
            }
            
            $handler = array(
                'controller' => array_shift($path),
                'action'     => array_shift($path),
                'params'     => $path
            );
            
            $this->_call($handler, $request);
        }
        
        protected function _call(array $handler, Http_Request $request) {
            $class = 'Controller_' . ucfirst($handler['controller']);
            
            if (!class_exists($class /* $autoload = true */))
            {
                throw new Mvc_Router_Exception(
                    sprintf('Не найден класс контроллера "%s"', $class)
                );
            }
            
            $method = 'action_' . $handler['action'];
            
            if (!$this->_isCallable($class, $method))
            {
                $msg = 'Не найден метод "%s" в контроллере "%s"';
                $msg = sprintf($msg, $method, $class);
                throw new Mvc_Router_Exception($msg);
            }
            
            $controller = new $class($request);
            $callback = array($controller, $method);
            $params = array($handler['params']);
            call_user_func_array($callback, $params);
        }
        
        protected function _isCallable($class, $method) {
            return in_array($method, get_class_methods($class));
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