<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Page,
			cs\User;
$User		= User::instance();
$Volunteers	= Volunteers::instance();
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
$goods		= Goods::instance()->search($params, $User->id);
if (
	!$User->admin() && !$Volunteers->is_driver($User->id)
) {
	foreach ($goods as &$good) {
		$good	= [
			'lat'	=> $good['lat'],
			'lng'	=> $good['lng']
		];
	}
}
$Page->json($goods);
