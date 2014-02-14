<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\User;
$User		= User::instance();
$Volunteers	= Volunteers::instance();
if (
	!$User->admin() && !$Volunteers->is_driver($User->id)
) {
	error_code(403);
	return;
}
Goods::instance()->add_reservation($_POST['id'], $User->id);
