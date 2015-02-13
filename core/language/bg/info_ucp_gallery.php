<?php
/**
*
* info_ucp_gallery [English]
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

$lang = array_merge($lang, array(
	'UCP_GALLERY'						=> 'Галерия',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Управелние на лични албуми',
	'UCP_GALLERY_SETTINGS'				=> 'Лични настройки',
	'UCP_GALLERY_WATCH'					=> 'Контрол на абонаментите',
));
