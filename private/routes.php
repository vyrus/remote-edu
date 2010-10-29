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
            array('/login', 'users', 'login', 'users.login', Mvc_Router::ROUTE_STATIC),

            /* Выход */
            array('/logout', 'users', 'logout', 'users.logout', Mvc_Router::ROUTE_STATIC),

            /* Восстановление пароля: запрос на восстановление */
            array('/restore-password', 'users', 'restore_passwd', 'users.restore-passwd', Mvc_Router::ROUTE_STATIC),

            /* Восстановление пароля: установка нового пароля */
            array('/restore-password/([0-9]+)/([0-9a-z]{32})', array('user_id', 'code'), 'users', 'reset_passwd', 'users.reset-passwd', Mvc_Router::ROUTE_REGEX),

            /* Управление пользователями */
            array('/admin/users', 'users', 'index_by_admin', 'admin.users', Mvc_Router::ROUTE_STATIC),

            /* Управление пользователями */
            array('/admin/users', 'users', 'index_by_admin', 'admin.users', Mvc_Router::ROUTE_STATIC),

            /* Список пользователей */
            array('/admin/users/list/(all|admin|teacher|student)?', array('filter'), 'users', 'users_list', 'users.list', Mvc_Router::ROUTE_REGEX),

            /* Редактирование аккаунтов */
            array('/admin/users/edit/([0-9]+)', array('user_id'), 'users', 'edit_account', 'users.edit', Mvc_Router::ROUTE_REGEX),

            /* Просмотр профиля слушателя */
            array('/admin/users/profile/([0-9]+)', array('user_id'), 'users', 'view_profile', 'users.profile', Mvc_Router::ROUTE_REGEX),

            /* Регистрация сотрудника */
            array('/admin/employee/register', 'users', 'register_employee_by_admin', 'employee.register', Mvc_Router::ROUTE_STATIC),

            /* Управление учебными программами */
            array('/admin/programs', 'education_programs', 'index', 'admin.programs', Mvc_Router::ROUTE_STATIC),

            /* Добавление учебных программ */
            array('/admin/programs/add/(direction|course)', array('program_type'), 'education_programs', 'add_program', 'programs.add', Mvc_Router::ROUTE_REGEX),

            /* Редактирование учебных программ */
            array('/admin/programs/edit/(direction|course)/([0-9]+)', array('program_type', 'program_id'), 'education_programs', 'edit_program', 'programs.edit', Mvc_Router::ROUTE_REGEX),

            /* Удаление учебных программ */
            array('/admin/programs/remove/(direction|course)/([0-9]+)', array('program_type', 'program_id'), 'education_programs', 'remove_program', 'programs.remove', Mvc_Router::ROUTE_REGEX),

            /* Добавление дисциплин */
            array('/admin/disciplines/add/([0-9]+)', array('speciality_id'), 'education_programs', 'add_discipline', 'disciplines.add', Mvc_Router::ROUTE_REGEX),

            /* Редактирование дисциплин */
            array('/admin/disciplines/edit/([0-9]+)', array('discipline_id'), 'education_programs', 'edit_discipline', 'disciplines.edit', Mvc_Router::ROUTE_REGEX),

            /* Удаление дисциплин */
            array('/admin/disciplines/remove/([0-9]+)', array('discipline_id'), 'education_programs', 'remove_discipline', 'disciplines.remove', Mvc_Router::ROUTE_REGEX),

            /* Сохранение порядка дисциплин */
            array('/admin/disciplines/save-order', array(), 'education_programs', 'save_discipline_order', 'disciplines.save-order', Mvc_Router::ROUTE_REGEX),

            /* Добавление разделов */
            array('/admin/sections/add/([0-9]+)', array('discipline_id'), 'education_programs', 'add_section', 'sections.add', Mvc_Router::ROUTE_REGEX),

            /* Редактирование разделов */
            array('/admin/sections/edit/([0-9]+)', array('section_id'), 'education_programs', 'edit_section', 'sections.edit', Mvc_Router::ROUTE_REGEX),

            /* Удаление разделов */
            array('/admin/sections/remove/([0-9]+)', array('section_id'), 'education_programs', 'remove_section', 'sections.remove', Mvc_Router::ROUTE_REGEX),

            /* Управление учебными материалами администратором */
            array('/admin/materials', 'educational_materials', 'index', 'admin.materials', Mvc_Router::ROUTE_STATIC),

            /* Редактирование материалов администратором */
            array('/admin/materials/edit/([0-9]+)', array('material_id'), 'educational_materials', 'edit', 'materials.edit', Mvc_Router::ROUTE_REGEX),

            /* Загрузка учебных материалов */
            array('/admin/materials/upload', 'educational_materials', 'upload', 'materials.upload', Mvc_Router::ROUTE_STATIC),

            /* Загрузка учебных материалов */
            array('/admin/materials/remove', 'educational_materials', 'remove', 'materials.remove', Mvc_Router::ROUTE_STATIC),

            /* Управление заявками */
            array('/admin/applications', 'applications', 'index_by_admin', 'admin.applications', Mvc_Router::ROUTE_STATIC),

            /* Изменение статусы заявки администратором */
            array('/admin/applications/change-status/(accepted|declined|signed)/([0-9]+)', array('new_status', 'app_id'), 'applications', 'change_app_status', 'applications.change-status', Mvc_Router::ROUTE_REGEX),

            /* Удаление заявки администратором */
            array('/admin/applications/delete/([0-9]+)', array('app_id'), 'applications', 'delete', 'applications.delete', Mvc_Router::ROUTE_REGEX),

            /* Изменение статусы заявки администратором */
            array('/admin/payments/add/([0-9]+)', array('app_id'), 'payments', 'add', 'payments.add', Mvc_Router::ROUTE_REGEX),

            /* Назначение преподавателей, ответственных за дисциплины */
            array('/admin/responsible-teachers', 'assignment', 'responsible_teacher', 'admin.responsible-teachers', Mvc_Router::ROUTE_STATIC),

            /* Назначение кураторов слушателей */
            array('/admin/curators', 'assignment', 'student_curator', 'admin.curators', Mvc_Router::ROUTE_STATIC),

            /* Инструкции для администратора по регистрации пользователей */
            array('/admin/help/registration', 'pages', 'display',  array('page' => 'help/registration'), 'help.registration', Mvc_Router::ROUTE_STATIC),

            /* Инструкции для администратора по формированию программ  */
            array('/admin/help/programs', 'pages', 'display',  array('page' => 'help/programs'), 'help.programs', Mvc_Router::ROUTE_STATIC),

            /* Работа ответственного преподавателя с дисциплинами */
            array('/teacher_courses/discipline/([0-9]+)?', array('discipline_id'), 'teacher_courses', 'discipline', 'teacher.discipline', Mvc_Router::ROUTE_REGEX),

            /* Работа ответственного преподавателя с курсами */
            //array('/teacher_courses/course/([0-9]+)?', array('course_id'), 'teacher_courses', 'course', 'teacher.course', Mvc_Router::ROUTE_REGEX),

            /* Редактирование контрольной точки */
            array('/admin/checkpoints/edit/', array('checkpoint_id'), 'checkpoints', 'edit', 'checkpoints.edit', Mvc_Router::ROUTE_REGEX),

            /* Добавление контрольной точки */
            array('/teacher_courses/set_checkpoint_pass/([0-9]+)/([0-9]+)', array('student_id', 'section_id'), 'teacher_courses', 'set_checkpoint_pass', 'teacher.set_checkpoint_pass', Mvc_Router::ROUTE_REGEX),

            /* Удаление контрольной точки */
            array('/teacher_courses/remove_checkpoint_pass/([0-9]+)/([0-9]+)', array('student_id', 'section_id'), 'teacher_courses', 'remove_checkpoint_pass', 'teacher.remove_checkpoint_pass', Mvc_Router::ROUTE_REGEX),

            /**
            * @todo Нормальные ссылки для тестов.
            */
            /* __Редактирование тестов__ */
            array('/tests/edit/([0-9]+)', array('test_id'), 'tests', 'edit', 'tests.edit', Mvc_Router::ROUTE_REGEX),

            /* __Добавление дополнительной попытки сдать тест__ */
            array('/tests/add-extra-attempt/([0-9]+)/([0-9]+)', array('user_id', 'test_id'), 'tests', 'add_extra_attempt', 'tests.add-extra-attempt', Mvc_Router::ROUTE_REGEX),

            /* __Результаты сдачи теста__ */
            array('/tests/results/([0-9]+)', array('test_id'), 'tests', 'results', 'tests.results', Mvc_Router::ROUTE_REGEX),

            /* Инструкции для слушателя */
            array('/student', 'pages', 'display', array('page' => 'help/instructions'), 'student.index', Mvc_Router::ROUTE_STATIC),

            /* Регистрация слушателя */
            array('/student/register', 'users', 'register_student', 'student.register', Mvc_Router::ROUTE_STATIC),

            /* Активация слушателя */
            array('/student/activate/([0-9]+)/([0-9a-z]{32})', array('user_id', 'code'), 'users', 'activate_student', 'student.activate', Mvc_Router::ROUTE_REGEX),

            /* Активация сотрудника */
            array('/employee/activate/([0-9]+)/([0-9a-z]{32})', array('user_id', 'code'), 'users', 'activate_employee', 'employee.activate', Mvc_Router::ROUTE_REGEX),

            /* Расширенный профиль слушателя */
            array('/student/extended-profile', 'users', 'profile_extended_by_student', 'student.extended-profile', Mvc_Router::ROUTE_STATIC),

            /* Заявки слушателя */
            array('/student/applications', 'applications', 'list_by_student', 'student.applications', Mvc_Router::ROUTE_STATIC),

            /* Подача заявки слушателем */
            array('/student/apply', 'applications', 'index_by_student', 'student.apply', Mvc_Router::ROUTE_STATIC),

            /* Отправка слушателем заявки на выбранную программу */
            array('/student/apply/(discipline|program)/([0-9]+)', array('program_type', 'program_id'), 'applications', 'apply', 'student.applications.apply', Mvc_Router::ROUTE_REGEX),

            /* Доступные для слушателя учебные программы */
            array('/student/programs', 'education_programs', 'available', 'student.programs', Mvc_Router::ROUTE_STATIC),

            /* Отображение слушателю доступных материалов */
            array('/student/materials/show/([0-9]+)/([0-9]+)', array('discipline_id', 'app_id'), 'educational_materials', 'show', 'materials.show', Mvc_Router::ROUTE_REGEX),

            /* Скачивание материалов */
            array('/student/materials/download/([0-9]+)', array('material_id'), 'educational_materials', 'get_material', 'materials.download', Mvc_Router::ROUTE_REGEX),

            /* Страничка помощи слушателю - как пользоваться материлами */
            array('/student/help/materials', 'pages', 'display', array('page' => 'help/materials'), 'help.materials', Mvc_Router::ROUTE_STATIC),

            /* Страничка помощи слушателю - как начать обучение */
            array('/student/help/how-to-start', 'pages', 'display', array('page' => 'help/how-to-start'), 'help.how-to-start', Mvc_Router::ROUTE_STATIC),

            /* Страница с ценами */
            array('/price', 'pages', 'display', array('page' => 'price'), 'price', Mvc_Router::ROUTE_STATIC),

            /* Страница со способами оплаты */
            array('/payment', 'pages', 'display', array('page' => 'payment'), 'payment', Mvc_Router::ROUTE_STATIC),

            /* Просмотр входящих сообщений */
            array('/messages/inbox/([0-9]+)?', array('page'), 'messages', 'inbox', 'messages.inbox', Mvc_Router::ROUTE_REGEX),

            /* Оправка сообщения */
            array('/messages/send/([0-9]+)?', array('to_id'), 'messages', 'send', 'messages.send', Mvc_Router::ROUTE_REGEX),

            /* Чтение сообщения */
            array('/messages/read/([0-9]+)', array('message_id'), 'messages', 'message', 'messages.read', Mvc_Router::ROUTE_REGEX),

            /* Получение вложения сообщения */
            array('/messages/attachment/([0-9]+)', array('attachment_id'), 'messages', 'attachment', 'messages.attachment', Mvc_Router::ROUTE_REGEX),

            /* Удаление выбранного сообщения */
            array('/messages/remove', 'messages', 'remove', 'messages.remove', Mvc_Router::ROUTE_STATIC),

            /* Автодополнение при выборе региона */
            array('/ajax/autocomplete/region', 'ajax', 'autocomplete_region', 'ajax.autocomplete-region', Mvc_Router::ROUTE_STATIC),

            /* Автодополнение при выборе города */
            array('/ajax/autocomplete/city', 'ajax', 'autocomplete_city', 'ajax.autocomplete-city', Mvc_Router::ROUTE_STATIC),

            /* Download contract by student */
            array('/student/download_contract/([0-9a-z]{32})', array('file_name'), 'applications', 'download_contract', 'applications.download_contract', Mvc_Router::ROUTE_REGEX)
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
                'educational_materials/show',
                'education_programs/available',

                'tests/examination',
                'tests/ajax_get_exam_questions',
                'tests/ajax_check_exam_questions',
            ),

            /* Преподаватель */
            Model_User::ROLE_TEACHER => array
            (
                'educational_materials/index',          // учебные материалы, добавленные залогиненным преподавателем
                'educational_materials/upload',
                'educational_materials/edit',
                'educational_materials/remove',

                'teacher_courses/discipline',           // дисциплины, за которые ответственным назначен преподаватель
                //'teacher_courses/course',               // курсы, за которые ответственным назначен преподаватель
                'teacher_courses/set_checkpoint',       // добавление контрольной точки
                'teacher_courses/remove_checkpoint',    // удаление контрольной точки
            ),

            /* Администратор */
            Model_User::ROLE_ADMIN => array
            (
                'admin/checkpoints/edit',                // Редактирование контрольной точки

                '/assignment/responsible_teacher',
                '/assignment/students_curator',
                '/assignment',

                'users/register_employee_by_admin',     // учебные материалы, доступные для слушателя
                'users/users_list',
                'users/edit_account',
                'users/view_profile',

                'applications/index_by_admin',          // список поданных заявок всех пользователей
                'applications/delete',

                'payments/add',

                'educational_materials/index',
                'educational_materials/upload',
                'educational_materials/edit',
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
            )
        )
    );

?>