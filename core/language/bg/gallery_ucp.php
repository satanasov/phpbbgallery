<?php
/**
*
* gallery_ucp [English]
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
	'ACCESS_CONTROL_ALL'			=> 'Всички',
	'ACCESS_CONTROL_REGISTERED'		=> 'Регистрирани потрбители',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Регистрирани потрбители, освен враговете ви',
	'ACCESS_CONTROL_FRIENDS'		=> 'Само приятели',
	'ACCESS_CONTROL_SPECIAL_FRIENDS'		=> 'Само за специални приятели',
	'ALBUMS'						=> 'Албуми',
	'ALBUM_ACCESS'					=> 'Позволи достъп на',
	'ALBUM_ACCESS_EXPLAIN'			=> 'Може да изпозлзвате списък с %1$sПриятели и Врагове%2$s за да контролирате достъпа до този албум. <strong>Модеараторите</strong> могат <strong>винаги</strong> да достъпят албума.',
	'ALBUM_DESC'					=> 'Описание на албума',
	'ALBUM_NAME'					=> 'Име на албума',
	'ALBUM_PARENT'					=> 'Старши албум',
	'ATTACHED_SUBALBUMS'			=> 'Прикачени албуми',

	'CREATE_PERSONAL_ALBUM'			=> 'Създай личен албум',
	'CREATE_SUBALBUM'				=> 'Създай подалбум',
	'CREATE_SUBALBUM_EXP'			=> 'Можете да прикачите нов подалбум към вашата лична галерия.',
	'CREATED_SUBALBUM'				=> 'Подалбума е създаден успешно',

	'DELETE_ALBUM'					=> 'Изтрий албум',
	'DELETE_ALBUM_CONFIRM'			=> 'Изтрий албум с всички прикачени подалбуми и изображения?',
	'DELETED_ALBUMS'				=> 'Албумите са успешно изтрити',

	'EDIT'							=> 'Промени',
	'EDIT_ALBUM'					=> 'Промени албум',
	'EDIT_SUBALBUM'					=> 'Промени подалбум',
	'EDIT_SUBALBUM_EXP'				=> 'Тук можете да променяте албумите си.',
	'EDITED_SUBALBUM'				=> 'Албумът е успешно променен',

	'GOTO'							=> 'Отиди',

	'MANAGE_SUBALBUMS'				=> 'Управлявай подалбумите си',
	'MISSING_ALBUM_NAME'			=> 'Моля въведете име за този албум',

	'NEED_INITIALISE'				=> 'Все още нямате личен албум.',
	'NO_ALBUM_STEALING'				=> 'Не можете да оправлявате албума на други потребители.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'Вие добавихте максимума подалбум към личния си албум',
	'NO_PARENT_ALBUM'				=> '&laquo;-- без старши',
	'NO_PERSALBUM_ALLOWED'			=> 'Нямате права да създадете собствения си албум',
	'NO_PERSONAL_ALBUM'				=> 'Все още нямате личен албум. Тук можете да създадете личен албум с подалбуми.<br />В личните албуми само собственика може да качва изображения.',
	'NO_SUBALBUMS'					=> 'Няма свързани албуми',
	'NO_SUBSCRIPTIONS'				=> 'Не сте се абонирали за никакви изображения.',

	'PARSE_BBCODE'					=> 'Parse BBCode',
	'PARSE_SMILIES'					=> 'Parse smilies',
	'PARSE_URLS'					=> 'Parse links',
	'PERSONAL_ALBUM'				=> 'Личен албум',

	'UNSUBSCRIBE'					=> 'спри да следиш',
	'USER_ALLOW_COMMENTS'			=> 'Позволи потребителите да коментират изображенията',

	'YOUR_SUBSCRIPTIONS'			=> 'Тук виждате албумите и изображенията за които ще бъдете нотифицирани.',

	'WATCH_CHANGED'					=> 'Настройките запазени',
	'WATCH_COM'						=> 'Абонирай се за избораженията които коментирате по подразбиране',
	'WATCH_NOTE'					=> 'Тази настройка влияе само на нови изборажения. Всички други изображения трябва да бъдат добавени през опцията "абонирай се за изображение".',
	'WATCH_OWN'						=> 'Абонирай се за собствените си изборажения по подразбиране',
));
