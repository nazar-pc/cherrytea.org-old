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
			cs\User,
			cs\Trigger;
Trigger::instance()->register(
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
);
