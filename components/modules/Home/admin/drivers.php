<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			h,
			cs\Index,
			cs\User;
$Index			= Index::instance();
$Index->buttons	= false;
$User			= User::instance();
$drivers		= Volunteers::instance()->get_drivers();
$Index->content(
	h::{'table.admin-home-page.cs-table.cs-center-all'}(
		h::{'thead tr th'}(
			'Id',
			'Ім’я',
			'Сторінка в соціальній мережі',
			'Репутація',
			'Дія'
		).
		h::{'tbody tr'}(array_map(
			function ($driver) use ($User) {
				switch ($driver['active']) {
					case '1':
						$class	= 'uk-alert-success';
						$action	= h::{'button[type=submit][name=driver_deactivate]'}(
							'Деактивувати',
							[
								'value'	=> $driver['id']
							]
						);
					break;
					case '0':
						$class = 'uk-alert-danger';
						$action	= h::{'button[type=submit][name=driver_activate]'}(
							'Активувати',
							[
								'value'	=> $driver['id']
							]
						);
					break;
					default:
						$class = false;
						$action	=
							h::{'button[type=submit][name=driver_deactivate]'}(
								'Деактивувати',
								[
									'value'	=> $driver['id']
								]
							).
							h::{'button[type=submit][name=driver_activate]'}(
								'Активувати',
								[
									'value'	=> $driver['id']
								]
							);
				}
				return [
					h::td([
						$driver['id'],
						$User->username($driver['id']),
						h::a(
							$driver['profile'],
							[
								'href'		=> $driver['profile'],
								'target'	=> '_blank'
							]
						),
						$driver['reputation'],
						$action.
						h::{'button[type=submit][name=not_driver]'}(
							'Не водій',
							[
								'value'	=> $driver['id']
							]
						)
					]),
					[
						'class'	=> $class
					]
				];
			},
			$drivers
		))
	)
);
