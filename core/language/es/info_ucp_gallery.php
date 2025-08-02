<?php
/**
*
* info_ucp_gallery [Spanish]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
**/

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
