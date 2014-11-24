<?php
/**
*
* permissions_gallery [Deutsch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Übersetzt von franki (http://motorradforum-niederrhein.de/downloads/)
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

$lang['permission_cat']['gallery'] = 'phpBB Galerie';

// Adding the permissions
$lang = array_merge($lang, array(
	'acl_a_gallery_manage'		=> array('lang' => 'Kann die Einstellungen der phpBB Gallery ändern',		'cat' => 'gallery'),
	'acl_a_gallery_albums'		=> array('lang' => 'Kann Alben und Berechtigungen hinzufügen und ändern',	'cat' => 'gallery'),
	'acl_a_gallery_import'		=> array('lang' => 'Kann die Import-Funktion benutzen',						'cat' => 'gallery'),
	'acl_a_gallery_cleanup'		=> array('lang' => 'Kann die phpBB Gallery reinigen',						'cat' => 'gallery'),
));
