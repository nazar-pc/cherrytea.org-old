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
			cs\Trigger,
			cs\User,
			h;
Trigger::instance()
	->register(
		'admin/System/components/modules/install/process',
		function ($data) {
			if ($data['name'] != 'Home') {
				return;
			}
			$Config	= Config::instance();
			$Config->module('Update')->version	= 10;
			$Config->save();
			return;
		}
	)->register(
		'System/User/registration/after',
		function ($data) {
			$Volunteers	= Volunteers::instance();
			$Volunteers->set_driver($data['id'], 'yes');
			Mail::instance()->send_to(
				Config::instance()->core['admin_email'],
				'На CherryTea.org зареєструвався новий водій!)',
				h::p('На <a href="http://cherrytea.org">CherryTea.org</a> зареєструвався новий водій <b>'.User::instance()->username($data['id']).'</b>!')
			);
		}
	);
