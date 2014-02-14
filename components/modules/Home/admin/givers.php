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
$Index->buttons	= false;
$User			= User::instance();
$givers			= Volunteers::instance()->get_givers();
$Index->content(
	h::{'table.admin-home-page.cs-table.cs-center-all'}(
		h::{'thead tr th'}(
			'Id',
			'Ім’я',
			'Сторінка в соціальній мережі',
			'Репутація',
			'Дія'
		).
		h::{'tbody tr| td'}(array_map(
			function ($giver) use ($User) {
				return [
					$giver['id'],
					$User->username($giver['id']),
					h::a(
						$giver['profile'],
						[
							'href'		=> $giver['profile'],
							'target'	=> '_blank'
						]
					),
					$giver['reputation'],
					h::{'button[type=submit][name=is_driver]'}(
						'Це водій',
						[
							'value'	=> $giver['id']
						]
					)
				];
			},
			$givers
		))
	)
);
