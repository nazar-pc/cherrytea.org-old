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
Mail::instance()->send_to(
	Config::instance()->core['admin_email'],
	'На CherryTea.org з’явились нові речі!)',
	h::p('На <a href="http://cherrytea.org">CherryTea.org</a> <b>'.xap($_POST['name']).'</b> додав нові речі!').
	h::p(xap($_POST['phone'])).
	h::p(xap($_POST['address'])).
	h::p(xap($_POST['comment']))
);
