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

DROP TABLE IF EXISTS `disciplines`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `disciplines` (
  `discipline_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `coef` tinyint(4) DEFAULT NULL,
  `labour_intensive` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`discipline_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `programs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `labour_intensive` smallint(6) DEFAULT NULL,
  `edu_type` enum('direction','course') DEFAULT NULL,
  `paid_type` enum('free','paid') DEFAULT NULL,
  PRIMARY KEY (`program_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;