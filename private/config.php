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
			array (
				'type'		=> Mvc_Router::ROUTE_STATIC,
				'pattern'	=> "/education_programs",
				'handler'	=> array (
					'controller'	=> "Education_Programs",
					'action'		=> 'index',
				),
			),
			
			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/add_program/(direction|course)',
					'params'=> array ('program_type'),
				),
				'handler'	=> array (
					'controller'	=> "Education_Programs",
					'action'		=> 'add_program',
				),
			),
			
			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/add_discipline/([0-9]+)',
					'params'=> array ('speciality_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'add_discipline',
				)
			),
			
			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/add_section/([0-9]+)',
					'params'=> array ('discipline_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'add_section',
				)
			),

			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/remove_program/(direction|course)/([0-9]+)',
					'params'=> array ('program_type', 'program_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'remove_program',
				)
			),			

			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/remove_discipline/([0-9]+)',
					'params'=> array ('discipline_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'remove_discipline',
				)
			),			
			
			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/remove_program/([0-9]+)',
					'params'=> array ('program_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'remove_program',
				)
			),			

			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/edit_program/(direction|course)/([0-9]+)',
					'params'=> array ('program_type','program_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'edit_program',
				)
			),
			
			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/edit_discipline/([0-9]+)',
					'params'=> array ('discipline_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'edit_discipline',
				)
			),

			array (
				'type'		=> Mvc_Router::ROUTE_REGEX,
				'pattern'	=> array (
					'regex'	=> '/edit_section/([0-9]+)',
					'params'=> array ('section_id'),
				),
				'handler'	=> array (
					'controller'	=> 'Education_Programs',
					'action'		=> 'edit_section',
				)
			),
	
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
            ),
            
            array(
                'type'    => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/ajax/autocomplete/region',
                'handler' => array
                (
                    'controller' => 'ajax',
                    'action'     => 'autocomplete_region'
                )
            ),
            
            array(
                'type'    => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/ajax/autocomplete/city',
                'handler' => array
                (
                    'controller' => 'ajax',
                    'action'     => 'autocomplete_city'
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