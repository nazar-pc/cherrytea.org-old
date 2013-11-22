###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	driver_map	= $('#driver-map')
	if !driver_map.length
		return
	container	= $('.home-page-filter')
	container.find('input[name=date]')
		.pickmeup(
			format		: 'd.m.Y'
			onChange	: (formated) ->
				container
					.find('[name=date]')
					.val(formated)
					.pickmeup('hide')
					.change();
		)
	container.find('[name=time]')
		.next()
			.find('a')
				.click ->
					container
						.find('[name=time]')
							.val($(@).text())
							.change()
	ymaps.ready ->
		map			= new ymaps.Map 'driver-map', {
			center		: [50.4505, 30.523]
			zoom		: 13
			controls	: ['geolocationControl', 'fullscreenControl', 'typeSelector', 'zoomControl']
		}
		driver_map.get(0).close_balloon	= ->
			map.balloon.close()
		if navigator.geolocation
			navigator.geolocation.getCurrentPosition(
				(position) ->
					map.panTo([position.coords.latitude, position.coords.longitude])
				->
				{
					enableHighAccuracy	: true
					timeout				: 30 * 60 * 1000	#Wait for 30 minutes max
				}
			)
		find_givers	= ->
			$.ajax(
				url		: 'api/Home/find_givers'
				data	:
					date	: container.find('input[name=date]').val()
					time	: container.find('[name=time]').val()
				type	: 'get'
				success	: (result) ->
					map.geoObjects.removeAll()
					if result && result.length
						lat	= [0, 0]
						lng	= [0, 0]
						for good in result
							lat	= [
								Math.min(lat[0], good.lat),
								Math.max(lat[0], good.lat)
							]
							lng	= [
								Math.min(lng[0], good.lng),
								Math.max(lng[0], good.lng)
							]
							icon_number	= Math.round(Math.random() * 11)
							map.geoObjects.add(
								new ymaps.Placemark(
									[
										good.lat
										good.lng
									]
									{
										hintContent	: good.username + ' ' + good.phone
									}
									{
										iconLayout			: 'default#image'
										iconImageHref		: '/components/modules/Home/includes/img/map-icons.png'
										iconImageSize		: [60, 58]
										iconImageOffset		: [-24, -58]
										iconImageClipRect	: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]]
										balloonLayout		: ymaps.templateLayoutFactory.createClass(
											"""<section class="home-page-map-balloon-container">
												<header><h1>#{good.username} <small>#{good.phone}</small></h1> <a class="uk-close" onclick="$('#driver-map').get(0).close_balloon()"></a></header>
												<article>
													<address>#{good.address}</address>
													<time>#{good.date} (#{good.time})</time>
													<p>#{good.comment}</p>
												</article>
												<footer><button class="reservation uk-button" data-id="#{good.id}">Заберу за 24 години</button></footer>
											</section>"""
										)
									}
								)
							)
			)
			container
				.on(
					'click'
					'.reservation'
					->
						reservation	= $(@)
						$.ajax(
							url		: 'api/Home/reservation'
							data	:
								id	: reservation.data('id')
							success	: ->
								reservation
									.html('Зарезервовано')
									.prop('disabled', true)
								alert 'Прийнято! Дякуємо та чекаємо вашого приїзду!'
								find_givers()
							error	: (xhr) ->
								if xhr.responseText
									alert(cs.json_decode(xhr.responseText).error_description)
								else
									alert(L.auth_connection_error)
						)
				)
		find_givers()
		search_timeout	= 0
		container.on(
			'keyup change'
			'[name=date], [name=time]'
			->
				clearTimeout(search_timeout)
				search_timeout = setTimeout(find_givers, 300)
		)