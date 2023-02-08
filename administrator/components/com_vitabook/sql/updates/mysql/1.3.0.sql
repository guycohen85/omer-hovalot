ALTER TABLE `#__vitabook_messages` DROP `asset_id`;
ALTER TABLE `#__vitabook_messages` ADD `location` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `site`;