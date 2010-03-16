-- 
-- Дамп данных таблицы `users`
-- 

INSERT INTO `users` (`login`, `passwd`, `role`, `email`, `surname`, `name`, `patronymic`, `status`)
VALUES
('admin', /* 123 */ 'bd945caf6e009b23a4998106f0be8d3a', 'admin', 'admin@remote-edu.localhost', 'Админов', 'Админ', 'Админович', 'active'),
('teacher', /* 123 */ 'bd945caf6e009b23a4998106f0be8d3a', 'teacher', 'teacher@remote-edu.localhost', 'Преподов', 'Препод', 'Преподович', 'active'),
('student', /* 123 */ 'bd945caf6e009b23a4998106f0be8d3a', 'student', 'student@remote-edu.localhost', 'Студентов', 'Студент', 'Студентович', 'active');

-- 
-- Дамп данных таблицы `programs`
-- 

INSERT INTO `programs` (`program_id`, `title`, `labour_intensive`, `edu_type`, `paid_type`, `responsible_teacher`, `cost`)
VALUES
(1, 'Бесплатное направление', 100, 'direction', 'free', NULL, NULL),
(2, 'Платное направление', 300, 'direction', 'paid', NULL, '100.00'),
(3, 'Информационные технологии в сфере профессиональных коммуникаций', 498, 'direction', 'paid', NULL, '13000.00');

-- 
-- Дамп данных таблицы `disciplines`
-- 

INSERT INTO `disciplines` (`discipline_id`, `program_id`, `serial_number`, `title`, `coef`, `labour_intensive`, `responsible_teacher`)
VALUES
(1, 1, 0, 'Бесплатная дисциплина 1', 70, 50, 2),
(2, 1, 1, 'Бесплатная дисциплина 2', 30, 50, NULL),
(3, 2, 0, 'Платная дисциплина 1', 10, 100, NULL),
(4, 2, 1, 'Платная дисциплина 2', 40, 100, NULL),
(5, 2, 2, 'Платная дисциплина 3', 50, 100, NULL),
(6, 3, 0, 'Сайтостроение', 13, 64, NULL);

-- 
-- Дамп данных таблицы `sections`
-- 

INSERT INTO `sections` (`section_id`, `discipline_id`, `title`, `number`)
VALUES
(1, 3, 'Раздел 1', 1),
(2, 3, 'Раздел 2', 2),
(3, 3, 'Раздел 3', 3),
(4, 1, 'Название раздела', 1),
(5, 6, 'Основы сайтостроения', 1),
(6, 6, 'Язык разметки гипертекста HTML', 2),
(7, 6, 'Основы WEB-дизайна', 3),
(8, 6, 'Технологический процесс разработки WEB-страницы. Основные этапы создания WEB-сайта ', 4),
(9, 6, 'Размещение WEB-сайта в сети Интернет', 5),
(10, 6, 'Сопровождение и реклама WEB-сайта ', 6);

--
-- Дамп данных таблицы `materials`
--

INSERT INTO `materials` (`id`, `description`, `original_filename`, `mime_type`, `filename`, `section`, `type`, `uploader`)
VALUES
(1, 'Классификация WEB-сайтов', 'material_1.txt', 'text/plain', '219c24c250800f9affaa6cd0679f0095', 5, 'lecture', 2),
(2, 'Этапы проектирования WEB-сайтов', 'material_2.txt', 'text/plain', '70fb3bb2ca4b2ee5ce3d00253baca285', 5, 'lecture', 2),
(3, 'Обзор средств, используемых в сайтостроении', 'material_3.txt', 'text/plain', '5de2ad87f44b00204c347c1e77471bbc', 5, 'lecture', 2);

--
-- Дамп данных таблицы `materials_states`
--

INSERT INTO `materials_states` (`student_id`, `material_id`, `state`)
VALUES
(3, 1, NULL),
(3, 2, NULL),
(3, 3, NULL);

--
-- Дамп данных таблицы `applications`
--

INSERT INTO `applications` (`app_id`, `user_id`, `object_id`, `type`, `status`, `contract_filename`)
VALUES
(1, 3, 1, 'program', 'accepted', '6c998084e840917c05db421a8041330b'),
(2, 3, 6, 'discipline', 'signed', NULL);

INSERT INTO `apps_history` (`app_id`, `status`, `modifed`)
VALUES
(1, 'applied', '2010-02-15 19:29:34'),
(2, 'applied', '2010-02-15 20:20:08');

INSERT INTO `payments` (`payment_id`, `app_id`, `amount`, `created`)
VALUES
(NULL ,  '2',  '13000', NOW());