<?php

    /* $Id$ */
           
    require_once '../init.php';

    $routes = include 'routes.php';
    $short_routes = array();
    
    $type2const = array(
        'static' => 'Mvc_Router::ROUTE_STATIC',
        'regex'  => 'Mvc_Router::ROUTE_REGEX'
    );
    
    foreach ($routes['routes'] as $route)
    {
        $route = (object) $route;
        $alias = (isset($route->alias) ? $route->alias : '');
        
        $short_route = array();
        $short_route[] = $alias;
        $short_route[] = $type2const[$route->type];
        
        switch ($route->type)
        {
            case Mvc_Router::ROUTE_STATIC;
                $short_route[] = $route->pattern;
                break;
                
            case Mvc_Router::ROUTE_REGEX;
                $short_route[] = $route->pattern['regex'];
                $short_route[] = $route->pattern['params'];
                break;
        }
        
        $short_route[] = $route->handler['controller'];
        $short_route[] = $route->handler['action'];
        
        if (isset($route->handler['params'])) {
            $short_route[] = $route->handler['params'];
        }
        
        $short_routes[] = $short_route;
    }
    
    highlight_string('<?php ' . _export($short_routes) . '; ?>');
    
    function _export($value, $export_array_keys = false) {
        global $type2const;
        
        $code = '';
        
        switch (gettype($value))
        {
            case 'string':
                if (in_array($value, $type2const)) {
                    $code .= $value;
                } else {
                    $code .= '\'';
                    $code .= $value;
                    $code .= '\'';
                }
                break;
                
            case 'array':
                $code .= 'array(';
                foreach ($value as $key => $val)
                {
                    if (!is_numeric($key)) {
                        $code .= _export($key);
                        $code .= ' => ';
                    }
                    
                    $code .= _export($val);
                    
                    end($value);
                    if (key($value) !== $key) {
                        $code .= ', ';
                    }
                }
                $code .= ')';
                break;
        }
        
        return $code;
    }
    
?>