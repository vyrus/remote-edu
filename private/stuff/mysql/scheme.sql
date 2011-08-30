-- phpMyAdmin SQL Dump
-- version 3.3.7deb6
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 23 2011 г., 18:12
-- Версия сервера: 5.1.49
-- Версия PHP: 5.3.3-7+squeeze3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- База данных: `remote_edu`
--

-- --------------------------------------------------------

--
-- Структура таблицы `applications`
--

CREATE TABLE `applications` (
  `app_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `type` enum('program','discipline') NOT NULL,
  `status` enum('applied','declined','accepted','signed','prepaid','finished') DEFAULT NULL,
  `contract_filename` varchar(255) DEFAULT NULL,
  `date_app` date DEFAULT NULL,
  PRIMARY KEY (`app_id`),
  KEY `index_object` (`object_id`),
  KEY `index_user` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Структура таблицы `apps_history`
--

CREATE TABLE `apps_history` (
  `app_id` int(10) unsigned NOT NULL,
  `status` enum('applied','declined','accepted','signed','prepaid','finished') NOT NULL DEFAULT 'applied',
  `modifed` datetime NOT NULL,
  PRIMARY KEY (`app_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
/*
--
-- Структура таблицы `checkpoints`
--

CREATE TABLE `checkpoints` (
  `section_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `text` text,
  `type` set('lab','control','test') NOT NULL,
  `test_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `checkpoints_students`
--

CREATE TABLE `checkpoints_students` (
  `section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`section_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

-- --------------------------------------------------------

--
-- Структура таблицы `controls`
--

CREATE TABLE `controls` (
  `control_id` int(11) NOT NULL AUTO_INCREMENT,
  `control_material_id` int(11) DEFAULT NULL,
  `section_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `auto_set_credit` tinyint(1) NOT NULL DEFAULT '0',
  `control_material_type` enum('practice','control','test','credit') DEFAULT NULL,
  PRIMARY KEY (`control_id`),
  KEY `index_materail_id` (`control_material_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

--
-- Структура таблицы `controls_students`
--

CREATE TABLE `controls_students` (
  `control_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `mark` enum('no','m2','yes','m3','m4','m5') NOT NULL,
  `control_date` datetime DEFAULT NULL,
  PRIMARY KEY (`control_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `credited_sections_students`
--

CREATE TABLE `credited_sections_students` (
  `section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`section_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `disciplines`
--

CREATE TABLE `disciplines` (
  `discipline_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `serial_number` int(11) NOT NULL,
  `title` varchar(256) DEFAULT NULL,
  `coef` tinyint(4) DEFAULT NULL,
  `labour_intensive` smallint(6) DEFAULT NULL,
  `responsible_teacher` int(11) DEFAULT NULL,
  PRIMARY KEY (`discipline_id`),
  KEY `index_program` (`program_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `edu_docs`
--

CREATE TABLE `edu_docs` (
  `edu_doc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `type` enum('diploma-high','diploma-medium','custom') NOT NULL,
  `custom_type` varchar(256) DEFAULT NULL,
  `number` varchar(128) NOT NULL,
  `exit_year` year(4) NOT NULL,
  `speciality` varchar(256) NOT NULL,
  `qualification` varchar(256) NOT NULL,
  PRIMARY KEY (`edu_doc_id`),
  KEY `fk_edu_docs_users` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Структура таблицы `examinations`
--

CREATE TABLE `examinations` (
  `examination_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `num_errors` int(11) NOT NULL,
  `num_questions` int(11) NOT NULL,
  `passed` enum('true','false') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`examination_id`),
  KEY `index_user_test` (`user_id`,`test_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `extra_attempts`
--

CREATE TABLE `extra_attempts` (
  `user_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `extra_attempts` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`test_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `localities`
--

CREATE TABLE `localities` (
  `locality_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` int(10) unsigned NOT NULL,
  `code` smallint(3) unsigned zerofill NOT NULL,
  `name` char(40) NOT NULL,
  `type` enum('аал','арбан','аул','волость','высел','г','городок','д','дп','ж/д_будка','ж/д_казарм','ж/д_оп','ж/д_платф','ж/д_пост','ж/д_рзд','ж/д_ст','заимка','казарма','кв-л','кордон','кп','м','мкр','нп','остров','п','п/о','п/р','п/ст','пгт','погост','починок','промзона','рзд','рп','с','с/а','с/о','с/п','с/с','сл','снт','ст','ст-ца','тер','у','х') NOT NULL,
  PRIMARY KEY (`locality_id`),
  KEY `fk_localities_regions` (`region_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=691 ;

-- --------------------------------------------------------

--
-- Структура таблицы `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `type` enum('lecture','practice','control') DEFAULT 'lecture',
  `uploader` int(11) DEFAULT NULL,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_section` (`section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Структура таблицы `materials_states`
--

CREATE TABLE `materials_states` (
  `student_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `state` enum('downloaded','last') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `read` enum('read','unread') DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `index_to_from` (`to`,`from`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `message_attachment`
--

CREATE TABLE `message_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` int(11) DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `opened_sections_students`
--

CREATE TABLE `opened_sections_students` (
  `section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  PRIMARY KEY (`section_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `passports`
--

CREATE TABLE `passports` (
  `passport_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `series` smallint(4) unsigned zerofill NOT NULL,
  `number` mediumint(6) unsigned zerofill NOT NULL,
  `birthday` date NOT NULL,
  `given_by` varchar(256) NOT NULL,
  `given_date` date NOT NULL,
  `region_id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `street` varchar(64) NOT NULL,
  `house` varchar(8) NOT NULL,
  `flat` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`passport_id`),
  KEY `fk_passports_users` (`user_id`),
  KEY `fk_passports_regions` (`region_id`),
  KEY `fk_passports_localities` (`city_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(10) unsigned NOT NULL,
  `amount` decimal(9,2) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payments_applications` (`app_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Структура таблицы `phones`
--

CREATE TABLE `phones` (
  `phones_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `stationary` varchar(16) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`phones_id`),
  KEY `fk_phones_users` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Структура таблицы `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `labour_intensive` smallint(6) DEFAULT NULL,
  `edu_type` enum('direction','course') DEFAULT NULL,
  `paid_type` enum('free','paid') DEFAULT NULL,
  `responsible_teacher` int(11) DEFAULT NULL,
  `cost` decimal(9,2) unsigned DEFAULT NULL,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`program_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `questions`
--

CREATE TABLE `questions` (
  `question_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` int(10) unsigned NOT NULL,
  `type` enum('pick-one') NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`question_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `regions`
--

CREATE TABLE `regions` (
  `region_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` tinyint(2) unsigned zerofill NOT NULL,
  `name` char(40) NOT NULL,
  PRIMARY KEY (`region_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=99 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `discipline_id` int(11) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `number` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`section_id`),
  KEY `index_discipline` (`discipline_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tests`
--

CREATE TABLE `tests` (
  `test_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `theme` varchar(256) NOT NULL,
  `num_questions` int(11) NOT NULL,
  `time_limit` int(11) NOT NULL,
  `attempts_limit` int(11) NOT NULL,
  `errors_limit` int(11) NOT NULL,
  PRIMARY KEY (`test_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `passwd` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `email` varchar(256) NOT NULL,
  `surname` varchar(64) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `patronymic` varchar(64) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `curator` int(11) DEFAULT NULL,
  `date_reg` date DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

