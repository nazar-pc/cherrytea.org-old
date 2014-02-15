<?php
/**
 * @package		Blogs
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2011-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			cs\modules\Home\Volunteers;
/**
 *	@var \cs\DB\_Abstract $cdb
 */
$cdb	= DB::instance();
$givers	= $cdb->qfa(
	"SELECT `giver`, COUNT(`id`) AS `count`
	FROM `[prefix]goods`
	WHERE `success` = '1'
	GROUP BY `giver`"
);
$Volunteers	= Volunteers::instance();
foreach ($givers as $g) {
	$Volunteers->change_reputation($g['giver'], $g['count']);
}
