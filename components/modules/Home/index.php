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
			h::h2('Вхід на сайт').
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
} elseif (Drivers::instance()->active($User->id) || $User->admin()) {
	$Page->content(
		h::{'section.home-page article'}(
			h::h2('В мене є автомобіль').
			h::{'div.home-page-filter.uk-form'}([
				h::{'input[name=date]'}([
					'placeholder'	=> 'Будь-яка дата'
				]).
				h::{'div.uk-button-dropdown[data-uk-dropdown={mode:\'click\'}]'}(
					h::{'input[name=time]'}([
						'placeholder'	=> 'Будь-який час'
					]).
					h::{'div.uk-dropdown ul.uk-nav.uk-nav-dropdown li| a'}(
						'08:00 - 10:00',
						'10:00 - 12:00',
						'12:00 - 15:00',
						'15:00 - 17:00',
						'17:00 - 22:00'
					)
				).
				h::{'div#driver-map[level=0]'}()
			]).
			h::{'p.cs-center'}('Не забувайте під час збору речей брати з собою код зі сторінки профілю, він є обов’язковим для водіїв.')
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
	if ($good && isset($_POST['confirmation_code'])) {
		if ($driver = Drivers::instance()->get_by_code($_POST['confirmation_code'])) {
			$Goods->set_driver($good['id'], $driver['id']);
			$good	= false;
		} else {
			$Page->warning('Код неправильний, спробуйте ще раз');
		}
	}
	if ($good) {
		$Index->content(
			h::{'section.home-page article.home-page-added-goods'}(
				h::h2('Дякуємо за розміщену інформацію про наявні речі!').
				h::{'p.cs-center'}('Вільний водій зв’яжеться з вами за першої нагоди.').
				h::p('Коли віддаватимете речі - спитайте про код, який має кожен водій. Цей код використовується за для контролю чесності та надійності водіїв.').
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
			h::h2('В мене є речі').
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
			h::{'div#giver-map[level=0]'}().
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
					'17:00 - 22:00'
				)
			).
			h::{'textarea[name=comment][rows=4][required]'}([
				'placeholder'	=> 'Ваш коментар'
			]).
			h::{'p.cs-right button[type=submit]'}('Надіслати')
		)
	);
}