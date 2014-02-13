###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
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
