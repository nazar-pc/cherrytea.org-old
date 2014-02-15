INSERT IGNORE INTO `[prefix]volunteers`
(`id`, `code`)
SELECT `id`, `id` FROM `[prefix]users` WHERE `id` > 2;
