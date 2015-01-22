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

	'QUEUE_STATUS_0'				=> 'Изображението чака одопрение.',
	'QUEUE_STATUS_1'				=> 'Изображението е одобрено.',
	'QUEUE_STATUS_2'				=> 'Изображението е заключено.',

	'QUEUES_A_APPROVE'				=> 'Approve images',
	'QUEUES_A_APPROVE2'				=> 'Approve images?',
	'QUEUES_A_APPROVE2_CONFIRM'		=> 'Are you sure, you want to approve these images?',
	'QUEUES_A_DELETE'				=> 'Delete images',
	'QUEUES_A_DELETE2'				=> 'Delete images?',
	'QUEUES_A_DELETE2_CONFIRM'		=> 'Are you sure, you want to delete these images?',
	'QUEUES_A_LOCK'					=> 'Lock images',
	'QUEUES_A_LOCK2'				=> 'Lock images?',
	'QUEUES_A_LOCK2_CONFIRM'		=> 'Are you sure, you want to lock these images?',
	'QUEUES_A_MOVE'					=> 'Move images',
	'QUEUES_A_UNAPPROVE'			=> 'Unapprove images',
	'QUEUES_A_UNAPPROVE2'			=> 'Unapprove images?',
	'QUEUES_A_UNAPPROVE2_CONFIRM'	=> 'Are you sure, you want to unapprove these images?',
	'QUEUES_A_DISAPPROVE2_CONFIRM'	=> 'Are you sure, you want to unapprove these images?',

	'REPORT_A_CLOSE'				=> 'Close report',
	'REPORT_A_CLOSE2'				=> 'Close report?',
	'REPORT_A_CLOSE2_CONFIRM'		=> 'Are you sure, you want to close this report?',
	'REPORT_A_DELETE'				=> 'Delete report',
	'REPORT_A_DELETE2'				=> 'Delete report?',
	'REPORT_A_DELETE2_CONFIRM'		=> 'Are you sure, you want to delete this report?',
	'REPORT_A_OPEN'					=> 'Open report',
	'REPORT_A_OPEN2'				=> 'Open report?',
	'REPORT_A_OPEN2_CONFIRM'		=> 'Are you sure, you want to open this report?',

	'REPORT_NOT_FOUND'				=> 'The report could not be found.',
	'REPORT_STATUS_1'				=> 'The report needs to be reviewed.',
	'REPORT_STATUS_2'				=> 'The report is closed.',

	'REPORTS_A_CLOSE'				=> 'Close reports',
	'REPORTS_A_CLOSE2'				=> 'Close reports?',
	'REPORTS_A_CLOSE2_CONFIRM'		=> 'Are you sure, you want to close these reports?',
	'REPORTS_A_DELETE'				=> 'Delete reports',
	'REPORTS_A_DELETE2'				=> 'Delete reports?',
	'REPORTS_A_DELETE2_CONFIRM'		=> 'Are you sure, you want to delete these reports?',
	'REPORTS_A_OPEN'				=> 'Open reports',
	'REPORTS_A_OPEN2'				=> 'Open reports?',
	'REPORTS_A_OPEN2_CONFIRM'		=> 'Are you sure, you want to open these reports?',

	'REPORT_MOD'					=> 'Edited by',
	'REPORTED_IMAGES'				=> 'Reported images',
	'REPORTER'						=> 'Reporting user',
	'REPORTER_AND_ALBUM'			=> 'Reporter & Album',

	'WAITING_APPROVED_IMAGE'		=> array(
		0			=> 'No images approved.',
		1			=> 'In total there is <span style="font-weight: bold;">1</span> image approved.',
		2			=> 'In total there are <span style="font-weight: bold;">%s</span> images approved.',
	),
	'WAITING_DISPPROVED_IMAGE'		=> array(
		0			=> 'Не са отхвърлени изображения.',
		1			=> '<span style="font-weight: bold;">1</span> изображение е отхвърлено.',
		2			=> '<span style="font-weight: bold;">%s</span> изображения са отхвърлени.',
	),
	'WAITING_LOCKED_IMAGE'			=> array(
		0			=> 'No images locked.',
		1			=> 'In total there is <span style="font-weight: bold;">1</span> image locked.',
		2			=> 'In total there are <span style="font-weight: bold;">%s</span> images locked.',
	),
	'WAITING_REPORTED_DONE'			=> array(
		0			=> 'No reports reviewed.',
		1			=> 'In total there is <span style="font-weight: bold;">1</span> report reviewed.',
		2			=> 'In total there are <span style="font-weight: bold;">%s</span> reports reviewed.',
	),
	'WAITING_REPORTED_IMAGE'		=> array(
		0			=> 'No reports to review.',
		1			=> 'In total there is <span style="font-weight: bold;">1</span> report to review.',
		2			=> 'In total there are <span style="font-weight: bold;">%s</span> reports to review.',
	),
	'WAITING_UNAPPROVED_IMAGE'		=> array(
		0			=> 'No images waiting for approval.',
		1			=> 'In total there is <span style="font-weight: bold;">1</span> image waiting for approval.',
		2			=> 'In total there are <span style="font-weight: bold;">%s</span> images waiting for approval.',
	),
	'NO_WAITING_UNAPPROVED_IMAGE'	=> 'Няма изображения чакащи одобрение.',
));
