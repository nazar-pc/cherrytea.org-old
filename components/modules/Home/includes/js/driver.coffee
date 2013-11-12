###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	if !$('#driver-map').length
		return;
	container	= $('.home-page-filter')
	container.find('input[name=date]').Zebra_DatePicker(
		show_icon			: false
		direction			: true
		format				: 'd.m.Y'
		days				: ['Нд', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
		months				: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень']
		header_navigation	: ['<span class="uk-icon-chevron-left"></span>', '<span class="uk-icon-chevron-right"></span>']
		offset				: [-244, 300]
		view				: 'days'
		show_clear_date		: false
		show_select_today	: false
	);
	ymaps.ready ->
		map			= new ymaps.Map 'driver-map', {
			center		: [50.4505, 30.523],
			zoom		: 13,
			controls	: ['geolocationControl', 'fullscreenControl', 'typeSelector', 'zoomControl']
		}
		if navigator.geolocation
			navigator.geolocation.getCurrentPosition(
				(position) ->
					map.panTo([position.coords.latitude, position.coords.longitude])
				->
				{
					enableHighAccuracy	: true,
					timeout				: 120 * 1000	#Wait for 2 minutes max
				}
			)
		###placemark	= new ymaps.Placemark(myMap.getCenter(), {
			balloonContentBody: [
				'<address>',
				'<strong>Офис Яндекса в Москве</strong>',
				'<br/>',
				'Адрес: 119021, Москва, ул. Льва Толстого, 16',
				'<br/>',
				'Подробнее: <a href="http://company.yandex.ru/">http://company.yandex.ru/<a>',
				'</address>'
			].join('')
		}, {
			preset: 'islands#redDotIcon'
		})
		map.geoObjects.add(myPlacemark)
		###