<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\User;
$User		= User::instance();
$Drivers	= Drivers::instance();
$driver		= $Drivers->get($User->id);
if (
	!$User->admin() &&
	(
		!$driver || !$driver['active']
	)
) {
	error_code(403);
	return;
}
