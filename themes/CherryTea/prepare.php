<?php
/**
 * @package		ClevereStyle CMS
 * @subpackage	CleverStyle theme
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
$Page	= Page::instance();
if (preg_match('/msie|trident/i',$_SERVER['HTTP_USER_AGENT'])) {
	$Page->Head	.= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
}
$Page->Head	.= '<meta name="viewport" content="width=950">';