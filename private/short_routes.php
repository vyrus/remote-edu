<?php

    /* $Id$ */

    return array(
        /* Маршруты */
        'routes' => array
        (
            /* Шаблон статичного маршрута */
            /* array('алиас', Mvc_Router::ROUTE_STATIC, 'шаблон', 'контроллер', 'действие' [, array('параметр' => 'значение')]); */ 
            
            /* Шаблон маршрута на регулярном выражении */
            /* array('алиас', Mvc_Router::ROUTE_REGEX, 'регекс', array('параметр_1', 'параметр_2'), 'контроллер', 'действие' [, array('параметр' => 'значение')]); */ 
            
            /* Главная страница */
            array('index', Mvc_Router::ROUTE_STATIC, '/', 'pages', 'display', array('page' => 'index')),
            
            /* Авторизация */
            array('login', Mvc_Router::ROUTE_STATIC, '/login', 'users', 'login'),
            
            /* Выход */
            array('logout', Mvc_Router::ROUTE_STATIC, '/logout', 'users', 'logout'),
            
            /* Регистрация слушателя */
            array('student.register', Mvc_Router::ROUTE_STATIC, '/student/register', 'users', 'register_student'),
            
            /* Регистрация сотрудника */
            array('employee.register', Mvc_Router::ROUTE_STATIC, '/admin/employee/register', 'users', 'index_by_admin'),
            
            /* Управление учебными программами */
            array('programs.manage', Mvc_Router::ROUTE_STATIC, '/admin/programs', 'education_programs', 'index'),
            
            /* Управление учебными материалами */
            array('materials.manage', Mvc_Router::ROUTE_STATIC, '/admin/materials', 'educational_materials', 'index_by_admin'),
            
            /* Управление заявками */
            array('applications.manage', Mvc_Router::ROUTE_STATIC, '/admin/applications', 'applications', 'index_by_admin'),
            
            /* Инструкции для слушателя */
            array('student.index', Mvc_Router::ROUTE_STATIC, '/student', 'users', 'instructions_by_user'),
            
            /* Доступные для слушателя учебные программы */
            array('student.programs', Mvc_Router::ROUTE_STATIC, '/student/programs', 'education_programs', 'available'),
            
            /* Заявки слушателя */
            array('student.applications', Mvc_Router::ROUTE_STATIC, '/student/applications', 'applications', 'index_by_student'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/educational_material/([0-9]+)', array('material_id'), 'educational_materials', 'get_material'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/educational_materials/upload', 'educational_materials', 'upload'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/educational_materials/remove', 'educational_materials', 'remove'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/education_students', 'education_students', 'index'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/add_program/(direction|course)', array('program_type'), 'education_programs', 'add_program'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/add_discipline/([0-9]+)', array('speciality_id'), 'education_programs', 'add_discipline'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/add_section/([0-9]+)', array('discipline_id'), 'education_programs', 'add_section'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/remove_program/(direction|course)/([0-9]+)', array('program_type', 'program_id'), 'education_programs', 'remove_program'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/remove_discipline/([0-9]+)', array('discipline_id'), 'education_programs', 'remove_discipline'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/remove_section/([0-9]+)', array('section_id'), 'education_programs', 'remove_section'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/edit_program/(direction|course)/([0-9]+)', array('program_type', 'program_id'), 'education_programs', 'edit_program'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/edit_discipline/([0-9]+)', array('discipline_id'), 'education_programs', 'edit_discipline'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/save_discipline_order/', array(), 'education_programs', 'save_discipline_order'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/edit_section/([0-9]+)', array('section_id'), 'education_programs', 'edit_section'),
            
            /* Подача заявки */
            array('', Mvc_Router::ROUTE_REGEX, '/applications/apply/(discipline|program)/([0-9]+)', array('program_type', 'program_id'), 'applications', 'apply'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/applications/change_app_status/(accepted|declined|signed)/([0-9]+)', array('new_status', 'app_id'), 'applications', 'change_app_status'),
            
            /* Активация слушателя */
            array('', Mvc_Router::ROUTE_REGEX, '/activate_student/([0-9]+)/([0-9a-z]{32}).*', array('user_id', 'code'), 'users', 'activate_student'),
            
            /* Активация сотрудника */
            array('', Mvc_Router::ROUTE_REGEX, '/activate_employee/([0-9]+)/([0-9a-z]{32}).*', array('user_id', 'code'), 'users', 'activate_employee'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/ajax/autocomplete/region', 'ajax', 'autocomplete_region'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/ajax/autocomplete/city', 'ajax', 'autocomplete_city'),
            
            /* Страница с ценами */
            array('price', Mvc_Router::ROUTE_STATIC, '/price', 'pages', 'display', array('page' => 'price')),
            
            /* Страница со способами оплаты */
            array('payment', Mvc_Router::ROUTE_STATIC, '/payment', 'pages', 'display', array('page' => 'payment')),
            
            array('', Mvc_Router::ROUTE_STATIC, '/assignment/responsible_teacher', 'assignment', 'responsible_teacher'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/assignment/students_curator', 'assignment', 'student_curator'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/assignment', 'assignment', 'index'),
            
            /* Просмотр входящих сообщений */
            array('messages.inbox', Mvc_Router::ROUTE_STATIC, '/messages', 'messages', 'inbox'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/messages/inbox/([0-9]*)', array('page'), 'messages', 'inbox'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/messages/send', 'messages', 'send'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/messages/send/([0-9]*)', array('to_id'), 'messages', 'send'),
            
            array('', Mvc_Router::ROUTE_STATIC, '/messages/remove', 'messages', 'remove'),
            
            array('', Mvc_Router::ROUTE_REGEX, '/messages/([0-9]+)', array('message_id'), 'messages', 'message')
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