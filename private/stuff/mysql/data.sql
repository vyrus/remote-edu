-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 11, 2011 at 02:27 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dist`
--

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`app_id`, `user_id`, `object_id`, `type`, `status`, `contract_filename`) VALUES
(1, 3, 1, 'program', 'signed', '6c998084e840917c05db421a8041330b'),
(2, 3, 6, 'discipline', 'signed', NULL),
(4, 3, 2, 'program', 'signed', '6c998084e840917c05db421a8041330b'),
(5, 3, 2, 'discipline', 'accepted', NULL);

--
-- Dumping data for table `apps_history`
--

INSERT INTO `apps_history` (`app_id`, `status`, `modifed`) VALUES
(1, 'applied', '2010-02-15 19:29:34'),
(2, 'applied', '2010-02-15 20:20:08'),
(4, 'applied', '2011-02-18 12:42:07'),
(5, 'applied', '2011-02-23 21:05:59');

--
-- Dumping data for table `checkpoints`
--

INSERT INTO `checkpoints` (`section_id`, `active`, `title`, `text`, `type`, `test_id`) VALUES
(1, 0, '', NULL, '', NULL);

--
-- Dumping data for table `checkpoints_students`
--

INSERT INTO `checkpoints_students` (`section_id`, `student_id`, `created`) VALUES
(4, 3, '2011-02-14 00:19:36'),
(1, 3, '2011-02-18 12:44:13');

--
-- Dumping data for table `disciplines`
--

INSERT INTO `disciplines` (`discipline_id`, `program_id`, `serial_number`, `title`, `coef`, `labour_intensive`, `responsible_teacher`) VALUES
(1, 1, 0, 'Бесплатная дисциплина 1', 70, 50, 2),
(2, 1, 1, 'Бесплатная дисциплина 2', 30, 50, 2),
(3, 2, 0, 'Платная дисциплина 1', 10, 100, 2),
(4, 2, 1, 'Платная дисциплина 2', 40, 100, NULL),
(5, 2, 2, 'Платная дисциплина 3', 50, 100, NULL),
(6, 3, 0, 'Сайтостроение', 13, 64, 2);

--
-- Dumping data for table `edu_docs`
--

INSERT INTO `edu_docs` (`edu_doc_id`, `user_id`, `type`, `custom_type`, `number`, `exit_year`, `speciality`, `qualification`) VALUES
(1, 3, 'diploma-high', '', '12345', 2011, 'Прикладная информатика', 'Инженер');

--
-- Dumping data for table `examinations`
--


--
-- Dumping data for table `extra_attempts`
--


--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `description`, `original_filename`, `mime_type`, `filename`, `section`, `type`, `uploader`, `number`) VALUES
(1, 'Классификация WEB-сайтов', 'material_1.txt', 'text/plain', '219c24c250800f9affaa6cd0679f0095', 5, 'lecture', 2, 0),
(2, 'Этапы проектирования WEB-сайтов', 'material_2.txt', 'text/plain', '70fb3bb2ca4b2ee5ce3d00253baca285', 5, 'lecture', 2, 0),
(3, 'Обзор средств, используемых в сайтостроении', 'material_3.txt', 'text/plain', '5de2ad87f44b00204c347c1e77471bbc', 5, 'lecture', 2, 0),
(9, 'qwe11111', '2.sql', 'text/x-sql', 'bbb8a0d589f0c9e72fd4c0cc431169c5', 1, 'practice', 1, 0),
(10, 'rty', 'Дист.doc', 'application/msword', '2d72c6152502c6e59f37deeb36af8090', 1, 'practice', 1, 3),
(7, 'ads', '108.jpg', 'image/jpeg', '57e1e0a75387c6a1caddf46cc0a993a6', 1, 'lecture', 1, 1),
(8, 'zxc', '217-FZ.doc', 'application/msword', 'f85945afac67eae714d8ab1a7f97aecc', 1, 'lecture', 1, 4),
(11, 'ssdfsdfsdf', '0016-800x333.jpg', 'image/jpeg', '872b914727da7afddaee9691e07e3e9a', 1, 'practice', 1, 2);

--
-- Dumping data for table `materials_states`
--

INSERT INTO `materials_states` (`student_id`, `material_id`, `state`) VALUES
(2, 1, 'last'),
(3, 2, 'downloaded'),
(3, 3, 'downloaded'),
(1, 6, 'downloaded'),
(1, 1, 'downloaded'),
(3, 7, 'downloaded'),
(3, 10, 'last'),
(1, 11, 'downloaded'),
(1, 9, 'last');

--
-- Dumping data for table `message`
--


--
-- Dumping data for table `message_attachment`
--


--
-- Dumping data for table `passports`
--

INSERT INTO `passports` (`passport_id`, `user_id`, `series`, `number`, `birthday`, `given_by`, `given_date`, `region_id`, `city_id`, `street`, `house`, `flat`) VALUES
(1, 3, 1234, 123456, '2011-02-01', 'Орловским РОВД', '2011-02-10', 50, 690, 'Старо-Московское шоссе', '2', '43');

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `app_id`, `amount`, `created`) VALUES
(1, 2, '1600.00', '2011-02-13 23:53:56'),
(2, 2, '80.00', '2011-02-18 11:38:42'),
(3, 4, '45.00', '2011-02-18 12:45:24'),
(4, 4, '15.00', '2011-02-20 13:29:33'),
(5, 4, '20.00', '2011-02-20 13:51:09');

--
-- Dumping data for table `phones`
--

INSERT INTO `phones` (`phones_id`, `user_id`, `stationary`, `mobile`) VALUES
(1, 3, '718044', '+7 (905) 165-08-33');

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `title`, `labour_intensive`, `edu_type`, `paid_type`, `responsible_teacher`, `cost`, `number`) VALUES
(1, 'Бесплатное направление', 100, 'direction', 'free', NULL, NULL, 0),
(2, 'Платное направление', 300, 'direction', 'paid', NULL, '100.00', 0),
(3, 'Информационные технологии в сфере профессиональных коммуникаций', 498, 'direction', 'paid', NULL, '13000.00', 0);

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `test_id`, `type`, `data`) VALUES
(1, 1, 'pick-one', 'a:3:{s:8:"question";s:44:"Как расшифровывается PHP?";s:7:"answers";a:4:{i:0;s:6:"asdasd";i:1;s:5:"asdad";i:2;s:8:"asdadssd";i:3;s:9:"asdasdasd";}s:14:"correct_answer";i:1;}'),
(2, 1, 'pick-one', 'a:3:{s:8:"question";s:15:"Автор PHP?";s:7:"answers";a:4:{i:0;s:6:"asdasd";i:1;s:5:"asddf";i:2;s:4:"dfdf";i:3;s:6:"dsdasa";}s:14:"correct_answer";i:3;}'),
(3, 1, 'pick-one', 'a:3:{s:8:"question";s:28:"Год создания PHP?";s:7:"answers";a:4:{i:0;s:4:"1234";i:1;s:4:"1245";i:2;s:4:"1989";i:3;s:4:"1995";}s:14:"correct_answer";i:1;}'),
(4, 1, 'pick-one', 'a:3:{s:8:"question";s:28:"Предназначение";s:7:"answers";a:4:{i:0;s:6:"ячс";i:1;s:6:"ыфв";i:2;s:6:"ыва";i:3;s:10:"ываыв";}s:14:"correct_answer";i:1;}');

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `discipline_id`, `title`, `number`) VALUES
(1, 3, 'Раздел 1', 1),
(2, 3, 'Раздел 2', 2),
(3, 3, 'Раздел 3', 3),
(4, 1, 'Название раздела', 1),
(5, 6, 'Основы сайтостроения', 1),
(6, 6, 'Язык разметки гипертекста HTML', 2),
(7, 6, 'Основы WEB-дизайна', 3),
(8, 6, 'Технологический процесс разработки WEB-страницы. Основные этапы создания WEB-сайта ', 4),
(9, 6, 'Размещение WEB-сайта в сети Интернет', 5),
(10, 6, 'Сопровождение и реклама WEB-сайта ', 6),
(11, 2, 'Название раздела', 1),
(12, 1, 'jhggg1', 2);

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `theme`, `num_questions`, `time_limit`, `attempts_limit`, `errors_limit`) VALUES
(1, 'PHP', 3, 360, 1, 1);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `login`, `passwd`, `role`, `email`, `surname`, `name`, `patronymic`, `status`, `curator`) VALUES
(1, 'admin', 'bd945caf6e009b23a4998106f0be8d3a', 'admin', 'admin@remote-edu.localhost', 'Админов', 'Админ', 'Админович', 'active', NULL),
(2, 'teacher', 'bd945caf6e009b23a4998106f0be8d3a', 'teacher', 'teacher@remote-edu.localhost', 'Преподов', 'Препод', 'Преподович', 'active', NULL),
(3, 'student', 'bd945caf6e009b23a4998106f0be8d3a', 'student', 'student@remote-edu.localhost', 'Джигурда', 'Дмитрий', 'Владимирович', 'active', 2),
(5, 'student1', 'bd945caf6e009b23a4998106f0be8d3a', 'student', 'steveg0912@gmail.com', 'Фы', 'Фы', 'Фы', 'inactive', NULL),
(6, 'student2', 'bd945caf6e009b23a4998106f0be8d3a', 'student', 'steve111@yandex.ru', 'Лемнев', 'Сергей', 'Владимирович', 'active', NULL);

