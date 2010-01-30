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

INSERT INTO `programs` VALUES
(1, 'Бесплатное направление', 100, 'direction', 'free', NULL, NULL),
(2, 'Платное направление', 300, 'direction', 'paid', 100.00, NULL);

--
-- Дамп данных таблицы `disciplines`
--

INSERT INTO `disciplines` (`discipline_id`, `program_id`, `serial_number`, `title`, `coef`, `labour_intensive`, `responsible_teacher`) VALUES
(1, 1, 1, 'Бесплатная дисциплина 1', 70, 50, NULL),
(2, 1, 0, 'Бесплатная дисциплина 2', 30, 50, NULL),
(3, 2, 0, 'Платная дисциплина 1', 10, 100, NULL),
(4, 2, 1, 'Платная дисциплина 2', 40, 100, NULL),
(5, 2, 2, 'Платная дисциплина 3', 50, 100, NULL),
(7, 2, 3, 'Дисциплина 4', 30, 50, NULL);

-- 
-- Дамп данных таблицы `sections`
-- 

INSERT INTO `sections` VALUES 
(1, 3, 'Раздел 1', 1),
(2, 3, 'Раздел 2', 2),
(3, 3, 'Раздел 3', 3);