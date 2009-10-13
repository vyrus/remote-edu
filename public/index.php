<?php
    
    /* $Id$ */
    
    /* commit test */
    
    require_once '../private/init.php';
    
    /* Инициализируем объект запроса */ 
    $request = Http_Request::create();    
    
    /* Инициализируем диспетчер */
    $dispatcher = Resources::getInstance()->dispatcher;
    
    /* Обрабатываем запрос */
    $dispatcher->dispatch($request);
    
?>