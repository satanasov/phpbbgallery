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
	// Please do not change the copyright.
	'GALLERY_COPYRIGHT'	=> 'Powered by <a href="http://www.anavaro.com/">phpBB Gallery</a> &copy; 2016 <a href="http://www.anavaro.com/">Lucifer</a>',

	// A little line where you can give yourself some credits on the translation.
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
