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
$Givers		= Givers::instance();
$Goods		= Goods::instance();
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
	$Givers->get($_POST['not_driver']);
	$Index->save(
		(bool)$User->set_data('driver', 0, $_POST['not_driver'])
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
	DB::instance()->q(
		"DELETE FROM `[prefix]givers` WHERE `id` = '%s'",
		$_POST['is_driver']
	);
	$Drivers->add($_POST['is_driver']);
	$Drivers->activate($_POST['is_driver']);
	$Index->save(
		(bool)$User->set_data('driver', 1, $_POST['is_driver'])
	);
}