<?php
/**
*
* info_acp_gallery [Russian]
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
	'ACP_GALLERY_ALBUM_MANAGEMENT'		=> 'Управление альбомом',
	'ACP_GALLERY_ALBUM_PERMISSIONS'		=> 'Права доступа',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY'=> 'Копирование прав доступа',
	'ACP_GALLERY_CONFIGURE_GALLERY'		=> 'Настройка галереи',
	'ACP_GALLERY_LOGS'					=> 'Лог галереи',
	'ACP_GALLERY_LOGS_EXPLAIN'			=> 'Список действий, выполненных в галерее, таких как одобрение, отклонение, блокировка и разблокировка, закрытие жалоб и удаление фотографий.',
	'ACP_GALLERY_MANAGE_ALBUMS'			=> 'Управление альбомами',
	'ACP_GALLERY_OVERVIEW'				=> 'Обзор',
	'GALLERY'							=> 'Галерея',
	'GALLERY_EXPLAIN'					=> 'Фотогалерея',
	'GALLERY_HELPLINE_ALBUM'			=> 'Фото из галереи: [album]ID фото[/album]',
	'GALLERY_POPUP'						=> 'Галерея',
	'GALLERY_POPUP_HELPLINE'			=> 'Выбрать фото из галереи или загрузить новое',
	// A little line where you can give yourself some credits on the translation.
	//'GALLERY_TRANSLATION_INFO'			=> 'English "phpBB Gallery"-Translation by <a href="http://www.flying-bits.org/">nickvergessen</a>',
	'GALLERY_TRANSLATION_INFO'			=> 'Русский перевод phpBB Gallery — <a href="http://www.phpbbguru.net/">www.phpbbguru.net</a>',
	'IMAGES'							=> 'Фото',
	'IMG_BUTTON_UPLOAD_IMAGE'			=> 'Загрузка фото',
	'PERSONAL_ALBUM'					=> 'Фотоальбом',
	'PHPBB_GALLERY'						=> 'Галерея',
	'TOTAL_IMAGES_SPRINTF'				=> array(
		0		=> 'Фотографий в галерее: <strong>0</strong>',
		1		=> 'Фотографий в галерее: <strong>%d</strong>',
	),
));
