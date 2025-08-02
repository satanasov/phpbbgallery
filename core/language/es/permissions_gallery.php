<?php
/**
*
* permissions_gallery [Spanish]
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

// Adding the permissions
$lang = array_merge($lang, array(
	'ACL_A_GALLERY_MANAGE'		=> 'Puede administrar los ajustes de la galería phpBB',
	'ACL_A_GALLERY_ALBUMS'		=> 'Puede agregar / editar álbumes y permisos',
));
