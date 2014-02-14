<?php
/**
 * @package		Get emails
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs;
use			h;
$db		= DB::instance();
$emails	= $db->qfas(
	"SELECT `email`
	FROM `[prefix]users`"
);
Index::instance()->form	= false;
Page::instance()->content(
	h::{'textarea[rows=10]'}($emails)
);
