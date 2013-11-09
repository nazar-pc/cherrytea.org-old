<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			h;
$Page		= Page::instance();
$User		= User::instance();
$content	= '';
if (!$User->guest()) {
	$content	=
		h::{'h2.cs-center'}('Вхід на сайт').
		h::{'div.home-page-registration'}(
			h::div(
				h::h2('У мене є речі').
				h::a(
					h::icon('facebook').
					'Увійти через Facebook'
				).
				h::a(
					h::icon('vk').
					'Увійти через VK'
				)
			).
			h::div(
				h::h2('У мене є авто').
				h::a(
					h::icon('facebook').
					'Увійти через Facebook'
				).
				h::a(
					h::icon('vk').
					'Увійти через VK'
				)
			)
		);
}
$Page->content(
	h::{'section.home-page article'}(
		$content.
		h::{'p.cs-center.home-page-list-map-switcher input[type=radio]'}([
			'value'		=> ['list', 'map'],
			'in'		=> ['Список', 'Карта'],
			'checked'	=> 'list'
		]).
		h::{'div.home-page-filter.uk-form'}([
			h::{'div.uk-button-dropdown[data-uk-dropdown=]'}(
				h::button(
					h::icon('caret-down').
					'Район'
				).
				h::{'div.uk-dropdown ul.uk-nav.uk-nav-dropdown li| a'}(
					'Деснянський',
					'Дніпровський',
					'Печерський'
				)
			).
			h::input([
				'placeholder'	=> 'Дата'
			]).
			h::{'div.uk-button-dropdown[data-uk-dropdown=]'}(
				h::button(
					h::icon('caret-down').
					'Время'
				).
				h::{'div.uk-dropdown.uk-dropdown-width-4 div.uk-grid div.uk-width-1-4'}(
					h::{'ul.uk-nav.uk-nav-dropdown.uk-panel li| a'}(
						'6:00',
						'6:30',
						'7:00',
						'7:30',
						'8:00',
						'8:30',
						'9:00',
						'9:30',
						'10:00'
					),
					h::{'ul.uk-nav.uk-nav-dropdown.uk-panel li| a'}(
						'10:30',
						'11:00',
						'11:30',
						'12:00',
						'12:30',
						'13:00',
						'13:30',
						'14:00',
						'14:30'
					),
					h::{'ul.uk-nav.uk-nav-dropdown.uk-panel li| a'}(
						'15:00',
						'15:30',
						'16:00',
						'16:30',
						'17:00',
						'17:30',
						'18:00',
						'18:30',
						'19:00'
					),
					h::{'ul.uk-nav.uk-nav-dropdown.uk-panel li| a'}(
						'19:30',
						'20:00',
						'20:30',
						'21:00',
						'21:30',
						'22:00',
						'22:30',
						'23:00',
						'23:30'
					)
				)
			).
			h::{'button'}('Показати')
		])
	)
);