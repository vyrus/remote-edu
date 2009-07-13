<?php

    /* $Id$ */
    
    require_once '../private/init.php';
    
    $_SERVER['REQUEST_URI'] = '/static-route';
    //$_SERVER['REQUEST_URI'] = '/controller/action';
    //$_SERVER['REQUEST_URI'] = '/index/aaction';
    //$_SERVER['REQUEST_URI'] = '/index/protected';
    
    $request = Http_Request::create();
    $router = Resources::getInstance()->router;
    $router->dispatch($request);
        
?>