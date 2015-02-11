<?php
/**
*
* Gallery Notifications [Bulgarian]
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s качи изображения за одобрение в албум <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Изображения чакащи одобрение',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Одобрени изображения',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Изображенията в албум <strong>%1$s</strong> бяха одобрени',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Нови изображения',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'В албум <strong>%1$s</strong> бяха качени нови изобаржения',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Нови коментари',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> коментира изображение което следите',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Нов доклад на изображение',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> докладва изображение',
));
