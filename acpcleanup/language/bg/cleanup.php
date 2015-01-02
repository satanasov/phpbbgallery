<?php
/**
*
* @package Gallery - ACP CleanUp Extension [English]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
	'ACP_GALLERY_CLEANUP_EXPLAIN'	=> 'Тук можете да изтриете малко останки.',

	'CLEAN_AUTHORS_DONE'			=> 'Изображения без валиден автор са изтрити.',
	'CLEAN_CHANGED'					=> 'Автора променен на "Гост".',
	'CLEAN_COMMENTS_DONE'			=> 'Коментари без валиден автор - изтрити.',
	'CLEAN_ENTRIES_DONE'			=> 'Файлове без DB редове - изтрити.',
	'CLEAN_GALLERY'					=> 'Изчисти галерия',
	'CLEAN_GALLERY_ABORT'			=> 'Прекрати почистване!',
	'CLEAN_NO_ACTION'				=> 'Не беше извършено действие. Нещо се прецака!',
	'CLEAN_PERSONALS_DONE'			=> 'Лични албуми без валиден собственик - изтрити!',
	'CLEAN_PERSONALS_BAD_DONE'		=> 'Лични албуми от избраните потребители - изтрити.',
	'CLEAN_PRUNE_DONE'				=> 'Успешно прочеистени изображения.',
	'CLEAN_PRUNE_NO_PATTERN'		=> 'Няма задедн параметър за търсене.',
	'CLEAN_SOURCES_DONE'			=> 'Изображения без файлове - изтрити!',

	'CONFIRM_CLEAN'					=> 'Тази стъпка не може да сърне на зад!',
	'CONFIRM_CLEAN_AUTHORS'			=> 'Изтрий изображения без валиден автор?',
	'CONFIRM_CLEAN_COMMENTS'		=> 'Изтрий коментари без валиден автор?',
	'CONFIRM_CLEAN_ENTRIES'			=> 'Изтрий файлове без запис в базата?',
	'CONFIRM_CLEAN_PERSONALS'		=> 'Изтрий лични албуми без валиден соственик?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_PERSONALS_BAD'	=> 'Изтрий личните албуми от избран потребител?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_SOURCES'			=> 'Изтрий изображения без валиден фаил?',
	'CONFIRM_PRUNE'					=> 'Изтрий всички изборажения отговарящи на следните условия:<br /><br />%s<br />',

	'PRUNE'							=> 'Прочисти',
	'PRUNE_ALBUMS'					=> 'Прочисти албум',
	'PRUNE_CHECK_OPTION'			=> 'Check this option, while pruning images.',
	'PRUNE_COMMENTS'				=> 'Less than x comments',
	'PRUNE_PATTERN_ALBUM_ID'		=> 'The image is in one of the following albums:<br />&raquo; <strong>%s</strong>',
	'PRUNE_PATTERN_COMMENTS'		=> 'The image has less than <strong>%d</strong> comments.',
	'PRUNE_PATTERN_RATES'			=> 'The image has less than <strong>%d</strong> ratings.',
	'PRUNE_PATTERN_RATE_AVG'		=> 'The image has a rating average, lower than <strong>%s</strong>.',
	'PRUNE_PATTERN_TIME'			=> 'The image was uploaded before “<strong>%s</strong>“.',
	'PRUNE_PATTERN_USER_ID'			=> 'The image was uploaded by one of the following users:<br />&raquo; <strong>%s</strong>',
	'PRUNE_RATINGS'					=> 'Less than x ratings',
	'PRUNE_RATING_AVG'				=> 'Average rating lower than',
	'PRUNE_RATING_AVG_EXP'			=> 'Only prune images, with an average rating lower than “<samp>x.yz</samp>“.',
	'PRUNE_TIME'					=> 'Uploaded before',
	'PRUNE_TIME_EXP'				=> 'Only prune images, that where uploaded before “<samp>YYYY-MM-DD</samp>“.',
	'PRUNE_USERNAME'				=> 'Uploaded by',
	'PRUNE_USERNAME_EXP'			=> 'Only prune images from certain users. To prune images from “guests“ select the checkbox beyond the username-box.',
));
