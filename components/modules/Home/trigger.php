<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			h,
			cs\Config,
			cs\Mail,
			cs\User,
			cs\Trigger;
$driver	= 0;
Trigger::instance()->register(
	'HybridAuth/registration/before',
	function () use (&$driver) {
		$driver	= User::instance()->get_session_data('driver') ? 1 : 0;
	}
)->register(
	'HybridAuth/add_session/after',
	function () use ($driver) {
		$User	= User::instance();
		if ($User->get_data('driver') === false) {
			$User->set_data('driver', $driver);
			Drivers::instance()->add($User->id);
			Mail::instance()->send_to(
				Config::instance()->core['admin_email'],
				'На CherryTea.org новий водій!)',
				h::p('На <a href="http://cherrytea.org">CherryTea.org</a> зареєструвався новий водій <b>'.$User->username().'</b>, він чекає активації!')
			);
		}
	}
);