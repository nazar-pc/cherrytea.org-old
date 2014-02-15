INSERT IGNORE INTO `[prefix]volunteers`
(`id`, `reputation`)
SELECT `id`, 0 FROM `[prefix]users` WHERE `id` > 2;
