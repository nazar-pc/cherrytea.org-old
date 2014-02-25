ALTER TABLE `[prefix]goods` ADD `date` VARCHAR( 255 ) NOT NULL ,
ADD `time` VARCHAR( 255 ) NOT NULL ,
ADD `coordinates` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `[prefix]goods` ADD `address` VARCHAR( 255 ) NOT NULL AFTER `time` ;
ALTER TABLE `[prefix]goods` ADD `phone` VARCHAR( 255 ) NOT NULL ;
