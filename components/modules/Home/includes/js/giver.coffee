###
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	giver_map	= $('#add-good-map')
	if giver_map.length
		container	= $('.cs-home-page-add-goods')
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
				if !container.find('[name=address]').val()
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
		$('.cs-home-i-have-a-car').click ->
			$.ajax(
				url		: 'api/Home/i_have_a_car'
				type	: 'put'
				success	: ->
					alert 'Дякуємо!) Після перевірки вашого облікового запису вам буде надано персональний код водія та доступ до контактів волонтерів з речами'
					location.reload()
			)
		container.submit ->
			$.ajax(
				url		: 'api/Home/goods'
				type	: 'post'
				data	:
					name		: container.find('[name=name]').val()
					phone		: container.find('[name=phone]').val()
					address		: container.find('[name=address]').val()
					coordinates	: container.find('[name=coordinates]').val()
					date		: container.find('[name=date]').val()
					time		: container.find('[name=time]').val()
					comment		: container.find('[name=comment]').val()
				success	: ->
					$("""
						<div>
							<div class="uk-form" style="width: 700px;margin-left: -350px;">
								<h2 class="cs-center">Дякуємо за розміщену інформацію про наявні речі!</h2>
								<p class="cs-center">Вільний водій зв’яжеться з вами за першої нагоди.</p>
								<p>Коли віддаватимете речі - спитайте про код, який має кожен водій. Цей код використовується задля контролю чесності та надійності водіїв.</p>
								<p>В розділі "Мої речі" нижче ви можете підтвердити передачу речей водію, ввівши його код.</p>
							</div>
						</div>
					""")
						.appendTo('body')
						.cs().modal('show')
						.on 'uk.modal.hide', ->
							$(this).remove()
			)
			return false
		$('.cs-home-page-my-goods')
			.on(
				'click'
				'.cs-home-page-delete-good'
				->
					#
			)
			.on(
				'click'
				'.cs-home-page-confirm-good'
				->
					id		= $(@).data('id')
					modal	= $.cs.simple_modal(
						"""
							<p>Введіть код, який ви отримали від водія, щоб ми знали, хто відвозить ваші речі</p>
							<input placeholder="Код водія" autofocus>
							<button class="uk-button">Готово</button>
						"""
						true
						500
					)
					modal.find('button')
							.click ->
								$.ajax(
									url		: "api/Home/goods/#{id}/confirm"
									type	: 'post'
									data	:
										confirmation_code	: modal.find('input').val()
									success	: ->
										alert 'Дякуємо, що творите добрі справи!'
										location.reload()
								)
			)
