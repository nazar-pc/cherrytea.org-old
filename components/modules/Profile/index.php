<?php
/**
 * @package		Profile
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Profile;
use			h,
			cs\modules\Home\Drivers,
			cs\Page,
			cs\User;
$Page		= Page::instance();
$User		= User::instance();
$Drivers	= Drivers::instance();
if ($Drivers->active($User->id) || $User->admin()) {
	$driver	= $Drivers->get($User->id);
	$Page->content(
		h::{'section.profile-page article'}(
			h::h2('Ваш профіль').
			h::p('Персональний код водія: '.h::b($driver['code']))
		)
	);
} else {
	error_code(403);
	return;
}