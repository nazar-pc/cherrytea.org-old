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
$User	= User::instance();
$Goods	= Goods::instance();
if ($User->guest()) {
	error_code(403);
	return;
}
if (!isset($_POST['comment'], $_POST['name'], $_POST['phone'], $_POST['address'], $_POST['coordinates'], $_POST['date'], $_POST['time'])) {
	error_code(400);
	return;
}
if (!$Goods->add(
	$User->id,
	$_POST['comment'],
	$_POST['name'],
	$_POST['phone'],
	$_POST['address'],
	$_POST['coordinates'],
	$_POST['date'],
	$_POST['time']
)) {
	error_code(500);
}
