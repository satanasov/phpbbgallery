<?php
/**
*
* permissions_gallery [Dutch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* [Dutch] translated by Dutch Translators (https://github.com/dutch-translators)
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
	$lang = array();
}

// Adding the permissions
$lang = array_merge($lang, array(
	'ACL_A_GALLERY_MANAGE'		=> 'Kan de phpBB Galerij instellingen beheren',
	'ACL_A_GALLERY_ALBUMS'		=> 'Kan albums en permissies toevoegen/wijzigen',
));
