ALTER TABLE `8b842_goods` ADD `reserved` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Time till which good is reserved' AFTER `given` ,
ADD INDEX ( `reserved` ) ;
ALTER TABLE `8b842_goods` ADD `reserved_driver` INT UNSIGNED NOT NULL AFTER `reserved` ,
ADD INDEX ( `reserved_driver` ) ;