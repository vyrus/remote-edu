<?php

    /* $Id$ */
    
    require_once '../private/init.php';
     
    $request = Http_Request::create();
    $router = Resources::getInstance()->router;
    $router->dispatch($request);
        
?>