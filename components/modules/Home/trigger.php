<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\User,
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
		if ($User->driver === false) {
			$User->driver	= $driver;
		}
	}
);