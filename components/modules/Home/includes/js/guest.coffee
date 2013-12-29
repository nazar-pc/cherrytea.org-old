###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	$('.home-page-sign-in a').click ->
		$this		= $(this)
		provider	= if $this.hasClass('fb') then 'Facebook' else 'Vkontakte'
		$.ajax(
			url		: 'api/Home/i_am_driver'
			data	:
				driver	: if $this.hasClass('driver') then 1 else 0
			success	: ->
				location.href	= 'HybridAuth/' + provider
			error	: (xhr) ->
				if xhr.responseText
					alert(cs.json_decode(xhr.responseText).error_description)
				else
					alert(L.auth_connection_error)
		);
	guest_map	= $('#guest-map')
	if !guest_map.length
		return
	do (w = $(window).width()) ->
		guest_map
			.width(w)
			.css(
				marginLeft	: guest_map.parent().outerWidth() / 2 - w / 2
			)
	ymaps.ready ->
		map			= new ymaps.Map 'guest-map', {
			center		: [50.4505, 30.523]
			zoom		: 13
			controls	: ['geolocationControl', 'fullscreenControl', 'typeSelector', 'zoomControl']
		}
		map.behaviors.disable('scrollZoom');
		guest_map.get(0).close_balloon	= ->
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
		add_destination	= ->
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
						balloonLayout		: ymaps.templateLayoutFactory.createClass(
							"""<section class="home-page-map-balloon-container centers">
								<header><h1>Благодійний фонд Карітас-Київ</h1> <a class="uk-close" onclick="$('#guest-map').get(0).close_balloon()"></a></header>
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
						balloonLayout		: ymaps.templateLayoutFactory.createClass(
							"""<section class="home-page-map-balloon-container centers">
								<header><h1>Книжковий магазин Свічадо</h1> <a class="uk-close" onclick="$('#guest-map').get(0).close_balloon()"></a></header>
								<article>
									<address>вулиця Покровська, 6</address>
									<time>Будні: з 10:00 до 17:00</time>
								</article>
							</section>"""
						)
					}
				)
			)
		add_destination()
		find_goods	= ->
			$.ajax(
				url		: 'api/Home/find_goods'
				type	: 'get'
				success	: (result) ->
					map.geoObjects.removeAll()
					add_destination()
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
									{}
									{
										iconLayout			: 'default#image'
										iconImageHref		: '/components/modules/Home/includes/img/map-icons.png'
										iconImageSize		: [60, 58]
										iconImageOffset		: [-24, -58]
										iconImageClipRect	: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]]
									}
								)
							)
			)
		find_goods()