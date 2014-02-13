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
	$('.home-page-sign-in a').click ->
		location.href	= 'HybridAuth/' + (if $(@).hasClass('fb') then 'Facebook' else 'Vkontakte')
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
							placemarks.push(
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
						clusterer.removeAll()
						clusterer.add(placemarks)
			)
		find_goods()
