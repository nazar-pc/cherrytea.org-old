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
			cs\Page,
			cs\User;
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
} elseif ($User->get_data('driver')/* || $User->admin()*/) {
	$Page->content(
		h::{'section.home-page article'}(
		 h::{'h2.cs-center'}('В мене є машина').
			/*h::{'p.cs-center.home-page-list-map-switcher input[type=radio]'}([
				'value'		=> ['list', 'map'],
				'in'		=> ['Список', 'Карта'],
				'checked'	=> 'map'
			]).*/
			h::{'div.home-page-filter.uk-form'}([
				h::{'input[name=date]'}([
					'placeholder'	=> 'Дата'
				]).
				h::{'div.uk-button-dropdown[data-uk-dropdown={mode:\'click\'}]'}(
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
	$Index->action	= '';
	$Index->form	= true;
	$Index->buttons	= false;
	$Goods			= Goods::instance();
	$good			= $Goods->added_by_giver($User->id);
	if (!$good && isset($_POST['name'])) {
		$Goods->add(
			$User->id,
			$_POST['comment'],
			$_POST['phone'],
			$_POST['address'],
			$_POST['coordinates'],
			$_POST['date'],
			$_POST['time']
		);
	}
	if ($good) {
		$Index->content(
			h::{'section.home-page article.home-page-added-goods'}(
				h::{'h2.cs-cs-center'}('Дякуємо за розміщену інформацію про наявні речі!').
				h::{'p.cs-center'}('Вільний водій зв’яжеться з вами за першої нагоди.').
				h::p('Коли віддаватимете речі - спитайте про код, який має кожен водій. Цей код використовується за для безпеки та контролю чесності та надійності водіїв.').
				h::p('Код може бути текстовим - його необхідно ввести в поле нижче, або QR-код - його можно відсканувати за допомогою смартфону та перейти за посиланням, цей варіант зручний і швидкий.').
				h::p(
					h::{'input[name=confirmation_code]'}([
						'placeholder'	=> 'Код'
					]).
					h::{'button[type=submit]'}('Надіслати')
				)
			)
		);
		return;
	}
	$Index->content(
		h::{'section.home-page article.home-page-add-goods'}(
			h::{'h2.cs-center'}('В мене є речі').
			h::{'input[name=name][required]'}([
				'placeholder'	=> 'Ваше ім’я',
				'value'			=> $User->username()
			]).
			h::{'input[name=phone][required]'}([
				'placeholder'	=> 'Ваш номер телефону',
				'value'			=> $User->get_data('phone') ?: ''
			]).
			h::{'input[name=address][required]'}([
				'placeholder'	=> 'Ваша адреса',
				'value'			=> $User->get_data('address') ?: ''
			]).
			h::{'input[type=hidden][name=coordinates][required]'}([
				'value'			=> is_array($User->get_data('coordinates')) ? $User->get_data('coordinates') : '[50.4505, 30.523]'
			]).
			h::{'div#user-map[level=0]'}().
			h::{'input[name=date][required]'}([
				'placeholder'	=> 'Дата'
			]).
			h::{'div.uk-button-dropdown[data-uk-dropdown={mode:\'click\'}]'}(
				h::{'input[name=time][required]'}([
					'placeholder'	=> 'Зручний час'
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
			h::{'textarea[name=comment][rows=4][required]'}([
				'placeholder'	=> 'Ваш коментар'
			]).
			h::{'p.cs-right button[type=submit]'}('Надіслати')
		)
	);
}