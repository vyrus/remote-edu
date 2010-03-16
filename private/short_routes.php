<?php

    /* $Id$ */

    return array(
        /* Маршруты */
        'routes' => array
        (
            /* Шаблон статичного маршрута */
            /* array('шаблон', 'контроллер', 'действие', [array('параметр' => 'значение'), ] 'алиас', Mvc_Router::ROUTE_STATIC); */ 
            
            /* Шаблон маршрута на регулярном выражении */
            /* array('регекс', array('параметры'), 'контроллер', 'действие', [array('параметр' => 'значение'), ] 'алиас', Mvc_Router::ROUTE_REGEX); */ 
            
            /* Главная страница */
            array('/', 'pages', 'display', array('page' => 'index'), 'index', Mvc_Router::ROUTE_STATIC),
            
            /* Авторизация */
            array('/login', 'users', 'login', 'login', Mvc_Router::ROUTE_STATIC),
            
            /* Выход */
            array('/logout', 'users', 'logout', 'logout', Mvc_Router::ROUTE_STATIC),
            
            /* Регистрация слушателя */
            array('/student/register', 'users', 'register_student', 'student.register', Mvc_Router::ROUTE_STATIC),
            
            /* Регистрация сотрудника */
            array('/admin/employee/register', 'users', 'register_employee_by_admin', 'admin.register-employee', Mvc_Router::ROUTE_STATIC),
            
            /* Управление учебными программами */
            array('/admin/programs', 'education_programs', 'index', 'admin.programs', Mvc_Router::ROUTE_STATIC),
            
            /* Управление учебными материалами */
            array('/admin/materials', 'educational_materials', 'index_by_admin', 'admin.materials', Mvc_Router::ROUTE_STATIC),
            
            /* Загрузка учебных материалов */
            array('/admin/materials/upload', 'educational_materials', 'upload', 'admin.materials.upload', Mvc_Router::ROUTE_STATIC),
            
            /* Управление заявками */
            array('/admin/applications', 'applications', 'index_by_admin', 'admin.applications', Mvc_Router::ROUTE_STATIC),
            
            /* Изменение статусы заявки администратором */
            array('/admin/applications/change-status/(accepted|declined|signed)/([0-9]+)', array('new_status', 'app_id'), 'applications', 'change_app_status', 'admin.applications.change-status', Mvc_Router::ROUTE_REGEX),
            
            /* Удаление заявки администратором */
            /**
            * @todo Отвязка алиасов маршрутов от ролей юзеров.
            */
            array('/admin/applications/delete/([0-9]+)', array('app_id'), 'applications', 'delete', 'admin.applications.delete', Mvc_Router::ROUTE_REGEX),
            
            /* Изменение статусы заявки администратором */
            array('/admin/payments/add/([0-9]+)', array('app_id'), 'payments', 'add', 'admin.payments.add', Mvc_Router::ROUTE_REGEX),
            
            /* Инструкции для администратора */
            array('/admin/help', 'users', 'instructions_by_admin', 'admin.help', Mvc_Router::ROUTE_STATIC),
            
            /* Инструкции для слушателя */
            array('/student', 'users', 'instructions_by_user', 'student.index', Mvc_Router::ROUTE_STATIC),
            
            /* Доступные для слушателя учебные программы */
            array('/student/programs', 'education_programs', 'available', 'student.programs', Mvc_Router::ROUTE_STATIC),
            
            /* Заявки слушателя */
            array('/student/applications', 'applications', 'list_by_student', 'student.applications', Mvc_Router::ROUTE_STATIC),
            
            /* Подача заявки слушателем */
            array('/student/apply', 'applications', 'index_by_student', 'student.apply', Mvc_Router::ROUTE_STATIC),
            
            /* Отправка слушателем заявки на выбранную программу */
            array('/student/apply/(discipline|program)/([0-9]+)', array('program_type', 'program_id'), 'applications', 'apply', 'student.applications.apply', Mvc_Router::ROUTE_REGEX),
            
            /* Расширенный профиль слушателя */
            array('/student/extended-profile', 'users', 'profile_extended_by_student', 'student.extended-profile', Mvc_Router::ROUTE_STATIC),
            
            /* Страничка помощи слушателю - как начать обучение */
            array('/student/help/how-to-start', 'users', 'howtostart', 'student.help.how-to-start', Mvc_Router::ROUTE_STATIC),
            
            array('/educational_material/([0-9]+)', array('material_id'), 'educational_materials', 'get_material', '', Mvc_Router::ROUTE_REGEX),
            
            array('/educational_materials/upload', 'educational_materials', 'upload', '', Mvc_Router::ROUTE_STATIC),
            
            array('/educational_materials/remove', 'educational_materials', 'remove', '', Mvc_Router::ROUTE_STATIC),
            
            array('/education_students', 'education_students', 'index', '', Mvc_Router::ROUTE_STATIC),
            
            array('/add_program/(direction|course)', array('program_type'), 'education_programs', 'add_program', '', Mvc_Router::ROUTE_REGEX),
            
            array('/add_discipline/([0-9]+)', array('speciality_id'), 'education_programs', 'add_discipline', '', Mvc_Router::ROUTE_REGEX),
            
            array('/add_section/([0-9]+)', array('discipline_id'), 'education_programs', 'add_section', '', Mvc_Router::ROUTE_REGEX),
            
            array('/remove_program/(direction|course)/([0-9]+)', array('program_type', 'program_id'), 'education_programs', 'remove_program', '', Mvc_Router::ROUTE_REGEX),
            
            array('/remove_discipline/([0-9]+)', array('discipline_id'), 'education_programs', 'remove_discipline', '', Mvc_Router::ROUTE_REGEX),
            
            array('/remove_section/([0-9]+)', array('section_id'), 'education_programs', 'remove_section', '', Mvc_Router::ROUTE_REGEX),
            
            array('/edit_program/(direction|course)/([0-9]+)', array('program_type', 'program_id'), 'education_programs', 'edit_program', '', Mvc_Router::ROUTE_REGEX),
            
            array('/edit_discipline/([0-9]+)', array('discipline_id'), 'education_programs', 'edit_discipline', '', Mvc_Router::ROUTE_REGEX),
            
            array('/save_discipline_order/', array(), 'education_programs', 'save_discipline_order', '', Mvc_Router::ROUTE_REGEX),
            
            array('/edit_section/([0-9]+)', array('section_id'), 'education_programs', 'edit_section', '', Mvc_Router::ROUTE_REGEX),
            
            /* Активация слушателя */
            /**
            * @todo Маршруты и так теперь нечувствительны к слешу на конце 
            * адреса, .* больше не нужно ставить.
            */
            array('/activate_student/([0-9]+)/([0-9a-z]{32}).*', array('user_id', 'code'), 'users', 'activate_student', '', Mvc_Router::ROUTE_REGEX),
            
            /* Активация сотрудника */
            array('/activate_employee/([0-9]+)/([0-9a-z]{32}).*', array('user_id', 'code'), 'users', 'activate_employee', '', Mvc_Router::ROUTE_REGEX),
            
            array('/ajax/autocomplete/region', 'ajax', 'autocomplete_region', '', Mvc_Router::ROUTE_STATIC),
            
            array('/ajax/autocomplete/city', 'ajax', 'autocomplete_city', '', Mvc_Router::ROUTE_STATIC),
            
            /* Страница с ценами */
            array('/price', 'pages', 'display', array('page' => 'price'), 'price', Mvc_Router::ROUTE_STATIC),
            
            /* Страница со способами оплаты */
            array('/payment', 'pages', 'display', array('page' => 'payment'), 'payment', Mvc_Router::ROUTE_STATIC),
            
            /* Назначение преподавателей, ответственных за дисциплины */
            array('/admin/responsible-teachers', 'assignment', 'responsible_teacher', 'admin.responsible-teachers', Mvc_Router::ROUTE_STATIC),
            
            /* Назначение кураторов слушателей */
            array('/admin/curators', 'assignment', 'student_curator', 'admin.curators', Mvc_Router::ROUTE_STATIC),
            
            array('/assignment', 'assignment', 'index', '', Mvc_Router::ROUTE_STATIC),
            
            /* Просмотр входящих сообщений */
            array('/messages/inbox', 'messages', 'inbox', 'messages.inbox', Mvc_Router::ROUTE_STATIC),
            
            array('/messages/inbox/([0-9]*)', array('page'), 'messages', 'inbox', '', Mvc_Router::ROUTE_REGEX),
            
            array('/messages/send', 'messages', 'send', 'messages.send', Mvc_Router::ROUTE_STATIC),
            
            array('/messages/send/([0-9]*)', array('to_id'), 'messages', 'send', '', Mvc_Router::ROUTE_REGEX),
            
            array('/messages/remove', 'messages', 'remove', '', Mvc_Router::ROUTE_STATIC),
            
            array('/messages/([0-9]+)', array('message_id'), 'messages', 'message', '', Mvc_Router::ROUTE_REGEX)
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