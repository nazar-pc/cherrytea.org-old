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
	container.find('input[name=date]').pickmeup(
		format		: 'd.m.Y'
		onChange	: (formated) ->
			container
				.find('[name=date]')
				.val(formated)
				.pickmeup('hide');
	)
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