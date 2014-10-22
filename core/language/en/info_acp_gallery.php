<?php
/**
*
* info_acp_gallery [English]
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
	'ACP_GALLERY_ALBUM_MANAGEMENT'		=> 'Album management',
	'ACP_GALLERY_ALBUM_PERMISSIONS'		=> 'Permissions',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY'=> 'Copy permissions',
	'ACP_GALLERY_CONFIGURE_GALLERY'		=> 'Configure gallery',
	'ACP_GALLERY_LOGS'					=> 'Gallery log',
	'ACP_GALLERY_LOGS_EXPLAIN'			=> 'This lists all moderator actions of the gallery, like approving, disapproving, locking, unlocking, closing reports and deleting images.',
	'ACP_GALLERY_MANAGE_ALBUMS'			=> 'Manage albums',
	'ACP_GALLERY_OVERVIEW'				=> 'Overview',

	'GALLERY'							=> 'Gallery',
	'GALLERY_EXPLAIN'					=> 'Image Gallery',
	'GALLERY_HELPLINE_ALBUM'			=> 'Gallery image: [album]image_id[/album], with this BBCode you can add an image from the gallery into your post.',
	'GALLERY_POPUP'						=> 'Gallery',
	'GALLERY_POPUP_HELPLINE'			=> 'Open a popup where you can select your recent images and upload new images.',

	// A little line where you can give yourself some credits on the translation.
	//'GALLERY_TRANSLATION_INFO'			=> 'English “phpBB Gallery“-Translation by <a href="http://www.flying-bits.org/">nickvergessen</a>',
	'GALLERY_TRANSLATION_INFO'			=> '',

	'IMAGES'							=> 'Images',
	'IMG_BUTTON_UPLOAD_IMAGE'			=> 'Upload image',

	'PERSONAL_ALBUM'					=> 'Personal album',
	'PHPBB_GALLERY'						=> 'phpBB Gallery',

	'TOTAL_IMAGES_SPRINTF'				=> array(
		0		=> 'Total images <strong>0</strong>',
		1		=> 'Total images <strong>%d</strong>',
	),
));
