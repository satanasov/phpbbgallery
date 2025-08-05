<?php
/**
 * phpBB Gallery - ACP Core Extension [Bulgarian Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator Lucifer <https://www.anavaro.com>
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, array(
	'UCP_GALLERY'						=> 'Галерия',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Управелние на лични албуми',
	'UCP_GALLERY_SETTINGS'				=> 'Лични настройки',
	'UCP_GALLERY_WATCH'					=> 'Контрол на абонаментите',
));
