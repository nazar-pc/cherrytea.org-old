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
if (isset($_POST['activate'])) {
	$Index->save(
		$Drivers->activate($_POST['activate'])
	);
} elseif (isset($_POST['deactivate'])) {
	$Index->save(
		$Drivers->deactivate($_POST['deactivate'])
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