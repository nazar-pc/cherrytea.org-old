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
	$('.home-page-sign-in a').click ->
		location.href	= 'HybridAuth/' + (if $(@).hasClass('fb') then 'Facebook' else 'Vkontakte')
