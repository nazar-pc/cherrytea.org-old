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
	cs\modules\Home\Goods,
	cs\modules\Home\Volunteers;
if (!isset($_GET['good_id'])) {
	error_code(400);
}
if (isset($_GET['user_id']) && $_GET['user_id']) {
	$user_id = $_GET['user_id'];
} elseif (isset($_GET['user_email']) && filter_var($_GET['user_email'], FILTER_VALIDATE_EMAIL)) {
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
} else {
	error_code(400);
	return;
}
$Page	= Page::instance();
$good	= Goods::instance()->get($_GET['good_id']);
unset(
	$good['id'],
	$good['giver'],
	$good['added'],
	$good['driver'],
	$good['given'],
	$good['reserved'],
	$good['reserved_driver'],
	$good['success'],
	$good['profile_link']
);
$Page->json([
	'good'			=> $good,
	'driver_code'	=> Volunteers::instance()->get($user_id)['code']
]);
