<?php
/**
*
* permissions_gallery [Russian]
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
	'ACL_A_GALLERY_MANAGE'		=> 'Может настраивать галерею',
	'ACL_A_GALLERY_ALBUMS'		=> 'Может добавлять/редактировать альбомы и права доступа',
));
