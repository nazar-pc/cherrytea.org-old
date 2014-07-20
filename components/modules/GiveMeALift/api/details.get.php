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
if (!isset($_GET['good_id'], $_GET['user_id'])) {
	error_code(400);
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
	$good['username'],
	$good['profile_link']
);
$Page->json([
	'good'			=> $good,
	'driver_code'	=> Volunteers::instance()->get($_GET['user_id'])['code']
]);
