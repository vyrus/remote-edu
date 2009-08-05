<?php                               

    /* $Id$ */
    
    return array(
        /* Режим работы (debug/production) */
        'mode' => 'debug',
        
        /* Базовый адрес */
        'base_url' => 'http://remote-edu',
        
        /* Настройки соединения с БД */
        'db' => array
        (
            'dsn'     => 'mysql:host=localhost;dbname=remote-edu',
            'user'    => 'root',
            'passwd'  => '',
            'options' => array(
                /* Будет кидать исключения при ошибках */
                Db_Pdo::ATTR_ERRMODE => Db_Pdo::ERRMODE_EXCEPTION
            )
        ),
        
        /* Настройки авторизации */
        'auth' => array
        (
            /* Случайная последовательность символов для шифрования */
            'salt' => 'Ix8i8AQrEfFtgi14XupbT4kxHM511ZDFA'
        ),
        
        /* Маршруты */
        'routes' => array
        (
            /* Активация слушателя */
            array(
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/activate_student/([0-9]+)/([0-9a-z]{32}).*',
                    'params' => array('user_id', 'code')
                ),
                'handler' => array
                (
                    'controller' => 'users',
                    'action'     => 'activate_student'
                )
            ),
            
            /* Активация сотрудника */
            array(
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/activate_employee/([0-9]+)/([0-9a-z]{32}).*',
                    'params' => array('user_id', 'code')
                ),
                'handler' => array
                (
                    'controller' => 'users',
                    'action'     => 'activate_employee'
                )
            )
        ),
        
        /* Настройка отправки почтовых сообщений */
        'postman' => array
        (
            /* Адрес отправителя писем */
            'from_email' => 'robot@remote-edu.localhost',
            /* Имя отправителя */
            'from_name'  => 'Робот' 
        )
    );
            
?>