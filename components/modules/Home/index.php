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
				).
				h::div('або').
				h::{'a.gp'}(
					h::{'span img'}([
						'src'	=> '/components/modules/Home/includes/img/google-play.svg'
					]).
					'Завантажити Android додаток',
					[
						'href'		=> 'https://play.google.com/store/apps/details?id=net.givemealift',
						'target'	=> '_blank'
					]
				)
			).
			h::{'div.cs-home-page-legend'}(
				h::{'span.finished'}(' - забрані речі').
				h::{'span.red'}('- речі що необхідно забрати').
				h::{'span.blue'}(' - пункти прийому')
			).
			h::{'div#map[level=0]'}()
		)
	);
} else {
	$Volunteers	= Volunteers::instance();
	$volunteer	= $Volunteers->get($User->id);
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
			h::{'div.cs-home-page-map-goods-switcher input[type=radio]'}([
				'value'		=> [
					'all',
					'my'
				],
				'in'		=> [
					'Відвезти речі',
					'У мене є речі'
				],
				'checked'	=> 'all'
			]).
			h::{'div.cs-home-page-filter.uk-form'}(
				h::{'div.uk-button-dropdown.cs-home-page-filter-reservation[data-uk-dropdown=][data-value=0]'}(
					h::button('Потрібно відвезти').
					h::{'div.uk-dropdown ul.uk-nav.uk-nav-dropdown li'}(
						h::{'a[data-value=0]'}('Потрібно відвезти'),
						h::{'a[data-value=1]'}('Зарезервовані')
					)
				).
				h::icon('calendar').
				h::{'input[name=date]'}([
					'placeholder'	=> 'Будь-яка дата'
				]).
				h::icon('clock-o').
				h::{'div.uk-button-dropdown[data-uk-dropdown={mode:\'click\'}]'}(
					h::{'input[name=time]'}([
						'placeholder'	=> 'Будь-який час'
					]).
					h::{'div.uk-dropdown.uk-dropdown-small ul.uk-nav.uk-nav-dropdown li| a'}(
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
			).
			h::{'div#map[level=0]'}().
			h::{'div.cs-home-page-my-goods[style=display:none]'}(
				h::{'div.cs-home-page-add-goods-button.cs-center.cs-pointer.uk-margin-bottom'}(
					h::icon('plus').h::a('Додати').h::icon('caret-down')
				).
				h::{'form.cs-home-page-add-goods'}(
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
				h::{'div.cs-home-page-my-goods-list'}()
			).
			($driver ? h::{'h2.cs-center'}('Не забувайте під час збору речей брати з собою код зі сторінки профілю, він є обов’язковим для водіїв.') : '')
		)
	);
}
