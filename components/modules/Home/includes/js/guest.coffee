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