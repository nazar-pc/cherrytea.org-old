<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\Index,
			cs\User;
$Index	= Index::instance();
$User	= User::instance();
$Goods	= Goods::instance();
if (!isset($Index->route_ids[0])) {
	error_code(400);
	return;
}
$good	= $Goods->get($Index->route_ids[0]);
if (!(
	$User->admin() || $good['giver'] == $User->id
)) {
	error_code(403);
	return;
}
if (!$Goods->delete($Index->route_ids[0])) {
	error_code(500);
}
