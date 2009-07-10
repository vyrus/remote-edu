<?php

    /* $Id$ */
    
    require_once '../private/init.php';
    
    $storage = array(
        'get'    => $_GET,
        'post'   => $_POST,
        'cookie' => $_COOKIE,
        'files'  => $_FILES,
        'server' => $_SERVER
    );
    $request = Http_Request::create($storage);
    
    Mvc_Router::create($config['routes'])
        ->dispatch($request);
        
?>