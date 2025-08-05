<?php
/**
 * phpBB Gallery - ACP Core Extension
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
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
	'UCP_GALLERY'						=> 'Gallery',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Manage personal albums',
	'UCP_GALLERY_SETTINGS'				=> 'Personal settings',
	'UCP_GALLERY_WATCH'					=> 'Manage subscriptions',
));
