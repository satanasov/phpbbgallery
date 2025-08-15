<?php
/**
 * phpBB Gallery - ACP Core Extension [German Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator franki <https://dieahnen.de/ahnenforum/>
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
	'UCP_GALLERY'						=> 'Galerie',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Persönliche Alben verwalten',
	'UCP_GALLERY_SETTINGS'				=> 'Persönliche Einstellungen',
	'UCP_GALLERY_WATCH'					=> 'Benachrichtigungen verwalten',
));
