<?php
/**
 * @package		GiveMeALift
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use
	cs\modules\Home\Volunteers;
if (!isset($_GET['user_email']) || !filter_var($_GET['user_email'], FILTER_VALIDATE_EMAIL)) {
	error_code(400);
	return;
}
$User		= User::instance();
$user_id	= $User->get_id(hash('sha224', $_GET['user_email']));
if (!$user_id) {
	$result	= $User->registration($_GET['user_email'], false, false);
	if (!is_array($result)) {
		error_code(500);
		return;
	}
	$user_id	= $result['id'];
	unset($result);
}
Page::instance()->json(
	Volunteers::instance()->get($user_id)['code']
);
