<?php
/**
*
* Gallery Notifications [English]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 Lucifer Lucifer@anavaro.com http://www.anavaro.com
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s uploaded images for approval in album <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Images waiting for approval',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Approved images',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Images in album <strong>%1$s</strong> were approved',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'New images',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'New images were uploaded to album <strong>%1$s</strong>',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'New comments',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> commented on image you are watching',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'New image report',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> reported image',
));
