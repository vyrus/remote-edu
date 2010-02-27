SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(64) character set utf8 collate utf8_bin NOT NULL,
  `passwd` varchar(32) character set utf8 collate utf8_bin default NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `email` varchar(256) NOT NULL,
  `surname` varchar(64) default NULL,
  `name` varchar(64) default NULL,
  `patronymic` varchar(64) default NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `users` ADD `curator` int;

DROP TABLE IF EXISTS `materials`;
CREATE TABLE `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `discipline_id` int(11) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `number` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `disciplines`;
CREATE TABLE `disciplines` (
  `discipline_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `coef` tinyint(4) DEFAULT NULL,
  `labour_intensive` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`discipline_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `disciplines` ADD `responsible_teacher` int;

DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `labour_intensive` smallint(6) DEFAULT NULL,
  `edu_type` enum('direction','course') DEFAULT NULL,
  `paid_type` enum('free','paid') DEFAULT NULL,
  PRIMARY KEY (`program_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `programs` ADD `responsible_teacher` int;

-- 
-- Структура таблицы `regions`
-- 

DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `region_id` int(10) unsigned NOT NULL auto_increment,
  `code` tinyint(2) unsigned zerofill NOT NULL,
  `name` char(40) NOT NULL,
  PRIMARY KEY  (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Структура таблицы `localities`
-- 

DROP TABLE IF EXISTS `localities`;
CREATE TABLE `localities` (
  `locality_id` int(10) unsigned NOT NULL auto_increment,
  `region_id` int(10) unsigned NOT NULL,
  `code` smallint(3) unsigned zerofill NOT NULL,
  `name` char(40) NOT NULL,
  `type` enum('аал','арбан','аул','волость','высел','г','городок','д','дп','ж/д_будка','ж/д_казарм','ж/д_оп','ж/д_платф','ж/д_пост','ж/д_рзд','ж/д_ст','заимка','казарма','кв-л','кордон','кп','м','мкр','нп','остров','п','п/о','п/р','п/ст','пгт','погост','починок','промзона','рзд','рп','с','с/а','с/о','с/п','с/с','сл','снт','ст','ст-ца','тер','у','х') NOT NULL,
  PRIMARY KEY  (`locality_id`),
  KEY `fk_localities_regions` (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Структура таблицы `edu_docs`
-- 

DROP TABLE IF EXISTS `edu_docs`;
CREATE TABLE `edu_docs` (
  `edu_doc_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `type` enum('diploma-high','diploma-medium','custom') NOT NULL,
  `custom_type` varchar(256) default NULL,
  `number` varchar(128) NOT NULL,
  `exit_year` year(4) NOT NULL,
  `speciality` varchar(256) NOT NULL,
  `qualification` varchar(256) NOT NULL,
  PRIMARY KEY  (`edu_doc_id`),
  KEY `fk_edu_docs_users` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `passports`
-- 

DROP TABLE IF EXISTS `passports`;
CREATE TABLE `passports` (
  `passport_id` int(10) unsigned NOT NULL auto_increment,
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
  `flat` varchar(8) default NULL,
  PRIMARY KEY  (`passport_id`),
  KEY `fk_passports_users` (`user_id`),
  KEY `fk_passports_regions` (`region_id`),
  KEY `fk_passports_localities` (`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `phones`
-- 

DROP TABLE IF EXISTS `phones`;
CREATE TABLE `phones` (
  `phones_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `stationary` varchar(16) default NULL,
  `mobile` varchar(32) default NULL,
  PRIMARY KEY  (`phones_id`),
  KEY `fk_phones_users` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

-- 
-- Структура таблицы `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE `applications` (
  `app_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `type` enum('program','discipline') NOT NULL,
  `status` enum('applied','declined','accepted','signed','paid') NOT NULL,
  `contract_filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

-- 
-- Структура таблицы `apps_history`
-- 

DROP TABLE IF EXISTS `apps_history`;
CREATE TABLE `apps_history` (
  `app_id` int(10) unsigned NOT NULL,
  `status` enum('applied','declined','accepted','signed','paid') NOT NULL,
  `modifed` datetime NOT NULL,
  PRIMARY KEY  (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Структура таблицы `materials_states`
--

CREATE TABLE IF NOT EXISTS `materials_states` (
  `student_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `state` enum('downloaded','studied') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Удаляем статус заявки "оплачена".
--
ALTER TABLE `applications` CHANGE `status` `status` ENUM('applied', 'declined', 'accepted', 'signed');
ALTER TABLE `apps_history` CHANGE `status` `status` ENUM('applied', 'declined', 'accepted', 'signed');

-- 
-- Структура таблицы `payments`
-- 

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int(10) unsigned NOT NULL auto_increment,
  `app_id` int(10) unsigned NOT NULL,
  `amount` float default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`payment_id`),
  KEY `fk_payments_applications` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `read` enum('read','unread') DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- Изменяем тип поля, чтобы можно было гарантировать точность чисел. Максимальное
-- значените для DECIMAL(9, 2) составляет 9999999.99.
--
ALTER TABLE `payments` CHANGE `amount` `amount` DECIMAL(9, 2) UNSIGNED DEFAULT NULL;

--
-- Добавляем поле для цен направлений/курсов.
--
ALTER TABLE `programs` ADD `cost` DECIMAL(9, 2) UNSIGNED DEFAULT NULL;

--
-- Добавляем поле для порядкового номера дисциплины.
--
ALTER TABLE `disciplines` ADD `serial_number` INT NOT NULL AFTER `program_id`;

--
-- Изменяем тип поля `state`. Статус последнего скачанного материала = 'last'
-- вместо 'studied'
--
ALTER TABLE `materials_states` CHANGE `state` `state` enum('downloaded','last') DEFAULT NULL;
alter table materials add type enum('lecture','practice','check') default 'lecture';

SET character_set_client = @saved_cs_client;