<?php
/**
*
* permissions_gallery [English]
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
	$lang = array();
}

// Adding the permissions
$lang = array_merge($lang, array(
	'ACL_A_GALLERY_MANAGE'		=> 'Kann die Einstellungen der phpBB Gallery ändern',
	'ACL_A_GALLERY_ALBUMS'		=> 'Kann Alben und Berechtigungen hinzufügen oder ändern',
	'ACL_A_GALLERY_IMPORT'		=> 'Kann die Import-Funktion benutzen',
	'ACL_A_GALLERY_CLEANUP'		=> 'Kann die phpBB Gallery reinigen',
));
