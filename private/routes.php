<?php

    /* $Id$ */

    return array(
        /* Маршруты */
        /**
        * @todo Сделать покрасивше пути сайта.
        */
        'routes' => array
        (
            array (
                'type'     => Mvc_Router::ROUTE_REGEX,
                'pattern'  => array
                (
                    'regex'  => '/educational_material/([0-9]+)',
                    'params' => array ('material_id'),
                ),
                'handler' => array
                (
                    'controller' => 'educational_materials',
                    'action'     => 'get_material',
                ),
            ),

            array (
                'type'    => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/educational_materials/upload',
                'handler' => array
                (
                    'controller' => 'educational_materials',
                    'action'     => 'upload',
                ),
            ),

            array (
                'type'    => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/educational_materials/remove',
                'handler' => array
                (
                    'controller' => 'educational_materials',
                    'action'     => 'remove',
                ),
            ),

            array (
                'type'    => Mvc_Router::ROUTE_STATIC,
                'pattern' => "/education_students",
                'handler' => array
                (
                    'controller' => "education_students",
                    'action'     => 'index',
                ),
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/add_program/(direction|course)',
                    'params' => array ('program_type'),
                ),
                'handler' => array
                (
                    'controller' => "education_programs",
                    'action'     => 'add_program',
                ),
            ),

            array (
                'type'   => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/add_discipline/([0-9]+)',
                    'params' => array ('speciality_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'add_discipline',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/add_section/([0-9]+)',
                    'params' => array ('discipline_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'add_section',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/remove_program/(direction|course)/([0-9]+)',
                    'params' => array ('program_type', 'program_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'remove_program',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/remove_discipline/([0-9]+)',
                    'params' => array ('discipline_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'remove_discipline',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/remove_section/([0-9]+)',
                    'params' => array ('section_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'remove_section',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/edit_program/(direction|course)/([0-9]+)',
                    'params' => array ('program_type','program_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'edit_program',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/edit_discipline/([0-9]+)',
                    'params' => array ('discipline_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'edit_discipline',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/save_discipline_order/',
                    'params' => array (),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'save_discipline_order',
                )
            ),

            array (
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/edit_section/([0-9]+)',
                    'params' => array ('section_id'),
                ),
                'handler' => array
                (
                    'controller' => 'education_programs',
                    'action'     => 'edit_section',
                )
            ),

            /* Подача заявки */
            array(
                'type'    => Mvc_Router::ROUTE_REGEX,
                'pattern' => array
                (
                    'regex'  => '/applications/apply/(discipline|program)/([0-9]+)',
                    'params' => array ('program_type', 'program_id'),
                ),
                'handler' => array
                (
                    'controller' => 'applications',
                    'action'     => 'apply',
                )
            ),

            array(
                'type'     => Mvc_Router::ROUTE_REGEX,
                'pattern'  => array
                (
                    'regex'  => '/applications/change_app_status/(accepted|declined|signed)/([0-9]+)',
                    'params' => array ('new_status', 'app_id'),
                ),
                'handler' => array
                (
                    'controller' => 'applications',
                    'action'     => 'change_app_status',
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
            ),

            /* Страница с ценами */
            array(
                'type' => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/price',
                'handler' => array
                (
                    'controller' => 'pages',
                    'action'     => 'display',
                    'params'     => array('page' => 'price')
                )
            ),

            /* Страница со способами оплаты */
            array(
                'type' => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/payment',
                'handler' => array
                (
                    'controller' => 'pages',
                    'action'     => 'display',
                    'params'     => array('page' => 'payment')
                )
            ),

			array(
				'type' => Mvc_Router::ROUTE_STATIC,
				'pattern' => '/assignment/responsible_teacher',
				'handler' => array(
					'controller' => 'assignment',
					'action' => 'responsible_teacher',
				),
			),

			array(
				'type' => Mvc_Router::ROUTE_STATIC,
				'pattern' => '/assignment/students_curator',
				'handler' => array(
					'controller' => 'assignment',
					'action' => 'student_curator',
				),
			),

			array(
				'type' => Mvc_Router::ROUTE_STATIC,
				'pattern' => '/assignment',
				'handler' => array(
					'controller' => 'assignment',
					'action' => 'index',
				),
			),
			
			array(
			    'type' => Mvc_Router::ROUTE_STATIC,
			    'pattern' => '/messages',
			    'handler' => array(
			        'controller' => 'messages',
			        'action' => 'index',
			    ),
			),
			
			array(
			    'type' => Mvc_Router::ROUTE_STATIC,
			    'pattern' => '/messages/inbox',
			    'handler' => array(
			        'controller' => 'messages',
			        'action' => 'inbox',
			    ),
			),

            array(
                'type'     => Mvc_Router::ROUTE_REGEX,
                'pattern'  => array
                (
                    'regex'  => '/messages/inbox/([0-9]*)',
                    'params' => array ('page'),
                ),
                'handler' => array
                (
                    'controller' => 'messages',
                    'action'     => 'inbox',
                )
            ),

		    array(
    		    'type' => Mvc_Router::ROUTE_STATIC,
    		    'pattern' => '/messages/send',
    		    'handler' => array(
    		        'controller' => 'messages',
    		        'action' => 'send',
    		    ),
    		),
			
            array(
                'type'     => Mvc_Router::ROUTE_REGEX,
                'pattern'  => array
                (
                    'regex'  => '/messages/send/([0-9]*)',
                    'params' => array ('to_id'),
                ),
                'handler' => array
                (
                    'controller' => 'messages',
                    'action'     => 'send',
                )
            ),
            
            array(
                'type' => Mvc_Router::ROUTE_STATIC,
                'pattern' => '/messages/remove',
                'handler' => array(
                    'controller' => 'messages',
                    'action' => 'remove',
                ),
            ),	

            array(
                'type'     => Mvc_Router::ROUTE_REGEX,
                'pattern'  => array
                (
                    'regex'  => '/messages/([0-9]+)',
                    'params' => array ('message_id'),
                ),
                'handler' => array
                (
                    'controller' => 'messages',
                    'action'     => 'message',
                )
            ),	
        ),

        /* Права доступа к разделам сайта */
        'permissions' => array
        (
            /**
            * Здесь перечисляются роли пользователей и пути, к которым у них
            * есть доступ. Все пути, не перечисленные здесь, считаются
            * общедоступными.
            *
            * роль_пользователя => array(
            *   'контроллер/действие'
            * )
            */

            /* Слушатель */
            Model_User::ROLE_STUDENT => array
            (
                'users/profile_extended_by_student',

                'applications/list',                      // просмотр списка заявок и их статусов
                'applications/apply',                     // подача заяка
                'applications/index_by_student',          // форма для подачи заявки

                'educational_materials/index_by_student',
                'education_programs/show_available',
    		),

            /* Преподаватель */
            Model_User::ROLE_TEACHER => array
            (
                'education_students/index',

                'educational_materials/index_by_teacher',  // учебные материалы, добавленные залогиненным преподавателем
            ),

            /* Администратор */
            Model_User::ROLE_ADMIN => array
            (
				'/assignment/responsible_teacher',
				'/assignment/students_curator',
				'/assignment',

                'users/register_employee_by_admin',       // учебные материалы, доступные для слушателя

                'applications/index_by_admin',            // список поданных заявок всех пользователей
                'applications/delete',

                'payments/add',

                'educational_materials/index_by_admin',
                'educational_materials/upload',
                'educational_materials/remove',

                'education_programs/index',
                'education_programs/add_program',
                'education_programs/add_discipline',
                'education_programs/add_section',
                'education_programs/remove_program',
                'education_programs/remove_discipline',
                'education_programs/remove_section',
                'education_programs/edit_program',
                'education_programs/edit_discipline',
                'education_programs/edit_section',

                'education_students/index',
            )
        )
    );

?>
