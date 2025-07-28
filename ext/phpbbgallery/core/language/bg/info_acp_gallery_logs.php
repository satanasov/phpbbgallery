<?php
/**
*
* info_acp_gallery_logs [English]
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
	'ACP_LOG_GALLERY_MOD'						=> 'Модераторски лог',
	'ACP_LOG_GALLERY_MOD_EXP'					=> 'Системен лог с всички модераторски действия.',
	'ACP_LOG_GALLERY_ADM'						=> 'Администраторски лог',
	'ACP_LOG_GALLERY_ADM_EXP'					=> 'Системен лог с всички действия извърешени от администрацията и свързани с галерията.',
	'ACP_LOG_GALLERY_SYSTEM'						=> 'Системен лог',
	'ACP_LOG_GALLERY_SYSTEM_EXP'					=> 'Системен лог с всички съобщяния свързани с действието на системата',
	'LOG_GALLERY_SHOW_LOGS'						=> 'Покажи само',

	'SORT_USER_ID'							=> 'User ID',

	'LOG_ALBUM_ADD'							=> '<strong>Създаден нов албум</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUM'					=> '<strong>Изтрит албум</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUMS'					=> '<strong>Изтрити албум и подалбуми</strong><br />» %s',
	'LOG_ALBUM_DEL_MOVE_ALBUMS'				=> '<strong>Изтрит албум и преместени подалбуми</strong> в %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES'				=> '<strong>Изтрит албум и преместени изображения </strong> в %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_ALBUMS'		=> '<strong>Изтрити албум и подалбуми, преместени изображения</strong> в %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_MOVE_ALBUMS'	=> '<strong>Изтрит албум, преместени изображения</strong> в %1$s <strong>и подалбуми</strong> в %2$s<br />» %3$s',
	'LOG_ALBUM_DEL_IMAGES'					=> '<strong>Изтрити албум и изображенията му</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_ALBUMS'			=> '<strong>Изтрити албум, изображения и подалбуми</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_MOVE_ALBUMS'		=> '<strong>Изтрити албум и изображения, преместени подалбуми</strong> в %1$s<br />» %2$s',
	'LOG_ALBUM_EDIT'						=> '<strong>Променени детайли за албум</strong><br />» %s',
	'LOG_ALBUM_MOVE_DOWN'					=> '<strong>Албум</strong> %1$s <strong>преместен под</strong> %2$s',
	'LOG_ALBUM_MOVE_UP'						=> '<strong>Албум</strong> %1$s <strong>преместен над</strong> %2$s',
	'LOG_ALBUM_SYNC'						=> '<strong>Ресинхронизиран албум</strong><br />» %s',

	'LOG_CLEAR_GALLERY'					=> 'Изтрит е лога на галерията',

	'LOG_GALLERY_APPROVED'				=> '<strong>Одобрено изображение</strong><br />» %s',
	'LOG_GALLERY_COMMENT_DELETED'		=> '<strong>Изтрит коментар</strong><br />» %s',
	'LOG_GALLERY_COMMENT_EDITED'		=> '<strong>Редактиран коментр</strong><br />» %s',
	'LOG_GALLERY_DELETED'				=> '<strong>Изтрито изображение</strong><br />» %s',
	'LOG_GALLERY_EDITED'				=> '<strong>Променено изображение</strong><br />» %s',
	'LOG_GALLERY_LOCKED'				=> '<strong>Заключено изображение</strong><br />» %s',
	'LOG_GALLERY_MOVED'					=> '<strong>Преместено изображение</strong><br />» от %1$s в %2$s',
	'LOG_GALLERY_REPORT_CLOSED'			=> '<strong>Затворен доклад</strong><br />» %s',
	'LOG_GALLERY_REPORT_DELETED'		=> '<strong>Изтрит доклад</strong><br />» %s',
	'LOG_GALLERY_REPORT_OPENED'			=> '<strong>Отворен доклад</strong><br />» %s',
	'LOG_GALLERY_UNAPPROVED'			=> '<strong>Премахнато одобрение на изображение</strong><br />» %s',
	'LOG_GALLERY_DISAPPROVED'			=> '<strong>Отхвърлено изображение</strong><br />» %s',

	'LOGVIEW_VIEWALBUM'					=> 'Виж албум',
	'LOGVIEW_VIEWIMAGE'					=> 'Виж изображение',
));
