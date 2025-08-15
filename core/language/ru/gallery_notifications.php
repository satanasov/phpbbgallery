<?php
/**
 * phpBB Gallery - ACP Core Extension [Russian Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s фото загружено для утверждения в альбоме <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Фото ждёт одобрение',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Фото одобрено',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Фото в альбоме <strong>%1$s</strong> были одобрены',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_NOT_APPROVED'	=> 'Not approved images',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_NOT_APPROVED'		=> 'Images in album <strong>%1$s</strong> were not approved',
	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Новые фото',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'Новые фото были загружены в альбом <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Новые комментарии',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> прокомментировал фото',
	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Новая жалоба',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> пожаловался на изображение',
));
