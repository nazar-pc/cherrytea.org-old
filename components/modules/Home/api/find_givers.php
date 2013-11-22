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
if (
	!$User->admin() && !$Drivers->active($User->id)
) {
	error_code(403);
	return;
}
$Page		= Page::instance();
$params		= [];
if (isset($_GET['date']) && $_GET['date']) {
	$date			= _trim(explode('.', $_GET['date']));
	$params['date']	= mktime(0, 0, 0, $date[1], $date[0], $date[2]);
	unset($date);
}
if (isset($_GET['time']) && $_GET['time']) {
	$params['time']	= _trim(explode('-', str_replace(':', '.', $_GET['time'])));
}
if (isset($_GET['reserved']) && $_GET['reserved']) {
	$params['reserved']	= 1;
}
$Page->json(
	Goods::instance()->search($params, $User->id)
);