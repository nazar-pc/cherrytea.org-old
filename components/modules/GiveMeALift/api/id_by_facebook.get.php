<?php
/**
 * @package		GiveMeALift
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
if (!isset($_GET['facebook_id'], $_GET['facebook_profile_link'], $_GET['user_email']) || !filter_var($_GET['user_email'], FILTER_VALIDATE_EMAIL)) {
	error_code(400);
}
$cdb		= DB::instance()->{'0'}();
$Page		= Page::instance();
/**
 * @var User $User
 */
$User		= User::instance();
$user_id	= $cdb->qfs([
	"SELECT `id`
	FROM `[prefix]users_social_integration`
	WHERE
		`provider`		= '%s' AND
		`identifier`	= '%s' AND
		`profile`		= '%s'",
	'Facebook',
	$_GET['facebook_id'],
	$_GET['facebook_profile_link']
]);
if (!$user_id) {
	$result	= $User->registration($_GET['user_email'], false, false);
	if (!is_array($result)) {
		error_code(500);
		return;
	}
	$user_id	= $result['id'];
	$cdb->q(
		"INSERT INTO `[prefix]users_social_integration`
			(
				`id`,
				`provider`,
				`identifier`,
				`profile`
			) VALUES (
				'%s',
				'%s',
				'%s',
				'%s'
			)",
		$user_id,
		'Facebook',
		xap($_GET['facebook_id']),
		xap($_GET['facebook_profile_link'])
	);
	unset($result);
}
$Page->json($user_id);
