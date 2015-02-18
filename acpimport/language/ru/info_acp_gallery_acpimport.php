<?php
/**
*
* @package Gallery - ACP Import Extension [Russian]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
* Translation from Eduard Schlak (http://translations.schlak.info/)
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
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_IMPORT_ALBUMS'				=> 'Импорт новых изображений',
	'ACP_IMPORT_ALBUMS_EXPLAIN'		=> 'Здесь вы можете ввести количество изображений, которые будут импортированы. Перед тем, как импортировать изображения, измените размер вручную, используя программное обеспечение для редактирования изображений',

	'IMPORT_ALBUM'					=> 'Альбом назначения:',
	'IMPORT_DEBUG_MES'				=> '%1$s изображений импортированы. Ещё %2$s изображений надо импортировать',
	'IMPORT_DIR_EMPTY'				=> 'Каталог %s пустой.  Вы должны сначала загрузить изображения, прежде чем вы можете импортировать их',
	'IMPORT_FINISHED'				=> 'Все %1$s изображений успешно импортированы',
	'IMPORT_FINISHED_ERRORS'		=> '%1$s изображений были успешно импортированы, но произошли следующие ошибки:<br /><br />',
	'IMPORT_MISSING_ALBUM'			=> 'Выберите, пожалуйста, альбом, в который должны быть импортированы изображения',
	'IMPORT_SELECT'					=> 'Выберите изображения, которые вы хотите импортировать. Изображения, которые были успешно импортированы, будут удалены из выбора. После этого другие изображения будут для вас ещё доступны',
	'IMPORT_SCHEMA_CREATED'			=> 'Схема импорта был создана. Подожгите, пожалуйста, пока изображения будут импортированы',
	'IMPORT_USER'					=> 'Загружено пользователем',
	'IMPORT_USER_EXP'				=> 'Изображения могут вами также и другому пользователю быть назначены',
	'IMPORT_USERS_PEGA'				=> 'Добавить в личный альбом пользователя',

	'MISSING_IMPORT_SCHEMA'			=> 'Схема импорта (%s) не могла быть найдена',

	'NO_FILE_SELECTED'				=> 'Вы должны выбрать минимум один файл',
));
