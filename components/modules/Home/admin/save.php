<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			h,
			cs\DB,
			cs\Index,
			cs\User;
$Index		= Index::instance();
$Volunteers	= Volunteers::instance();
$Goods		= Goods::instance();
/**
 * For drivers
 */
if (isset($_POST['driver_activate'])) {
	$Index->save(
		$Volunteers->set_driver($_POST['driver_activate'], 'yes')
	);
} elseif (isset($_POST['driver_deactivate'])) {
	$Index->save(
		$Volunteers->set_driver($_POST['driver_deactivate'], 'no')
	);
} elseif (isset($_POST['not_driver'])) {
	$Index->save(
		$Volunteers->set_driver($_POST['not_driver'], 'unknown')
	);
}
/**
 * For goods
 */
if (isset($_POST['good_success'])) {
	$Index->save(
		$Goods->set_success($_POST['good_success'], 1)
	);
} elseif (isset($_POST['good_failed'])) {
	$Index->save(
		$Goods->set_success($_POST['good_failed'], 0)
	);
} elseif (isset($_POST['is_driver'])) {
	$Index->save(
		$Volunteers->set_driver($_POST['is_driver'], 'yes')
	);
}
