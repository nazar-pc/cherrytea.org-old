###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	if !$('#user-map').length
		return;
	container	= $('.home-page-add-goods')
	container
		.find('[name=date]')
			.pickmeup(
				format		: 'd.m.Y'
				mode		: 'range'
				onChange	: (formated) ->
					container
						.find('[name=date]')
						.val(formated.join(' — '));
			)
	container
		.find('[name=time]')
			.next()
				.find('a')
					.click ->
						container
							.find('[name=time]')
								.val($(this).text())
	coordinates	= container.find('[name=coordinates]')
	ymaps.ready ->
		map			= new ymaps.Map 'user-map', {
			center		: [50.4505, 30.523],
			zoom		: 13,
			controls	: ['zoomControl']
		}
		icon_number	= Math.round(Math.random() * 11)
		me			= new ymaps.Placemark [50.45056507697532,30.523316500663444], {}, {
			draggable			: true
			iconLayout			: 'default#image'
			iconImageHref		: '/components/modules/Home/includes/img/map-icons.png'
			iconImageSize		: [60, 58]
			iconImageOffset		: [-24, -58]
			iconImageClipRect	: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]]
		}
		map.geoObjects.add(me)
		me.events.add(
			'geometrychange',
			(e) ->
				coords	= cs.json_encode(e.get('originalEvent').originalEvent.newPosition)
				coordinates.val(coords)
		)
		if navigator.geolocation
			navigator.geolocation.getCurrentPosition(
				(position) ->
					map.panTo([position.coords.latitude, position.coords.longitude])
					me.geometry.setCoordinates(map.getCenter())
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