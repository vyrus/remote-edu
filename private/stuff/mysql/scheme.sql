-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(10) NOT NULL auto_increment,
  `login` varchar(50) character set utf8 collate utf8_bin NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `fio` varchar(256) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;