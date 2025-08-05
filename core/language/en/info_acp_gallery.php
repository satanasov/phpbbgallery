<?php
/**
 * phpBB Gallery - ACP Core Extension
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
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
	'ACP_GALLERY_ALBUM_MANAGEMENT'       => 'Album management',
	'ACP_GALLERY_ALBUM_PERMISSIONS'      => 'Permissions',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY' => 'Copy permissions',
	'ACP_GALLERY_CONFIGURE_GALLERY'      => 'Configure gallery',
	'ACP_GALLERY_LOGS'                   => 'Gallery log',
	'ACP_GALLERY_LOGS_EXPLAIN'           => 'This lists all moderator actions of the gallery, like approving, disapproving, locking, unlocking, closing reports and deleting images.',
	'ACP_GALLERY_MANAGE_ALBUMS'          => 'Manage albums',
	'ACP_GALLERY_OVERVIEW'               => 'Overview',

	'GALLERY'                  => 'Gallery',
	'GALLERY_EXPLAIN'          => 'Image Gallery',
	'GALLERY_HELPLINE_ALBUM'   => 'Gallery image: [image]image_id[/image], with this BBCode you can add an image from the gallery into your post.',
	'GALLERY_POPUP'            => 'Gallery',
	'GALLERY_POPUP_HELPLINE'   => 'Open a popup where you can select your recent images and upload new images.',

	// Please do not change the copyright.
	'GALLERY_COPYRIGHT'        => 'Powered by <a href="http://www.anavaro.com/">phpBB Gallery</a> &copy; 2016 <a href="http://www.anavaro.com/">Lucifer</a>',

	// A little line where you can give yourself some credits on the translation.
	//'GALLERY_TRANSLATION_INFO'			=> 'English “phpBB Gallery“-Translation by <a href="http://www.flying-bits.org/">nickvergessen</a>',
	'GALLERY_TRANSLATION_INFO' => '',

	'IMAGES'                  => 'Images',
	'IMG_BUTTON_UPLOAD_IMAGE' => 'Upload image',

	'PERSONAL_ALBUM' => 'Personal album',
	'PHPBB_GALLERY'  => 'phpBB Gallery',

	'TOTAL_IMAGES_SPRINTF' => array(
		0 => 'Total images <strong>0</strong>',
		1 => 'Total images <strong>%d</strong>',
	),
));
