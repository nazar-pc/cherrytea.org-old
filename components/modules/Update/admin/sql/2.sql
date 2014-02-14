ALTER TABLE `[prefix]drivers` CHANGE `active` `active` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
UPDATE `[prefix]drivers` SET `active` = 'unknown' WHERE `active` = '-1';
UPDATE `[prefix]drivers` SET `active` = 'no' WHERE `active` = '0';
UPDATE `[prefix]drivers` SET `active` = 'yes' WHERE `active` = '1';
ALTER TABLE `[prefix]drivers` CHANGE `active` `driver` SET( 'unknown', 'requested', 'no', 'yes' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unknown';
INSERT IGNORE INTO `[prefix]drivers`
(`id`, `reputation`)
SELECT `id`, `reputation`
FROM `[prefix]givers`;
DROP TABLE `[prefix]givers`;
RENAME TABLE `[prefix]drivers` TO `[prefix]volunteers`;
