<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			h;
$Page		= Page::instance();
if (User::instance()->user()) {
	$Page->js('//api-maps.yandex.ru/2.1-dev/?lang=ru-RU&load=package.full&lang=uk-UA');
}