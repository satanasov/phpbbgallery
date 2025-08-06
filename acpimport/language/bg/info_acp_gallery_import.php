<?php
/**
 * phpBB Gallery - ACP Import Extension [Bulgarian Translation]
 *
 * @package   phpbbgallery/acpimport
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
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

$lang = array_merge($lang, [
	'ACP_IMPORT_ALBUMS'				=> 'Вкарване на изображения',
	'ACP_IMPORT_ALBUMS_EXPLAIN'		=> 'Тук можете да вкарате голямо количество изображения от файловата система. Преди да вкарате изображенията, моля оразмерете ги на ръка.',

	'IMPORT_ALBUM'					=> 'Албум за изображенията:',
	'IMPORT_DEBUG_MES'				=> '%1$s изображения са вкарани. Остават още %2$s изображения.',
	'IMPORT_DIR_EMPTY'				=> 'Папката %s е празна. Трябва да качите изображенията, преди да ги вкарате.',
	'IMPORT_FINISHED'				=> 'Всички %1$s изображения са успешно добавени.',
	'IMPORT_FINISHED_ERRORS'		=> '%1$s изображения бяха успешно добавени, но възникна следната грешка:<br /><br />',
	'IMPORT_MISSING_ALBUM'			=> 'Моля изберете албум в който да вкарате изображенията.',
	'IMPORT_SELECT'					=> 'Изберете изображенията, които искате да вкарате. Успешно добавените изображения се изтриват. Всички други са налични.',
	'IMPORT_SCHEMA_CREATED'			=> 'Таблицата за вкарване беше успешно създадена, моля изчакайте прехвърлянето на изображенията.',
	'IMPORT_USER'					=> 'Качено от',
	'IMPORT_USER_EXP'				=> 'Можете да добавите изображения от друг потребител тук.',
	'IMPORT_USERS_PEGA'				=> 'Качи в потребителската галерия.',

	'MISSING_IMPORT_SCHEMA'			=> 'Избраната таблица за вкарване (%s) не може да бъде открита.',

	'NO_FILE_SELECTED'				=> 'Трябва да изберете поне един фаил.',

	'GALLERY_CORE_NOT_FOUND'		=> 'Първо трябва да бъде инсталирано и активирано разширението phpBB Gallery Core.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'Разширението е активирано успешно.',
]);
