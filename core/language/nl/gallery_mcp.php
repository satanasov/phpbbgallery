<?php
/**
*
* gallery_mcp [Dutch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* [Dutch] translated by Dutch Translators (https://github.com/dutch-translators)
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
	'CHOOSE_ACTION'					=> 'Selecteer de gewenste actie',

	'GALLERY_MCP_MAIN'				=> 'Begin',
	'GALLERY_MCP_OVERVIEW'			=> 'Overzicht',
	'GALLERY_MCP_QUEUE'				=> 'Wachtrij',
	'GALLERY_MCP_QUEUE_DETAIL'		=> 'Afbeelding details',
	'GALLERY_MCP_REPORTED'			=> 'Gemelde afbeeldingen',
	'GALLERY_MCP_REPO_DONE'			=> 'Gesloten meldingen',
	'GALLERY_MCP_REPO_OPEN'			=> 'Open meldingen',
	'GALLERY_MCP_REPO_DETAIL'		=> 'Melding details',
	'GALLERY_MCP_UNAPPROVED'		=> 'Afbeeldingen wachtend op goedkeuring',
	'GALLERY_MCP_APPROVED'			=> 'Goedgekeurde afbeeldingen',
	'GALLERY_MCP_LOCKED'			=> 'Gesloten afbeelding',
	'GALLERY_MCP_VIEWALBUM'			=> 'Ga naar album',
	'GALLERY_MCP_ALBUM_OVERVIEW'	=> 'Modereer album',

	'IMAGE_REPORTED'				=> 'De afbeelding is gemeld.',
	'IMAGE_UNAPPROVED'				=> 'De afbeelding wacht op goedkeuring.',

	'MODERATE_ALBUM'				=> 'Modereer album',

	'LATEST_IMAGES_REPORTED'		=> 'Laatste 5 meldingen',
	'LATEST_IMAGES_UNAPPROVED'		=> 'Laatste 5 afbeeldingen wachtend op goedkeuring',

	'QUEUE_A_APPROVE'				=> 'Afbeelding goedkeuren',
	'QUEUE_A_APPROVE2'				=> 'Afbeelding goedkeuren?',
	'QUEUE_A_APPROVE2_CONFIRM'		=> 'Weet je zeker dat je deze afbeelding wilt goedkeuren?',
	'QUEUE_A_DELETE'				=> 'Afbeelding verwijderen',
	'QUEUE_A_DELETE2'				=> 'Afbeelding verwijderen?',
	'QUEUE_A_DELETE2_CONFIRM'		=> 'Weet je zeker dat je deze afbeelding wilt verwijderen?',
	'QUEUE_A_LOCK'					=> 'Afbeelding sluiten',
	'QUEUE_A_LOCK2'					=> 'Afbeelding sluiten?',
	'QUEUE_A_LOCK2_CONFIRM'			=> 'Weet je zeker dat je deze afbeelding wilt sluiten?',
	'QUEUE_A_MOVE'					=> 'Verplaats afbeelding',
	'QUEUE_A_UNAPPROVE'				=> 'Afbeelding afkeuren',
	'QUEUE_A_UNAPPROVE2'			=> 'Afbeelding afkeuren?',
	'QUEUE_A_UNAPPROVE2_CONFIRM'	=> 'Weet je zeker dat je deze afbeelding wilt afkeuren?',

	'QUEUE_STATUS_0'				=> 'De afbeelding wacht op goedkeuring.',
	'QUEUE_STATUS_1'				=> 'De afbeelding is goedgekeurd.',
	'QUEUE_STATUS_2'				=> 'De afbeelding is gesloten.',

	'QUEUES_A_APPROVE'				=> 'Afbeeldingen goedkeuren',
	'QUEUES_A_APPROVE2'				=> 'Afbeeldingen goedkeuren?',
	'QUEUES_A_APPROVE2_CONFIRM'		=> 'Weet je zeker dat je deze afbeeldingen wilt goedkeuren?',
	'QUEUES_A_DELETE'				=> 'Afbeeldingen verwijderen',
	'QUEUES_A_DELETE2'				=> 'Afbeeldingen verwijderen?',
	'QUEUES_A_DELETE2_CONFIRM'		=> 'Weet je zeker dat je deze afbeeldingen wilt verwijderen?',
	'QUEUES_A_LOCK'					=> 'Afbeeldingen sluiten',
	'QUEUES_A_LOCK2'				=> 'Afbeeldingen sluiten?',
	'QUEUES_A_LOCK2_CONFIRM'		=> 'Weet je zeker dat je deze afbeeldingen wilt sluiten?',
	'QUEUES_A_MOVE'					=> 'Verplaats afbeeldingen',
	'QUEUES_A_UNAPPROVE'			=> 'Afbeeldingen afkeuren',
	'QUEUES_A_UNAPPROVE2'			=> 'Afbeeldingen afkeuren?',
	'QUEUES_A_UNAPPROVE2_CONFIRM'	=> 'Weet je zeker dat je deze afbeeldingen wilt afkeuren?',
	'QUEUES_A_DISAPPROVE2_CONFIRM'	=> 'Weet je zeker dat je deze afbeeldingen wilt afkeuren?',

	'REPORT_A_CLOSE'				=> 'Sluit melding',
	'REPORT_A_CLOSE2'				=> 'Melding sluiten?',
	'REPORT_A_CLOSE2_CONFIRM'		=> 'Weet je zeker dat je deze melding wilt sluiten?',
	'REPORT_A_DELETE'				=> 'Verwijderm elding',
	'REPORT_A_DELETE2'				=> 'Melding verwijderen?',
	'REPORT_A_DELETE2_CONFIRM'		=> 'Weet je zeker dat je deze melding wilt verwijderen?',
	'REPORT_A_OPEN'					=> 'Heropen melding',
	'REPORT_A_OPEN2'				=> 'Melding heropenen?',
	'REPORT_A_OPEN2_CONFIRM'		=> 'Weet je zeker dat je deze melding wilt heropenen?',

	'REPORT_NOT_FOUND'				=> 'De melding kan niet gevonden worden.',
	'REPORT_STATUS_1'				=> 'De melding moet bekeken worden.',
	'REPORT_STATUS_2'				=> 'De melding is gelosten.',

	'REPORTS_A_CLOSE'				=> 'Sluit meldingen',
	'REPORTS_A_CLOSE2'				=> 'Meldingen sluiten?',
	'REPORTS_A_CLOSE2_CONFIRM'		=> 'Weet je zeker dat je deze meldingen wilt sluiten?',
	'REPORTS_A_DELETE'				=> 'Verwijder meldingen',
	'REPORTS_A_DELETE2'				=> 'Meldingen verwijderen?',
	'REPORTS_A_DELETE2_CONFIRM'		=> 'Weet je zeker dat je deze meldingen wilt verwijderen?',
	'REPORTS_A_OPEN'				=> 'Heropenen meldingen',
	'REPORTS_A_OPEN2'				=> 'Meldingen heropenen?',
	'REPORTS_A_OPEN2_CONFIRM'		=> 'Weet je zeker dat je deze meldingen wilt heropenen?',

	'REPORT_MOD'					=> 'Bewerkt door',
	'REPORTED_IMAGES'				=> 'Gemelde afbeeldingen',
	'REPORTER'						=> 'Gemeld door',
	'REPORTER_AND_ALBUM'			=> 'Reporter & Album',

	'WAITING_APPROVED_IMAGE'		=> array(
		0			=> 'Geen afbeeldingen wachtend op goekeuring.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> afbeelding goedgekeurd.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> afbeeldingen goedgekeurd.',
	),
	'WAITING_DISPPROVED_IMAGE'		=> array(
		0			=> 'Geen afgekeurde afbeeldingen.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> afbeelding afgekleurd.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> afbeeldingen afgekeurd.',
	),
	'WAITING_LOCKED_IMAGE'			=> array(
		0			=> 'Geen gesloten afbeeldingen.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> afbeelding gesloten.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> afbeeldingen gesloten.',
	),
	'WAITING_REPORTED_DONE'			=> array(
		0			=> 'Geen meldingen bekeken.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> melding bekeken.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> meldingen bekeken.',
	),
	'WAITING_REPORTED_IMAGE'		=> array(
		0			=> 'Geen meldingen om te bekijken.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> melding om te bekijken.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> meldingen om te bekijken.',
	),
	'WAITING_UNAPPROVED_IMAGE'		=> array(
		0			=> 'Geen afbeeldingen die wachten op goedkeuring.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> afbeelding die wacht op goedkeuring.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> afbeeldingen die wachten op goedkeuring.',
	),
	'DELETED_IMAGES'		=> array(
		0			=> 'Geen verwijderde afbeeldingen.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> afbeelding verwijderd.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> afbeeldingen verwijderd.',
	),
	'MOVED_IMAGES'		=> array(
		0			=> 'Er zijn geen afbeeldingen verplaatst.',
		1			=> 'In totaal is er <span style="font-weight: bold;">1</span> afbeelding verplaatst.',
		2			=> 'In totaal zijn er <span style="font-weight: bold;">%s</span> afbeeldingen verplaatst.',
	),
	'NO_WAITING_UNAPPROVED_IMAGE'	=> 'Geen afbeeldingen wachtend op goekeuring.',
));
