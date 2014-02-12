<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
use	cs\User;
$User	= User::instance();//TODO needs change of modifying for united interface
if ($User->user()) {
	error_code(403);
	return;
}
$User->set_session_data('driver', isset($_POST['driver']) && $_POST['driver'] ? 1 : 0);
