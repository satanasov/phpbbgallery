<?php
/**
 * phpBB Gallery - ACP Core Extension [Italian Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator
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
	'UCP_GALLERY'						=> 'Galleria',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Gestisci album personali',
	'UCP_GALLERY_SETTINGS'				=> 'Impostazioni personali',
	'UCP_GALLERY_WATCH'					=> 'Gestisci sottoscrizioni',
));
