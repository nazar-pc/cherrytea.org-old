###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	giver_map	= $('#add-good-map')
	if giver_map.length
		container	= $('.home-page-add-goods')
		container
			.find('[name=date]')
				.pickmeup(
					format	: 'd.m.Y'
					mode	: 'range'
					change	: (formated) ->
						container
							.find('[name=date]')
							.val(formated.join(' - '));
				)
		container
			.find('[name=time]')
				.next()
					.find('a')
						.click ->
							container
								.find('[name=time]')
									.val($(@).text())
		coordinates	= container.find('[name=coordinates]')
		ymaps.ready ->
			map			= new ymaps.Map 'add-good-map', {
				center		: cs.json_decode(coordinates.val())
				zoom		: 13
				controls	: ['zoomControl']
			}
			icon_number	= Math.round(Math.random() * 11)
			me			= new ymaps.Placemark cs.json_decode(coordinates.val()), {}, {
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
					coords	= cs.json_encode(e.get('originalEvent').originalEvent.newCoordinates)
					coordinates.val(coords)
			)
			if navigator.geolocation
				navigator.geolocation.getCurrentPosition(
					(position) ->
						coords	= [position.coords.latitude, position.coords.longitude]
						map.panTo(coords)
						me.geometry.setCoordinates(coords)
					->
					{
						enableHighAccuracy	: true,
						timeout				: 120 * 1000	#Wait for 2 minutes max
					}
				)
				address_timeout	= 0
				container
					.find('[name=address]')
						.on(
							'keyup change',
							->
								if ($(@).val().length < 4)
									return
								clearTimeout(address_timeout)
								address_timeout	= setTimeout (->
									ymaps.geocode(container.find('[name=address]').val()).then(
										(res) ->
											coords	= res.geoObjects.get(0).geometry.getCoordinates()
											map.panTo(
												coords
												fly				: true
												checkZoomRange	: true
											)
											me.geometry.setCoordinates(coords)
											coordinates.val(cs.json_encode(coords))
									)
								), 300
						)
						.keyup()
