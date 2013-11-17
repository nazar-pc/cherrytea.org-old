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
			cs\Index,
			cs\User;
$Index		= Index::instance();
$Drivers	= Drivers::instance();
if (isset($_POST['activate'])) {
	$Index->save(
		$Drivers->activate($_POST['activate'])
	);
} elseif (isset($_POST['deactivate'])) {
	$Index->save(
		$Drivers->deactivate($_POST['deactivate'])
	);
}