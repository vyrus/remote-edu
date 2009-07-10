<?php                               

    /* $Id$ */
    
    return array(
        'routes' => array
        (
            array
            (
                'type'    => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/static-route',
                'handler' => array
                (
                    'controller' => 'index',
                    'action'     => 'static',
                    'params'     => array()
                )
            ),
            
            array
            (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/regex-route-([0-9]+).*',
                    'params' => array('number')
                ),
                'handler' => array
                (
                    'controller' => 'index',
                    'action'     => 'regex',
                    'params'     => array('default-param' => 'default-value')
                )
            )
        )
    );
            
?>