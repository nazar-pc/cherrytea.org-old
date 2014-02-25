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
			cs\Page,
			cs\User;
$Page		= Page::instance();
$User		= User::instance();
if ($User->guest()) {
	$Page->content(
		h::{'section.home-page article'}(
			h::{'h2.cs-center'}('Вхід на сайт').
			h::{'div.home-page-sign-in'}(
				h::{'a.fb'}(
					h::icon('facebook').
					'Увійти через Facebook'
				).
				h::{'a.vk'}(
					h::icon('vk').
					'Увійти через VK'
				)
			).
			h::{'div#map'}()
		)
	);
} else {
	$Volunteers	= Volunteers::instance();
	$volunteer	= $Volunteers->get($User->id);
	if (!$volunteer) {
		$Volunteers->add($User->id);
		$volunteer	= $Volunteers->get($User->id);
	}
	$driver		= $Volunteers->is_driver($volunteer['id']) ? $volunteer['id'] : 0;
	$Page->js("var driver = $driver, volunteer = '$volunteer[id]';", 'code');
	$Page->content(
		h::{'section.home-page article'}(
			h::header(
				h::{'div.avatar'}([
					'style'	=> 'background:url('.$User->avatar(140).')'
				]).
				h::{'span.cs-header-sign-out-process'}('Вихід').
				h::h2($User->username()).
				(
					$driver ? h::p('Персональний код водія: '.h::b($volunteer['code'])) : (
						$volunteer['driver'] == 'requested' ? 'Запит на доступ в ролі водія надіслано, очікується підтвердження' : (
							$volunteer['driver'] != 'no' ? h::{'button.cs-home-i-have-a-car'}('В мене є авто, готовий допомогти') :
								'Запит на доступ в ролі водія відхилено, але ви все ще можете віддати непотрібні речі'
						)
					)
				).
				h::p(
					h::icon('heart').
					h::b($volunteer['reputation'])
				)
			).
			h::{'form.cs-home-page-add-goods'}(
				h::{'h2.cs-center'}('В мене є речі').
				h::{'input[name=name][required]'}([
					'placeholder'	=> 'Ваше ім’я',
					'value'			=> isset($_POST['name']) ? $_POST['name'] : ($User->username())
				]).
				h::{'input[name=phone][required]'}([
					'placeholder'	=> 'Ваш номер телефону',
					'value'			=> isset($_POST['phone']) ? $_POST['phone'] : ($User->get_data('phone') ?: '')
				]).
				h::{'input[name=address][required]'}([
					'placeholder'	=> 'Ваша адреса',
					'value'			=> isset($_POST['address']) ? $_POST['address'] : ($User->get_data('address') ?: '')
				]).
				h::p('Будь ласка, перевірте, чи ваша адреса відповідає місцю на мапі? Якщо ні, пересуньте позначку в правильне місце.').
				h::{'input[type=hidden][name=coordinates][required]'}([
					'value'			=> isset($_POST['coordinates']) ? $_POST['coordinates'] : (is_array($User->get_data('coordinates')) ? $User->get_data('coordinates') : '[50.4505, 30.523]')
				]).
				h::{'div#add-good-map[level=0]'}().
				h::label(
					h::icon('calendar').
					h::{'input[name=date][required]'}([
						'placeholder'	=> 'Дата (від і до)',
						'value'			=> isset($_POST['date']) ? $_POST['date'] : ''
					])
				).
				h::{'div.uk-button-dropdown[data-uk-dropdown={mode:\'click\'}]'}(
					h::icon('time').
					h::{'input[name=time][required]'}([
						'placeholder'	=> 'Зручний час (від і до)',
						'value'			=> isset($_POST['time']) ? $_POST['time'] : ''
					]).
					h::{'div.uk-dropdown ul.uk-nav.uk-nav-dropdown li| a'}(
						'08:00 - 10:00',
						'10:00 - 12:00',
						'12:00 - 15:00',
						'15:00 - 17:00',
						'17:00 - 22:00'
					)
				).
				h::{'textarea[name=comment][rows=4][required]'}(
					isset($_POST['comment']) ? $_POST['comment'] : '',
					[
						'placeholder'	=> 'Ваш коментар'
					]
				).
				h::{'p.cs-right button[type=submit]'}('Надіслати')
			).
			h::{'div.cs-home-page-map-goods-switcher.driver input[type=radio]'}([
				'value'		=> [
					'all',
					$driver ? 'reserved' : false,
					'my'
				],
				'in'		=> [
					'Всі речі',
					$driver ? 'Зарезервовані' : false,
					'Мої речі'
				],
				'checked'	=> 'all'
			]).
			h::{'div.cs-home-page-filter.uk-form'}([
				h::icon('calendar').
				h::{'input[name=date]'}([
					'placeholder'	=> 'Будь-яка дата'
				]).
				h::icon('time').
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
				h::{'div.cs-home-page-legend'}(
					h::{'span.red'}('Забрати речі').
					h::{'span.blue'}('Пункти прийому')
				)
			]).
			h::{'div#map[level=0]'}().
			h::{'div.cs-home-page-my-goods'}().
			($driver ? h::{'p.cs-center'}('Не забувайте під час збору речей брати з собою код зі сторінки профілю, він є обов’язковим для водіїв.') : '')
		)
	);
} /*elseif ($driver && $driver['active'] == '0') {
	$Page->warning('Ваш аккаунт водія заблоковано адміністратором');
} elseif ($driver && $driver['active'] == '-1') {
	$Page->success('Ваш аккаунт водія потребує активації адміністратором. Найближчим часом з вами зв’яжуться в соціальній мережі');
} else {
	$Index			= Index::instance();
	$Index->action	= '';
	$Index->form	= true;
	$Index->buttons	= false;
	$Goods			= Goods::instance();
	$good			= $Goods->added_by($User->id);
	if (!$good && isset($_POST['name'])) {
		if ($_POST['comment'] && $_POST['name'] && $_POST['phone'] && $_POST['address'] && $_POST['coordinates'] && $_POST['date'] && $_POST['time']) {
			$Goods->add(
				$User->id,
				$_POST['comment'],
				$_POST['name'],
				$_POST['phone'],
				$_POST['address'],
				$_POST['coordinates'],
				$_POST['date'],
				$_POST['time']
			);
			$good	= $Goods->added_by($User->id);
		} else {
			$Page->warning('Всі поля обов’язкові для заповнення');
		}
	}
	if ($good && isset($_POST['confirmation_code'])) {
		if ($driver = Volunteers::instance()->get_driver_by_code($_POST['confirmation_code'])) {
			$Goods->set_driver($good['id'], $driver['id']);
			$good	= false;
		} else {
			$Page->warning('Код неправильний, спробуйте ще раз');
		}
	}
	$header	= h::header(
		h::{'div.avatar'}([
			'style'	=> 'background:url('.$User->avatar(140).')'
		]).
		h::{'span.cs-header-sign-out-process'}('Вихід').
		h::h2($User->username()).
		h::p(
			h::icon('heart').
			h::b(Volunteers::instance()->get($User->id)['reputation'] ?: 0)
		)
	);
	if ($good) {
		$Index->content(
			h::{'section.home-page article.home-page-added-goods'}(
				$header.
				h::{'h2.cs-center'}('Дякуємо за розміщену інформацію про наявні речі!').
				h::{'p.cs-center'}('Вільний водій зв’яжеться з вами за першої нагоди.').
				h::p('Коли віддаватимете речі - спитайте про код, який має кожен водій. Цей код використовується задля контролю чесності та надійності водіїв.').
				h::p('Отриманий код необхідно ввести в поле нижче').
				//h::p('Код може бути текстовим - його необхідно ввести в поле нижче, або QR-код - його можно відсканувати за допомогою смартфону та перейти за посиланням, цей варіант зручний і швидкий.').
				h::p(
					h::{'input[name=confirmation_code]'}([
						'placeholder'	=> 'Код'
					]).
					h::{'button[type=submit]'}('Надіслати')
				).
				h::{'div.cs-home-page-map-goods-switcher.giver input[type=radio]'}([
					'value'		=> ['map', 'my_goods'],
					'in'		=> ['Карта', 'Мої речі'],
					'checked'	=> 'map'
				]).
				h::{'div#map'}()
			)
		);
		return;
	}
	$Index->content(
		h::{'section.home-page article.home-page-add-goods'}(
			$header.
			h::{'h2.cs-center'}('В мене є речі').
			h::{'input[name=name][required]'}([
				'placeholder'	=> 'Ваше ім’я',
				'value'			=> isset($_POST['name']) ? $_POST['name'] : ($User->username())
			]).
			h::{'input[name=phone][required]'}([
				'placeholder'	=> 'Ваш номер телефону',
				'value'			=> isset($_POST['phone']) ? $_POST['phone'] : ($User->get_data('phone') ?: '')
			]).
			h::{'input[name=address][required]'}([
				'placeholder'	=> 'Ваша адреса',
				'value'			=> isset($_POST['address']) ? $_POST['address'] : ($User->get_data('address') ?: '')
			]).
			h::{'input[type=hidden][name=coordinates][required]'}([
				'value'			=> isset($_POST['coordinates']) ? $_POST['coordinates'] : (is_array($User->get_data('coordinates')) ? $User->get_data('coordinates') : '[50.4505, 30.523]')
			]).
			h::{'div#map[level=0]'}().
			h::label(
				h::icon('calendar').
				h::{'input[name=date][required]'}([
					'placeholder'	=> 'Дата (від і до)',
					'value'			=> isset($_POST['date']) ? $_POST['date'] : ''
				])
			).
			h::{'div.uk-button-dropdown[data-uk-dropdown={mode:\'click\'}]'}(
				h::icon('time').
				h::{'input[name=time][required]'}([
					'placeholder'	=> 'Зручний час (від і до)',
					'value'			=> isset($_POST['time']) ? $_POST['time'] : ''
				]).
				h::{'div.uk-dropdown ul.uk-nav.uk-nav-dropdown li| a'}(
					'08:00 - 10:00',
					'10:00 - 12:00',
					'12:00 - 15:00',
					'15:00 - 17:00',
					'17:00 - 22:00'
				)
			).
			h::{'textarea[name=comment][rows=4][required]'}(
				isset($_POST['comment']) ? $_POST['comment'] : '',
				[
					'placeholder'	=> 'Ваш коментар'
				]
			).
			h::{'p.cs-right button[type=submit]'}('Надіслати')
		)
	);
}*/
