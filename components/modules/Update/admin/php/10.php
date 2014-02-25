<?php
/**
 * @package		Blogs
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2011-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			cs\User;
/**
 *	@var \cs\DB\_Abstract $cdb
 */
$cdb	= DB::instance();
$User	= User::instance();
$givers	= $cdb->qfa(
	"SELECT `id`, `giver`
	FROM `[prefix]goods`"
);
foreach ($givers as $g) {
	$cdb->q(
		"UPDATE `[prefix]goods`
		SET
			`phone`		= '%s',
			`address`	= '%s'
		WHERE `id` = '%s'
		LIMIT 1",
		$User->get_data('phone', $g['giver']) ?: '',
		$User->get_data('address', $g['giver']) ?: '',
		$g['id']
	);
}
