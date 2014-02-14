<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use	cs\User;
$User	= User::instance();
if ($User->guest()) {
	error_code(403);
	return;
}
$Volunteers	= Volunteers::instance();
$volunteer	= $Volunteers->get($User->id);
if (in_array($volunteer['driver'], ['no', 'yes'])) {
	error_code(400);
	return;
}
$Volunteers->set_driver($User->id, 'requested');
