<?php
/**
*
* info_ucp_gallery [Deutsch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Übersetzt von franki (http://dieahnen.de/ahnenforum/)
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
	$lang = array();
}

$lang = array_merge($lang, array(
	'UCP_GALLERY'						=> 'Galerie',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Persönliche Alben verwalten',
	'UCP_GALLERY_SETTINGS'				=> 'Persönliche Einstellungen',
	'UCP_GALLERY_WATCH'					=> 'Benachrichtigungen verwalten',
	'UCP_GALLERY_FAVORITES'				=> 'Galerie-Favoriten',
));
