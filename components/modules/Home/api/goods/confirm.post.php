<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Index,
			cs\Page,
			cs\User;
$User	= User::instance();
if ($User->guest()) {
	error_code(403);
	return;
}
$Index	= Index::instance();
if (!isset($Index->route_ids[0], $_POST['confirmation_code'])) {
	error_code(400);
	return;
}
$Goods		= Goods::instance();
$added_by	= array_column($Goods->unconfirmed($User->id), 'id');
if (!in_array($Index->route_ids[0], $added_by)) {
	error_code(403);
	return;
}
$driver = Volunteers::instance()->get_driver_by_code($_POST['confirmation_code']);
if (!$driver) {
	error_code(404);
	Page::instance()->error('Водія з таким кодом не знайдено');
	return;
}
if (!$Goods->set_driver($Index->route_ids[0], $driver['id'])) {
	error_code(500);
}
