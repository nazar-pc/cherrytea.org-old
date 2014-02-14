<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			h,
			cs\Index,
			cs\User;
$Index			= Index::instance();
$Index->main_sub_menu	= [
	[
		'Водії',
		[
			'href'	=> 'admin/Home/drivers'
		]
	],
	[
		'Мають речі',
		[
			'href'	=> 'admin/Home/givers'
		]
	],
	[
		'Відвезені речі',
		[
			'href'	=> 'admin/Home/goods'
		]
	]
];
