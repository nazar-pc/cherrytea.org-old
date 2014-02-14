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
$goods			= Goods::instance()->for_approving();
$Index->content(
	h::{'table.admin-home-page.cs-table.cs-center-all'}(
		h::{'thead tr th'}(
			'Віддав',
			'Відвозить',
			'Отримав',
			'Коментар',
			'Дія'
		).
		h::{'tbody tr| td'}(array_map(
			function ($good) use ($User) {
				return [
					$good['username'],
					$User->username($good['driver']),
					date('d-m-Y H:i', $good['given']),
					$good['comment'],
					h::{'button[type=submit][name=good_success]'}(
						'Успішно',
						[
							'value'	=> $good['id']
						]
					).
					h::{'button[type=submit][name=good_failed]'}(
						'Неуспішно',
						[
							'value'	=> $good['id']
						]
					)
				];
			},
			$goods
		))
	)
);
