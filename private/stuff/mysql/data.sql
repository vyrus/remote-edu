-- 
-- Дамп данных таблицы `users`
-- 

INSERT INTO `users` (`login`, `passwd`, `role`, `email`, `surname`, `name`, `patronymic`, `status`)
VALUES
('admin', 'bd945caf6e009b23a4998106f0be8d3a', 'admin', 'admin@remote-edu.localhost', 'Админов', 'Админ', 'Админович', 'active'),
('teacher', 'bd945caf6e009b23a4998106f0be8d3a', 'teacher', 'teacher@remote-edu.localhost', 'Преподов', 'Препод', 'Преподович', 'active'),
('student', 'bd945caf6e009b23a4998106f0be8d3a', 'student', 'student@remote-edu.localhost', 'Студентов', 'Студент', 'Студентович', 'active');

-- 
-- Дамп данных таблицы `programs`
-- 

INSERT INTO `programs` VALUES (1, 'Направление-1', 120, 'direction', 'free');

-- 
-- Дамп данных таблицы `disciplines`
-- 

INSERT INTO `disciplines` VALUES (1, 1, 'Дисциплина-1', 10, 70);

-- 
-- Дамп данных таблицы `sections`
-- 

INSERT INTO `sections` VALUES (1, 1, 'Раздел-1', 1);