<?php
/**
 * phpBB Gallery - ACP CleanUp Extension [Russian Translation]
 *
 * @package   phpbbgallery/acpcleanup
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator Eduard Schlak <https://translations.schlak.info>
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

$lang = array_merge($lang, [
	'ACP_GALLERY_CLEANUP'			=> 'Очистить галерею',

	'ACP_GALLERY_CLEANUP_EXPLAIN'	=> 'Здесь вы можете удалить некоторые остатки.',

	'CLEAN_AUTHORS_DONE'			=> 'Изображения без действительного автора удалены',
	'CLEAN_CHANGED'					=> 'Автор изменено на "Гость"',
	'CLEAN_COMMENTS_DONE'			=> 'Комментарии без действительного автора удалены',
	'CLEAN_ENTRIES_DONE'			=> 'Файлы без записи в Базе данных удалены',
	'CLEAN_GALLERY'					=> 'Чистить галерею',
	'CLEAN_GALLERY_ABORT'			=> 'Отменить очистку!',
	'CLEAN_NO_ACTION'				=> 'Никакие действия не завершены. Что-то пошло не так!',
	'CLEAN_PERSONALS_DONE'			=> 'Личные альбомы без действительного владельца удалены',
	'CLEAN_PERSONALS_BAD_DONE'		=> 'Личные альбомы выбранных пользователей удаленны',
	'CLEAN_PRUNE_DONE'				=> 'Изображения успешно очищены',
	'CLEAN_PRUNE_NO_PATTERN'		=> 'Нет поискового шаблона',
	'CLEAN_SOURCES_DONE'			=> 'Изображения без файла удалены',

	'CONFIRM_CLEAN'					=> 'Это действие не может быть отменено!',
	'CONFIRM_CLEAN_AUTHORS'			=> 'Удалить изображеиня без действительного автора?',
	'CONFIRM_CLEAN_COMMENTS'		=> 'Удалить комментарии без действительного автора?',
	'CONFIRM_CLEAN_ENTRIES'			=> 'Файлы без записей в БД удалить?',
	'CONFIRM_CLEAN_PERSONALS'		=> 'Удалить личные альбомы без действительных владельцев?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_PERSONALS_BAD'	=> 'Удалить личные альбомы выбранных пользователей?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_SOURCES'			=> 'Изображения без файла удалить?',
	'CONFIRM_PRUNE'					=> 'Все изображения, имеющие следующие условия, удалить:<br /><br />%s<br />',

	'PRUNE'							=> 'Очистить',
	'PRUNE_ALBUMS'					=> 'Очистить альбомы',
	'PRUNE_CHECK_OPTION'			=> 'Включите эту опцию, если вы хотите, чтобы очистить изображения',
	'PRUNE_COMMENTS'				=> 'Менее чем x комментарий',
	'PRUNE_PATTERN_ALBUM_ID'		=> 'Изображение находится в одном из следующих альбомов:<br />&raquo; <strong>%s</strong>',
	'PRUNE_PATTERN_COMMENTS'		=> 'Изображение имеет меньше, чем <strong>%d</strong> комментарий',
	'PRUNE_PATTERN_RATES'			=> 'Изображение имеет меньше, чем <strong>%d</strong> отзывов',
	'PRUNE_PATTERN_RATE_AVG'		=> 'Изображение имеет среднюю оценку меньше, чем <strong>%s</strong>.',
	'PRUNE_PATTERN_TIME'			=> 'Изображение было через “<strong>%s</strong>“ загружено',
	'PRUNE_PATTERN_USER_ID'			=> 'Изображение было загружено одним из следующих пользователей:<br />&raquo; <strong>%s</strong>',
	'PRUNE_RATINGS'					=> 'Менее, чем x оценок',
	'PRUNE_RATING_AVG'				=> 'Средняя оценка ниже, чем',
	'PRUNE_RATING_AVG_EXP'			=> 'Будут очищены только изображения, со средней оценкой менее, чем “<samp>x.yz</samp>“.',
	'PRUNE_TIME'					=> 'Загружено перед',
	'PRUNE_TIME_EXP'				=> 'Очистить только изображения, которые были загружены перед “<samp>ГГГГ-ММ-ДД</samp>“',
	'PRUNE_USERNAME'				=> 'Загружено пользователем',
	'PRUNE_USERNAME_EXP'			=> 'Только изображения следующих пользователей очистить. Чтобы очистить изображения "гостей", установите флажок над полем имени пользователя',

	//Log
	'LOG_CLEANUP_DELETE_FILES'	    => '%s Изображения без БД записей были удалены',
	'LOG_CLEANUP_DELETE_ENTRIES'	=> '%s Изображения без файлов были удалены',
	'LOG_CLEANUP_DELETE_NO_AUTHOR'	=> '%s Изображения без действительных авторов были удалены',
	'LOG_CLEANUP_COMMENT_DELETE_NO_AUTHOR'	=> '%s Комментарии без действительных авторов были удалены',

	'MOVE_TO_IMPORT'	=> 'Move images to Import directory',
	'MOVE_TO_USER'		=> 'Move to user',
	'MOVE_TO_USER_EXP'	=> 'Images and comments will be moved as those of user you have defined. If none is selected - Anonymous will be used.',
	'CLEAN_USER_NOT_FOUND'	=> 'The user you selected does not exists!',

	'GALLERY_CORE_NOT_FOUND'		=> 'Сначала необходимо установить и включить расширение phpBB Gallery Core.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'Расширение успешно включено.',
]);
