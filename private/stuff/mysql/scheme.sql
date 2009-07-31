-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `email` varchar(256) NOT NULL,
  `surname` varchar(64) NULL,
  `name` varchar(64) NULL,
  `patronymic` varchar(64) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;