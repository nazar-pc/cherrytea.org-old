###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	if !$('#map').length
		return
	ymaps.ready ->
		# Map resizing on initialization and window resize
		do ->
			map_resize	= ->
				w = $(window).width()
				$('#map')
					.css(
						width		: w
						marginLeft	: 500 - w / 2
					)
			map_resize()
			$(window).resize(map_resize)
		window.map				= new ymaps.Map 'map', {
			center				: [50.4505, 30.523]
			zoom				: 13
			controls			: ['geolocationControl', 'fullscreenControl', 'typeSelector', 'zoomControl']
		}
		map.behaviors.disable('scrollZoom')
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
		map.icons_shape	= new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([
			[
				[17-24, 0-58],
				[30-24, 0-58],
				[41-24, 7-58],
				[47-24, 18-58],
				[47-24, 29-58],
				[42-24, 38-58],
				[24-24, 57-58],
				[8-24, 42-58],
				[0-24, 30-58],
				[0-24, 18-58],
				[6-24, 7-58],
				[17-24, 0-58]
			]
		]))
		map.geoObjects.add(
			new ymaps.Placemark(
				[50.487124, 30.596273]
				{
					hintContent	: 'Благодійний фонд Карітас-Київ'
				}
				{
					iconLayout			: 'default#image'
					iconImageHref		: '/components/modules/Home/includes/img/destination.png'
					iconImageSize		: [60, 58]
					iconImageOffset		: [-24, -58]
					iconImageShape		: map.icons_shape
					balloonLayout		: ymaps.templateLayoutFactory.createClass(
						"""<section class="home-page-map-balloon-container centers">
							<header><h1>Благодійний фонд Карітас-Київ</h1> <a class="uk-close" onclick="map.balloon.close()"></a></header>
							<article>
								<address>вулиця Івана Микитенка, 7б</address>
								<time>Будні: з 9:00 до 18:00<br>Вихідні: з 10:00 до 15:00</time>
							</article>
						</section>"""
					)
				}
			)
		)
		map.geoObjects.add(
			new ymaps.Placemark(
				[50.461404, 30.519216]
				{
					hintContent	: 'Книжковий магазин Свічадо'
				}
				{
					iconLayout			: 'default#image'
					iconImageHref		: '/components/modules/Home/includes/img/destination.png'
					iconImageSize		: [60, 58]
					iconImageOffset		: [-24, -58]
					iconImageShape		: map.icons_shape
					balloonLayout		: ymaps.templateLayoutFactory.createClass(
						"""<section class="home-page-map-balloon-container centers">
							<header><h1>Книжковий магазин Свічадо</h1> <a class="uk-close" onclick="map.balloon.close()"></a></header>
							<article>
								<address>вулиця Покровська, 6</address>
								<time>Будні: з 10:00 до 17:00</time>
							</article>
						</section>"""
					)
				}
			)
		)
		filter	= $('.cs-home-page-filter')
		filter.find('input[name=date]')
			.pickmeup(
				format	: 'd.m.Y'
				change	: (formated) ->
					filter
						.find('[name=date]')
						.val(formated)
						.pickmeup('hide')
						.change();
			)
		filter.find('[name=time]')
			.next()
				.find('a')
					.click ->
						filter
							.find('[name=time]')
								.val($(@).text())
								.change()
		clusterer	= new ymaps.Clusterer()
		clusterer.createCluster	= (center, geoObjects) ->
			cluster	= ymaps.Clusterer.prototype.createCluster.call(this, center, geoObjects)
			cluster.options.set(
				icons	: [
					{
						href	: '/components/modules/Home/includes/img/cluster-46.png'
						size	: [46, 46]
						offset	: [-23, -23]
					}
					{
						href	: '/components/modules/Home/includes/img/cluster-58.png'
						size	: [58, 58]
						offset	: [-27, -27]
					}
				]
			)
			cluster
		map.geoObjects.add(clusterer)
		find_goods	= ->
			goods	= $('.cs-home-page-map-goods-switcher.driver .uk-active input').val()
			if goods == 'my'
				$('#map, .cs-home-page-filter').hide()
				$('.cs-home-page-my-goods').html('<p class="uk-margin cs-center"><i class="uk-icon-spin uk-icon-spinner"></i></p>').show()
			else
				$('#map, .cs-home-page-filter').show()
				$('.cs-home-page-my-goods').hide().html('')
			$.ajax(
				url		: 'api/Home/find_goods'
				data	:
					date		: filter.find('input[name=date]').val()
					time		: filter.find('[name=time]').val()
					goods		: goods
				type	: 'get'
				success	: (result) ->
					if result && result.length
						if goods != 'my'
							placemarks	= []
							for good in result
								icon_number	= Math.round(Math.random() * 11)
								if window.driver
									reservation	=
										if window.driver == parseInt(good.reserved_driver, 10) && good.reserved > (new Date).getTime() / 1000
											"""<button class="reserved uk-button" data-id="#{good.id}">Зарезервовано</button>"""
										else
											"""<button class="reservation uk-button" data-id="#{good.id}">Заберу за 24 години</button>"""
								else
									reservation	= ''
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
											hintContent	: if window.driver || good.giver == window.volunteer then good.username + ' ' + good.phone else undefined
										}
										{
											iconLayout			: 'default#image'
											iconImageHref		: '/components/modules/Home/includes/img/map-icons.png'
											iconImageSize		: [60, 58]
											iconImageOffset		: [-24, -58]
											iconImageClipRect	: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]]
											iconImageShape		: map.icons_shape
											balloonLayout		: if window.driver || good.giver == window.volunteer then ymaps.templateLayoutFactory.createClass(
												"""<section class="home-page-map-balloon-container">
													<header><h1>#{good.username} <small>#{good.phone}</small></h1> #{admin}<a class="uk-close" onclick="map.balloon.close()"></a></header>
													<article>
														<address>#{good.address}</address>
														<time>#{good.date} (#{good.time})</time>
														<p>#{good.comment}</p>
													</article>
													<footer>#{reservation}</footer>
												</section>"""
											) else undefined
										}
									)
								)
							clusterer.removeAll()
							clusterer.add(placemarks)
						else
							content	= ''
							for good in result
								if good.success == '-1' && good.reserved > (new Date).getTime() / 1000
									state			= 'Зарезервовано водієм'
									icon_h_offset	= 97
								else
									if good.success != '-1'
										state			= 'Доставлено'
										icon_h_offset	= 2 * 97
									else
										state			= 'Очікує'
										icon_h_offset	= 0
								icon_v_offset	= Math.round(Math.random() * 6) * 97
								content			+= """<aside>
									<div class="icon" style="background-position: -#{icon_h_offset}px -#{icon_v_offset}px"></div>
									<h2>#{state}</h2>
									<span>#{good.phone}</span>
									<address>#{good.address}</address>
									<time>#{good.date} (#{good.time})</time>
									<p>#{good.comment}</p>
									<p>
										<button class="cs-home-page-delete-good uk-button"><i class="uk-icon-times"></i></button>
										<button class="cs-home-page-confirm-good uk-button"><i class="uk-icon-ok"></i> Водій забрав речі</button>
									</p>
								</aside>"""
							$('.cs-home-page-my-goods').html(content+content)
						return
			)
		find_goods()
		filter
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
		search_timeout	= 0
		filter.on(
			'keyup change'
			'[name=date], [name=time], .cs-home-page-map-goods-switcher.driver input'
			->
				clearTimeout(search_timeout)
				search_timeout = setTimeout(find_goods, 300)
		)
		$('.cs-home-page-map-goods-switcher.driver').on(
			'keyup change'
			'input'
			->
				clearTimeout(search_timeout)
				search_timeout = setTimeout(find_goods, 300)
		)
		$('#map').on(
			'click'
			'.delete-good'
			->
				if !window.cs.is_admin
					return
				if !confirm('Точно видалити?')
					return
				$.ajax(
					url		: 'api/Home/goods/' + $(this).data('id')
					type	: 'delete'
					success	: ->
						find_goods()
				)
		)
