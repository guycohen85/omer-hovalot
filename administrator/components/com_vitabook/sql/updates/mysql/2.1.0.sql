ALTER TABLE `#__vitabook_messages` CHANGE  `name`  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__vitabook_messages` CHANGE  `site`  `site` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `#__vitabook_messages` CHANGE  `location`  `location` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `#__vitabook_messages` CHANGE  `ip`  `ip` VARCHAR( 39 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__vitabook_messages` ADD `activated` tinyint(1) NOT NULL DEFAULT '0';
UPDATE `#__vitabook_messages` SET `activated` = 1;
