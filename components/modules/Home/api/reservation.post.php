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
if (
	!$User->admin() && !$Drivers->active($User->id)
) {
	error_code(403);
	return;
}
Goods::instance()->add_reservation($_POST['id'], $User->id);
