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
$Drivers	= Drivers::instance();
$User		= User::instance();
/**
 * For drivers
 */
if (isset($_POST['driver_activate'])) {
	$Index->save(
		$Drivers->activate($_POST['driver_activate'])
	);
} elseif (isset($_POST['driver_deactivate'])) {
	$Index->save(
		$Drivers->deactivate($_POST['driver_deactivate'])
	);
} elseif (isset($_POST['not_driver'])) {
	DB::instance()->q(
		"DELETE FROM `[prefix]drivers` WHERE `id` = '%s'",
		$_POST['not_driver']
	);
	$Index->save(
		(bool)$User->set_data('driver', 0, $_POST['not_driver'])
	);
}
/**
 * For goods
 */
$Goods	= Goods::instance();
if (isset($_POST['good_success'])) {
	$Index->save(
		$Goods->set_success($_POST['good_success'], 1)
	);
} elseif (isset($_POST['good_failed'])) {
	$Index->save(
		$Goods->set_success($_POST['good_failed'], 0)
	);
}