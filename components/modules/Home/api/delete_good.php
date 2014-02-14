<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Page,
			cs\User;
$User		= User::instance();
$Goods		= Goods::instance();
if (!$User->admin()) {
	error_code(403);
	return;
}
if (!$Goods->delete($_POST['id'])) {
	error_code(500);
}
