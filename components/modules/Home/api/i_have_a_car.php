<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Config,
			cs\Mail,
			cs\User,
			h;
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
$Volunteers->set_driver($User->id, 'yes');
Mail::instance()->send_to(
	Config::instance()->core['admin_email'],
	'На CherryTea.org новий водій!)',
	h::p('На <a href="http://cherrytea.org">CherryTea.org</a> <b>'.$User->username().'</b> виявив(ла) бажання стати водієм! Водія вже активовано, при потребі його можна деактивувати.')
);
