###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	$('.home-page-sign-in a').click ->
		location.href	= 'HybridAuth/' + (if $(@).hasClass('fb') then 'Facebook' else 'Vkontakte')
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
