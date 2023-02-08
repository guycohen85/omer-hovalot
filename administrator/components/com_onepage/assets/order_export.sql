CREATE TABLE IF NOT EXISTS `#__onepage_exported` (
  `id` bigint(20) NOT NULL auto_increment,
  `tid` int(11) NOT NULL,
  `localid` varchar(250) character set ascii NOT NULL,
  `status` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'NONE',
  `ai` varchar(20) NOT NULL,
  `specials` mediumtext NOT NULL,
  `path` varchar(512) NOT NULL,
  `cdate` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tid_3` (`tid`,`localid`,`status`),
  KEY `tid` (`tid`),
  KEY `ai` (`ai`),
  KEY `tid_2` (`tid`,`localid`),
  KEY `cdate` (`cdate`),
  KEY `cdate_2` (`tid`,`cdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;
CREATE TABLE IF NOT EXISTS `#__onepage_export_templates` (
  `tid` int(11) NOT NULL auto_increment,
  `file` varchar(255) NOT NULL,
  `name` varchar(255) default NULL,
  `type` enum('ORDER_DATA','ORDER_DATA_TXT','ORDERS','ORDERS_TXT') NOT NULL default 'ORDER_DATA',
  PRIMARY KEY  (`tid`),
  UNIQUE KEY `file` (`file`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102 ;
INSERT INTO `#__onepage_export_templates` (`tid`, `file`, `name`, `type`) VALUES
(100, 'invoice.ods', '', 'ORDER_DATA'),
(101, 'sek.ods', '', 'ORDER_DATA');
CREATE TABLE IF NOT EXISTS `#__onepage_export_templates_settings` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL default '0',
  `keyname` varchar(20) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `original` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `original` (`original`,`tid`,`keyname`),
  KEY `tid` (`tid`),
  KEY `tid_2` (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;
INSERT INTO `#__onepage_export_templates_settings` (`id`, `tid`, `keyname`, `value`, `original`) VALUES
(100, 100, 'tid_name', 'Invoice Example', ''),
(101, 100, 'tid_num', '0', ''),
(102, 100, 'tid_forward', '0', ''),
(103, 100, 'tid_back', '0', ''),
(104, 100, 'tid_foreign', '0', ''),
(105, 100, 'tid_autocreate', '0', ''),
(106, 100, 'tid_enabled', '1', ''),
(107, 100, 'tid_special', '1', ''),
(108, 100, 'tid_specials', '1', ''),
(109, 100, 'tid_ai', '1', ''),
(110, 100, 'tid_shared', '', ''),
(111, 100, 'tid_foreigntemplate', '101', ''),
(112, 100, 'tid_email', '1', ''),
(113, 100, 'tid_nummax', '', ''),
(114, 100, 'tid_itemmax', '', ''),
(115, 100, 'tid_type', 'ORDER_DATA', ''),
(116, 100, 'tid_emailsubject', 'Invoice Example', ''),
(117, 100, 'tid_emailbody', 'Please review the attachment of this email. ', ''),
(118, 101, 'tid_name', 'Postal Cheque Example', ''),
(119, 101, 'tid_special', '0', ''),
(120, 101, 'tid_ai', '0', ''),
(121, 101, 'tid_num', '0', ''),
(122, 101, 'tid_forward', '0', ''),
(123, 101, 'tid_back', '0', ''),
(124, 101, 'tid_enabled', '0', ''),
(125, 101, 'tid_foreign', '0', ''),
(126, 101, 'tid_email', '0', ''),
(127, 101, 'tid_autocreate', '0', ''),
(128, 101, 'tid_specials', '1', ''),
(129, 101, 'tid_shared', '', ''),
(130, 101, 'tid_foreigntemplate', '100', ''),
(131, 101, 'tid_nummax', '', ''),
(132, 101, 'tid_itemmax', '', ''),
(133, 101, 'tid_type', 'ORDER_DATA', ''),
(134, 101, 'tid_emailsubject', '', ''),
(135, 101, 'tid_emailbody', '', '');
