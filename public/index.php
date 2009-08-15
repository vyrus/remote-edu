<?php
    
    /* $Id$ */
    
    require_once '../private/init.php';
    
    /* Инициализируем объект запроса */ 
    $request = Http_Request::create();
    /* Загружаем роутер */
    $router = Resources::getInstance()->router;
    /* Обрабатывем запрос, передаём управление соответствующему контроллеру */
    $router->dispatch($request);
?>