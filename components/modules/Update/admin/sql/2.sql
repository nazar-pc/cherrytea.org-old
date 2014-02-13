ALTER TABLE `[prefix]drivers` CHANGE `active` `driver` SET( 'unknown', 'requested', 'no', 'yes' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'unknown';
INSERT IGNORE INTO `[prefix]drivers`
(`id`, `reputation`)
SELECT `id`, `reputation`
FROM `[prefix]givers`;
DROP TABLE `[prefix]givers`;
RENAME TABLE `8b842_drivers` TO `8b842_volunteers`;
