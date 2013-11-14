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
if ($User->guest()) {
	$Page->content(
		h::{'section.home-page article'}(
			h::{'h2.cs-center'}('Вхід на сайт').
			h::{'div.home-page-sign-in'}(
				h::div(
					h::h2('У мене є речі').
					h::{'a.fb'}(
						h::icon('facebook').
						'Увійти через Facebook'
					).
					h::{'a.vk'}(
						h::icon('vk').
						'Увійти через VK'
					)
				).
				h::div(
					h::h2('У мене є авто').
					h::{'a.fb.driver'}(
						h::icon('facebook').
						'Увійти через Facebook'
					).
					h::{'a.vk.driver'}(
						h::icon('vk').
						'Увійти через VK'
					)
				)
			)
		)
	);
} elseif ($User->get_data('driver') || $User->admin()) {
	$Page->content(
		h::{'section.home-page article'}(
			h::{'p.cs-center.home-page-list-map-switcher input[type=radio]'}([
				'value'		=> ['list', 'map'],
				'in'		=> ['Список', 'Карта'],
				'checked'	=> 'map'
			]).
			h::{'div.home-page-filter.uk-form'}([
				h::{'input[name=date]'}([
					'placeholder'	=> 'Дата'
				]).
				h::{'div.uk-button-dropdown[data-uk-dropdown=]'}(
					h::{'input[name=time]'}([
						'placeholder'	=> 'Час'
					]).
					h::{'div.uk-dropdown ul.uk-nav.uk-nav-dropdown li| a'}(
						'08:00 - 10:00',
						'10:00 - 12:00',
						'12:00 - 15:00',
						'15:00 - 17:00',
						'17:00 - 22:00',
						'22:00 - 08:00'
					)
				).
				h::{'button'}('Пошук').
				h::{'div#driver-map[level=0]'}()
			])
		)
	);
} else {
	$Index			= Index::instance();
	$Index->form	= true;
	$Index->buttons	= false;
	$Index->content(
		h::{'section.home-page article.home-page-add-goods'}(
			h::{'h2.cs-center'}('В мене є речі').
			h::{'input[name=name]'}([
				'placeholder'	=> 'Ваше ім’я',
				'value'			=> $User->username()
			]).
			h::{'input[name=phone]'}([
				'placeholder'	=> 'Ваш номер телефону'
			]).
			h::{'input[name=address]'}([
				'placeholder'	=> 'Ваша адреса'
			]).
			h::{'input[type=hidden][name=coordinates]'}([
				'placeholder'	=> 'Ваша адреса'
			]).
			h::{'div#user-map[level=0]'}().
			h::{'input[name=date]'}([
				'placeholder'	=> 'Дата'
			]).
			h::{'input[type=hidden][name=time]'}().
			h::{'div.uk-button-dropdown[data-uk-dropdown=]'}(
				h::button(
					h::icon('caret-down').
					'Зручний час'
				).
				h::{'div.uk-dropdown ul.uk-nav.uk-nav-dropdown li| a'}(
					'08:00 - 10:00',
					'10:00 - 12:00',
					'12:00 - 15:00',
					'15:00 - 17:00',
					'17:00 - 22:00',
					'22:00 - 08:00'
				)
			).
			h::{'textarea[name=comment][rows=4]'}([
				'placeholder'	=> 'Ваш коментар'
			]).
			h::{'p.cs-right button[type=submit]'}('Надіслати')
		)
	);
}