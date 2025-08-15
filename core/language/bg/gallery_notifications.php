<?php
/**
 * phpBB Gallery - ACP Core Extension [Bulgarian Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator Lucifer <https://www.anavaro.com>
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s качи изображения за одобрение в албум <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Изображения чакащи одобрение',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Одобрени изображения',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Изображенията в албум <strong>%1$s</strong> бяха одобрени',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_NOT_APPROVED'	=> 'Отхвърлени изображения',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_NOT_APPROVED'		=> 'Изображенията в албум <strong>%1$s</strong> бяха отхвърлени',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Нови изображения',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'В албум <strong>%1$s</strong> бяха качени нови изобаржения',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Нови коментари',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> коментира изображение което следите',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Нов доклад на изображение',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> докладва изображение',
));
