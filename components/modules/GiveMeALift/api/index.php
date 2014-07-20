<?php
/**
 * @package		GiveMeALift
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
if (!isset($_REQUEST['secret']) || $_REQUEST['secret'] !== Core::instance()->GiveMeALift) {
	error_code(403);
}
