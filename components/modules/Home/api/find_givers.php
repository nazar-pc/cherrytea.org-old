<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Page,
			cs\User;
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
$Page		= Page::instance();
$params		= [];
if ($_POST['date']) {
	$date			= _trim(explode('.', $_POST['date']));
	$params['date']	= mktime(0, 0, 0, $date[1], $date[0], $date[2]);
	unset($date);
}
if ($_POST['time']) {
	$params['time']	= _trim(str_replace(':', '.', $_POST['time']));
}
$Page->json(
	Goods::instance()->search($params)
);