<?php
/**
*
* gallery_mcp [Bulgarian]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2015 Lucifer lucifer@anavaro.com http://www.anavaro.com
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
	'CHOOSE_ACTION'					=> 'Изберете желаното действие',

	'GALLERY_MCP_MAIN'				=> 'Основно',
	'GALLERY_MCP_OVERVIEW'			=> 'Преглед',
	'GALLERY_MCP_QUEUE'				=> 'Опашка',
	'GALLERY_MCP_QUEUE_DETAIL'		=> 'Детайли за изображението',
	'GALLERY_MCP_REPORTED'			=> 'Докладвани изображения',
	'GALLERY_MCP_REPO_DONE'			=> 'Затоворени доклади',
	'GALLERY_MCP_REPO_OPEN'			=> 'Отворени доклади',
	'GALLERY_MCP_REPO_DETAIL'		=> 'Детайли за доклада',
	'GALLERY_MCP_UNAPPROVED'		=> 'Изображения чакащи одобрение',
	'GALLERY_MCP_APPROVED'			=> 'Одобрени изображения',
	'GALLERY_MCP_LOCKED'			=> 'Заключени изображения',
	'GALLERY_MCP_VIEWALBUM'			=> 'Виж албум',
	'GALLERY_MCP_ALBUM_OVERVIEW'	=> 'Модерирай албум',

	'IMAGE_REPORTED'				=> 'Изображението беше докладвано.',
	'IMAGE_UNAPPROVED'				=> 'Изображението чака одобрение.',

	'MODERATE_ALBUM'				=> 'Модерирай албум',

	'LATEST_IMAGES_REPORTED'		=> 'Последните 5 докладвани изображения',
	'LATEST_IMAGES_UNAPPROVED'		=> 'Последните 5 изображения чакащи одобрение',

	'QUEUE_A_APPROVE'				=> 'Одобри изображение',
	'QUEUE_A_APPROVE2'				=> 'Одобри изображение?',
	'QUEUE_A_APPROVE2_CONFIRM'		=> 'Сигурен ли сте, че искате да одорите изображение?',
	'QUEUE_A_DELETE'				=> 'Изтрий изображение',
	'QUEUE_A_DELETE2'				=> 'Изтрий изображение?',
	'QUEUE_A_DELETE2_CONFIRM'		=> 'Сигурен ли сте, че искате да изтриете това изобаение?',
	'QUEUE_A_LOCK'					=> 'Заключи изображение',
	'QUEUE_A_LOCK2'					=> 'Заключи изображение?',
	'QUEUE_A_LOCK2_CONFIRM'			=> 'Сигурен ли сте, че искате да заключите това изображение?',
	'QUEUE_A_MOVE'					=> 'Премести изображение',
	'QUEUE_A_UNAPPROVE'				=> 'Махни одобрение на изборажение',
	'QUEUE_A_UNAPPROVE2'			=> 'Махни одобрение на изборажение?',
	'QUEUE_A_UNAPPROVE2_CONFIRM'	=> 'Сиигирен ли сте, че искате да махнете одобрението на това изборажение?',

	'QUEUE_STATUS_0'				=> 'Изображението чака одобрение.',
	'QUEUE_STATUS_1'				=> 'Изображението е одобрено.',
	'QUEUE_STATUS_2'				=> 'Изображението е заключено.',

	'QUEUES_A_APPROVE'				=> 'Одобри изображенията',
	'QUEUES_A_APPROVE2'				=> 'Одобри изображенията?',
	'QUEUES_A_APPROVE2_CONFIRM'		=> 'Сигурни ли сте, че искате да одобрите тези изображения?',
	'QUEUES_A_DELETE'				=> 'Изтрий избораженията',
	'QUEUES_A_DELETE2'				=> 'Изтрий избораженията?',
	'QUEUES_A_DELETE2_CONFIRM'		=> 'Сигурни ли сте, че искате да изтриете тези изображения?',
	'QUEUES_A_LOCK'					=> 'Заключи избораженията',
	'QUEUES_A_LOCK2'				=> 'Заключи избораженията?',
	'QUEUES_A_LOCK2_CONFIRM'		=> 'Сигурни ли сте, че искате да заключите тези изображения?',
	'QUEUES_A_MOVE'					=> 'Премести избораженията',
	'QUEUES_A_UNAPPROVE'			=> 'Махни одобрението на изборажения',
	'QUEUES_A_UNAPPROVE2'			=> 'Махни одобрението на изборажения?',
	'QUEUES_A_UNAPPROVE2_CONFIRM'	=> 'Сигурни ли сте, че искате да премахнете одобрението на тези избражения?',
	'QUEUES_A_DISAPPROVE2_CONFIRM'	=> 'Сигурни ли сте, че искате да премахнете одобрението на тези избражения?',

	'REPORT_A_CLOSE'				=> 'Затвори доклад',
	'REPORT_A_CLOSE2'				=> 'Затвори доклад?',
	'REPORT_A_CLOSE2_CONFIRM'		=> 'Сигурни ли сте, че искате да затворите този доклад?',
	'REPORT_A_DELETE'				=> 'Изтрий доклад',
	'REPORT_A_DELETE2'				=> 'Изтрий доклад?',
	'REPORT_A_DELETE2_CONFIRM'		=> 'Сигурни ли сте, че искате да изтриете този доклад?',
	'REPORT_A_OPEN'					=> 'Отвори доклад',
	'REPORT_A_OPEN2'				=> 'Отвори доклад?',
	'REPORT_A_OPEN2_CONFIRM'		=> 'Сигурни ли сте, че искате да отворите този доклад?',

	'REPORT_NOT_FOUND'				=> 'Докладът не може да бъде открит.',
	'REPORT_STATUS_1'				=> 'Докладът трябва да бъде прегледан.',
	'REPORT_STATUS_2'				=> 'Докладът е затворен.',

	'REPORTS_A_CLOSE'				=> 'Затовори доклади',
	'REPORTS_A_CLOSE2'				=> 'Затвори докладите?',
	'REPORTS_A_CLOSE2_CONFIRM'		=> 'Сигурни ли сте, че искате да затворите тези доклади?',
	'REPORTS_A_DELETE'				=> 'Изтрий доклади',
	'REPORTS_A_DELETE2'				=> 'Изтрий докладите?',
	'REPORTS_A_DELETE2_CONFIRM'		=> 'Сигурни ли сте, че искате да изтриете тези доклади?',
	'REPORTS_A_OPEN'				=> 'Отвори доклади',
	'REPORTS_A_OPEN2'				=> 'Отвори докладите?',
	'REPORTS_A_OPEN2_CONFIRM'		=> 'Сигурни ли сте, че искате да отворите тези доклади?',

	'REPORT_MOD'					=> 'Променено от',
	'REPORTED_IMAGES'				=> 'Докладвани изображения',
	'REPORTER'						=> 'Докладвал',
	'REPORTER_AND_ALBUM'			=> 'Докладвал и Албум',

	'WAITING_APPROVED_IMAGE'		=> array(
		0			=> 'Не бяха одобрени изображения.',
		1			=> 'Общо <span style="font-weight: bold;">1</span> изображение беше одобрено.',
		2			=> 'Общо <span style="font-weight: bold;">%s</span> изображения бяха одобрени.',
	),
	'WAITING_DISPPROVED_IMAGE'		=> array(
		0			=> 'Не са отхвърлени изображения.',
		1			=> 'Общо <span style="font-weight: bold;">1</span> изображение е отхвърлено.',
		2			=> 'Общо <span style="font-weight: bold;">%s</span> изображения са отхвърлени.',
	),
	'WAITING_LOCKED_IMAGE'			=> array(
		0			=> 'Не бяха заключени изображения.',
		1			=> 'Общо <span style="font-weight: bold;">1</span> изображение беше заключено.',
		2			=> 'Общо <span style="font-weight: bold;">%s</span> изображения бяха заключени.',
	),
	'WAITING_REPORTED_DONE'			=> array(
		0			=> 'Няма прегледани доклади.',
		1			=> 'Общо <span style="font-weight: bold;">1</span> доклад е прегледан.',
		2			=> 'Общо <span style="font-weight: bold;">%s</span> доклада са прегледани.',
	),
	'WAITING_REPORTED_IMAGE'		=> array(
		0			=> 'Няма доклади за преглед.',
		1			=> 'Общо има <span style="font-weight: bold;">1</span> доклад за преглед.',
		2			=> 'Общо има <span style="font-weight: bold;">%s</span> доклада за преглед.',
	),
	'WAITING_UNAPPROVED_IMAGE'		=> array(
		0			=> 'Няма изображения чакащи одобрение.',
		1			=> 'Общо <span style="font-weight: bold;">1</span> изображение чака одобрение.',
		2			=> 'Общо <span style="font-weight: bold;">%s</span> изображения чакат одобрение.',
	),
	'DELETED_IMAGES'		=> array(
		0			=> 'Не са изтрити изображения.',
		1			=> 'Общо <span style="font-weight: bold;">1</span> изборажение е изтрито.',
		2			=> 'Общо <span style="font-weight: bold;">%s</span> изображения са изтрити.',
	),
	'MOVED_IMAGES'		=> array(
		0			=> 'Не са преместени изображения.',
		1			=> 'Общо <span style="font-weight: bold;">1</span> изборажение е преместено.',
		2			=> 'Общо <span style="font-weight: bold;">%s</span> изображения са преместени.',
	),
	'NO_WAITING_UNAPPROVED_IMAGE'	=> 'Няма изображения чакащи одобрение.',
));
