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