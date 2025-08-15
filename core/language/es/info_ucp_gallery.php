<?php
/**
 * phpBB Gallery - ACP Core Extension [Spanish Translation]
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
	'UCP_GALLERY'						=> 'Galería',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Administrar álbumes personales',
	'UCP_GALLERY_SETTINGS'				=> 'Configuraciones personales',
	'UCP_GALLERY_WATCH'					=> 'Administrar Suscripciones',
));
