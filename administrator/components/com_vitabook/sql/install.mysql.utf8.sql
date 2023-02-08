CREATE TABLE IF NOT EXISTS `#__vitabook_messages` (
  `id` int(99) NOT NULL AUTO_INCREMENT,
  `parent_id` int(99) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) NOT NULL DEFAULT '0',
  `jid` int(99) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `site` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__vitabook_messages` VALUES(1, 0, 0, 1, 0, 0, 'ROOT', NULL, NULL, NULL, '0000-00-00 00:00:00', '', '', 1, 1);