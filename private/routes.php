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
            
            /* Сохранение порядка учебных программ */
            array('/admin/programs/save-order', array(), 'education_programs', 'save_program_order', 'programs.save-order', Mvc_Router::ROUTE_REGEX),

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
            
            /* Сохранение порядка разделов(секций) */
            array('/admin/sections/save-order', array(), 'education_programs', 'save_section_order', 'sections.save-order', Mvc_Router::ROUTE_REGEX),

            /* Редактирование материалов администратором */
            array('/admin/materials/edit/([0-9]+)', array('material_id'), 'education_programs', 'edit_material', 'materials.admin.edit', Mvc_Router::ROUTE_REGEX),

            /* Загрузка учебных материалов */
            array('/admin/materials/upload', 'education_programs', 'upload_material', 'materials.admin.upload', Mvc_Router::ROUTE_STATIC),

            /* Удаление учебных материалов */
            array('/admin/materials/remove', 'education_programs', 'remove_material', 'materials.admin.remove', Mvc_Router::ROUTE_STATIC),
            
            /* Сохрание порядка материалов */
            array('/admin/materials/save-order', 'education_programs', 'save_material_order', 'materials.admin.save-order', Mvc_Router::ROUTE_STATIC),
            
            /* Управление учебными материалами преподователем */
            array('/admin/materials', 'educational_materials', 'index', 'teacher.materials', Mvc_Router::ROUTE_STATIC),
            
            /* Редактирование материалов преподователем */
            array('/teacher/materials/edit/([0-9]+)', array('material_id'), 'educational_materials', 'edit', 'materials.teacher.edit', Mvc_Router::ROUTE_REGEX),

            /* Загрузка учебных преподователем */
            array('/teacher/materials/upload', 'educational_materials', 'upload', 'materials.teacher.upload', Mvc_Router::ROUTE_STATIC),

            /* Удаление учебных преподователем */
            array('/teacher/materials/remove', 'educational_materials', 'remove', 'materials.teacher.remove', Mvc_Router::ROUTE_STATIC),
            
            /* Сохрание порядка преподователем */
            array('/teacher/materials/save-order', 'educational_materials', 'save_order', 'materials.teacher.save-order', Mvc_Router::ROUTE_STATIC),

            /* Управление тестами */
            array('/admin/tests', 'tests', 'list', 'tests.list', Mvc_Router::ROUTE_STATIC),

            /* Создание тестов */
            array('/admin/tests/create', 'tests', 'create', 'tests.create', Mvc_Router::ROUTE_STATIC),

            /* Редактирование тестов */
            array('/admin/tests/edit/([0-9]+)', array('test_id'), 'tests', 'edit', 'tests.edit', Mvc_Router::ROUTE_REGEX),

            /* Редактирование тестов */
            array('/admin/tests/delete/([0-9]+)', array('test_id'), 'tests', 'delete', 'tests.delete', Mvc_Router::ROUTE_REGEX),

            /* Добавление дополнительной попытки сдать тест */
            array('/admin/tests/add-extra-attempt/([0-9]+)/([0-9]+)', array('user_id', 'test_id'), 'tests', 'add_extra_attempt', 'tests.add-extra-attempt', Mvc_Router::ROUTE_REGEX),

            /* Просмотр результатов сдачи теста */
            array('/admin/tests/results/([0-9]+)', array('test_id'), 'tests', 'results', 'tests.results', Mvc_Router::ROUTE_REGEX),

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

            /* Работа куратора со своими слушателями */
            array('/teacher_students/', array(), 'teacher_students', 'index', 'teacher.students', Mvc_Router::ROUTE_REGEX),

            /* Работа куратора с дисциплинами своих слушателей */
            array('/teacher_students/disciplines/([0-9]+)', array('student_id'), 'teacher_students', 'disciplines', 'teacher.student_disciplines', Mvc_Router::ROUTE_REGEX),

            /* Отображение куратору успеваемости слушателя по дисциплине */
            array('/teacher_students/discipline/([0-9]+)/([0-9]+)', array('student_id', 'discipline_id'), 'teacher_students', 'discipline', 'teacher.student_discipline', Mvc_Router::ROUTE_REGEX),

            /* Редактирование контрольной точки */
            array('/checkpoints/edit/', array('checkpoint_id'), 'checkpoints', 'edit', 'checkpoint.edit', Mvc_Router::ROUTE_REGEX),

            /* Добавление контрольной точки */
            array('/checkpoints/set_pass/([0-9]+)/([0-9]+)', array('student_id', 'section_id'), 'checkpoints', 'set_pass', 'checkpoint.set_pass', Mvc_Router::ROUTE_REGEX),

            /* Удаление контрольной точки */
            array('/checkpoints/remove_pass/([0-9]+)/([0-9]+)', array('student_id', 'section_id'), 'checkpoints', 'remove_pass', 'checkpoint.remove_pass', Mvc_Router::ROUTE_REGEX),

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

            /* Интерфейс слушателя "Зачётная книжка" */
            //array('/student/record_book', 'Student_RecordBook', 'index', 'student.record_book', Mvc_Router::ROUTE_STATIC),
            array('/student/record_book', 'Student_RecordBook', 'index', 'student.record_book', Mvc_Router::ROUTE_STATIC),

            /* Отображение слушателю истории обучения по дисциплине */
            array('/student/record_book/discipline/([0-9]+)', array('discipline_id'), 'Student_RecordBook', 'discipline', 'student.record_book.discipline', Mvc_Router::ROUTE_REGEX),

            /* Отображение слушателю доступных материалов */
            array('/student/materials/show/([0-9]+)/([0-9]+)', array('discipline_id', 'app_id'), 'educational_materials', 'show', 'materials.show', Mvc_Router::ROUTE_REGEX),

            /* Скачивание материалов */
            array('/student/materials/download/([0-9]+)', array('material_id'), 'educational_materials', 'get_material', 'materials.download', Mvc_Router::ROUTE_REGEX),

            /* Прохождение теста */
            array('/student/test/([0-9]+)/([0-9]+)/([0-9a-z]{32})', array('test_id', 'section_id', 'code'), 'tests', 'examination', 'tests.examination', Mvc_Router::ROUTE_REGEX),

            /* Страничка помощи слушателю - как пользоваться материлами */
            array('/student/help/materials', 'pages', 'display', array('page' => 'help/materials'), 'help.materials', Mvc_Router::ROUTE_STATIC),

            /* Страничка помощи слушателю - как начать обучение */
            array('/student/help/how-to-start', 'pages', 'display', array('page' => 'help/how-to-start'), 'help.how-to-start', Mvc_Router::ROUTE_STATIC),

            /* Download contract by student */
            array('/student/download_contract/([0-9a-z]{32})', array('file_name'), 'applications', 'download_contract', 'applications.download_contract', Mvc_Router::ROUTE_REGEX),

            /* Страница с ценами */
            array('/price', 'pages', 'display', array('page' => 'price'), 'price', Mvc_Router::ROUTE_STATIC),

            /* Страница со способами оплаты */
            array('/payment', 'pages', 'display', array('page' => 'payment'), 'payment', Mvc_Router::ROUTE_STATIC),

            /* Просмотр входящих сообщений */
            array('/messages/inbox/([0-9]+)?', array('page'), 'messages', 'inbox', 'messages.inbox', Mvc_Router::ROUTE_REGEX),

            /* Оправка сообщения */
            //array('/messages/send/([0-9]+)?', array('to_id'), 'messages', 'send', 'messages.send', Mvc_Router::ROUTE_REGEX),
            array('/messages/send', 'messages', 'send', 'messages.send', Mvc_Router::ROUTE_STATIC),

            /* Чтение сообщения */
            array('/messages/read/([0-9]+)', array('message_id'), 'messages', 'message', 'messages.read', Mvc_Router::ROUTE_REGEX),

            /* Получение вложения сообщения */
            array('/messages/attachment/([0-9]+)', array('attachment_id'), 'messages', 'attachment', 'messages.attachment', Mvc_Router::ROUTE_REGEX),

            /* Удаление выбранного сообщения */
            array('/messages/remove', 'messages', 'remove', 'messages.remove', Mvc_Router::ROUTE_STATIC),

            /* Автодополнение при выборе региона */
            array('/ajax/autocomplete/region', 'ajax', 'autocomplete_region', 'ajax.autocomplete-region', Mvc_Router::ROUTE_STATIC),

            /* Автодополнение при выборе города */
            array('/ajax/autocomplete/city', 'ajax', 'autocomplete_city', 'ajax.autocomplete-city', Mvc_Router::ROUTE_STATIC)
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

                'student/record_book/index',              // интерфейс "Зачётная книжка"
                'student/record_book/discipline',

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
                'checkpoints/set_pass',
                'checkpoints/remove_pass',

                'teacher_students',                    // интерфейс "Мои слушатели"
                'teacher_students/disciplines',
                'teacher_students/discipline',

                'tests/list',
                'tests/create',
                'tests/edit',
                'tests/delete',
                'tests/add_extra_attempt',
                'tests/results',
                'tests/ajax_save_options',
                'tests/ajax_save_questions',
                'tests/ajax_load_test',
                'tests/ajax_delete_question',
                'tests/ajax_load_list'
            ),

            /* Администратор */
            Model_User::ROLE_ADMIN => array
            (
                'checkpoints/edit',                     // Редактирование контрольной точки

                'assignment/responsible_teacher',
                'assignment/students_curator',
                'assignment',

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

                'tests/list',
                'tests/create',
                'tests/edit',
                'tests/delete',
                'tests/add_extra_attempt',
                'tests/results',
                'tests/ajax_save_options',
                'tests/ajax_save_questions',
                'tests/ajax_load_test',
                'tests/ajax_delete_question',
                'tests/ajax_load_list'
            )
        )
    );