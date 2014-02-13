###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	if !$('#map').length
		return
	container	= $('.home-page-filter')
	container.find('input[name=date]')
		.pickmeup(
			format	: 'd.m.Y'
			change	: (formated) ->
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
		clusterer	= new ymaps.Clusterer()
		map.geoObjects.add(clusterer)
		find_goods	= ->
			$.ajax(
				url		: 'api/Home/find_goods'
				data	:
					date		: container.find('input[name=date]').val()
					time		: container.find('[name=time]').val()
					reserved	: if $('.home-page-map-switcher.driver .uk-active input').val() == 'reserved_goods' then 1 else 0
				type	: 'get'
				success	: (result) ->
					if result && result.length
						lat			= [0, 0]
						lng			= [0, 0]
						placemarks	= []
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
							reservation	=
								if driver_id == parseInt(good.reserved_driver, 10) && good.reserved > (new Date).getTime() / 1000
									"""<button class="reserved uk-button" data-id="#{good.id}">Зарезервовано</button>"""
								else
									"""<button class="reservation uk-button" data-id="#{good.id}">Заберу за 24 години</button>"""
							admin		=
								if window.cs.is_admin
									"""<span class="uk-icon-trash delete-good" data-id="#{good.id}"></span>"""
								else
									''
							placemarks.push(
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
										iconImageShape		: map.icons_shape
										balloonLayout		: ymaps.templateLayoutFactory.createClass(
											"""<section class="home-page-map-balloon-container">
												<header><h1>#{good.username} <small>#{good.phone}</small></h1> #{admin}<a class="uk-close" onclick="map.balloon.close()"></a></header>
												<article>
													<address>#{good.address}</address>
													<time>#{good.date} (#{good.time})</time>
													<p>#{good.comment}</p>
												</article>
												<footer>#{reservation}</footer>
											</section>"""
										)
									}
								)
							)
						clusterer.removeAll()
						clusterer.add(placemarks)
			)
		container
			.on(
				'click'
				'.reservation'
				->
					reservation	= $(@)
					$.ajax(
						url		: 'api/Home/reservation'
						type	: 'post'
						data	:
							id	: reservation.data('id')
						success	: ->
							reservation
								.html('Зарезервовано')
								.removeClass('reservation')
								.addClass('reserved')
							alert 'Зарезервовано! Дякуємо та чекаємо вашого приїзду!'
							find_goods()
						error	: (xhr) ->
							if xhr.responseText
								alert(cs.json_decode(xhr.responseText).error_description)
							else
								alert(L.auth_connection_error)
					)
			)
			.on(
				'click'
				'.reserved'
				->
					reserved	= $(@)
					$.ajax(
						url		: 'api/Home/reservation'
						type	: 'delete'
						data	:
							id	: reserved.data('id')
						success	: ->
							reserved
								.html('Заберу за 24 години')
								.removeClass('reserved')
								.addClass('reservation')
							alert 'Резерв скасовано! Дякуємо що попередили!'
							find_goods()
						error	: (xhr) ->
							if xhr.responseText
								alert(cs.json_decode(xhr.responseText).error_description)
							else
								alert(L.auth_connection_error)
					)
			)
		find_goods()
		search_timeout	= 0
		container.on(
			'keyup change'
			'[name=date], [name=time], .home-page-map-switcher.driver input'
			->
				clearTimeout(search_timeout)
				search_timeout = setTimeout(find_goods, 300)
		)
		$('.home-page-map-switcher.driver').on(
			'keyup change'
			'input'
			->
				clearTimeout(search_timeout)
				search_timeout = setTimeout(find_goods, 300)
		)
		driver_map.on(
			'click',
			'.delete-good'
			->
				if !window.cs.is_admin
					return
				if !confirm('Точно видалити?')
					return
				$.ajax(
					url		: 'api/Home/delete_good'
					data	:
						id	: $(this).data('id')
					success	: ->
						find_goods()
				)
		)
