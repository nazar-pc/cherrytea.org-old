###*
 * @package		CherryTea
 * @category	themes
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###

social_block	= $('.social-block')
fb				= social_block.find('.fb')
fb
	.click ->
		window.open(
			"//www.facebook.com/sharer.php?u=https\u00253A\u00252F\u00252Fwww.facebook.com\u00252Fcherrytea.org&display=popup",
			'cherrytea-share-fb',
			'height=340, left=' + ($(window).width() / 2 - 335) + ', location=no, toolbar=no, top=' + ($(window).height() / 2 - 170) + ',width=670'
		)
$.getJSON(
	'http://graph.facebook.com/' + 'https://www.facebook.com/cherrytea.org',
	(json) ->
		fb.html(json.likes)
)
