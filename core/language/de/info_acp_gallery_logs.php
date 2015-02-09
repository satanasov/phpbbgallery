<?php
/**
*
* info_acp_gallery_logs [Deutsch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Übersetzt von franki (http://dieahnen.de/ahnenforum/)
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
	'ACP_LOG_GALLERY_MOD'					=> 'Moderator-Protokoll',
	'ACP_LOG_GALLERY_MOD_EXP'				=> 'Moderator-Protokoll',
	'ACP_LOG_GALLERY_ADM'					=> 'Administrator-Protokoll',
	'ACP_LOG_GALLERY_ADM_EXP'				=> 'Administrator-Protokoll',
	'ACP_LOG_GALLERY_SYSTEM'				=> 'System-Protokoll',
	'ACP_LOG_GALLERY_SYSTEM_EXP'			=> 'System-Protokoll',
	'LOG_GALLERY_SHOW_LOGS'					=> 'Nur anzeigen',

	'SORT_USER_ID'							=> 'Benutzer-ID',

	'LOG_ALBUM_ADD'							=> '<strong>Alben erstellt</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUM'					=> '<strong>Alben gelöscht</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUMS'					=> '<strong>Alben mit Subalben gelöscht</strong><br />» %s',
	'LOG_ALBUM_DEL_MOVE_ALBUMS'				=> '<strong>Alben gelöscht und Subalben verschoben</strong> nach %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES'				=> '<strong>Alben gelöscht und Bilder verschoben</strong> nach %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_ALBUMS'		=> '<strong>Alben mit Subalben gelöscht und Bildern verschoben</strong> nach %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_MOVE_ALBUMS'	=> '<strong>Alben gelöscht, Bilder verschoben</strong> nach %1$s <strong>und Subalben</strong> nach %s<br />» %2$s',
	'LOG_ALBUM_DEL_IMAGES'					=> '<strong>Alben mit Bildern gelöscht</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_ALBUMS'			=> '<strong>Alben mit Bildern und Subalben gelöscht</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_MOVE_ALBUMS'		=> '<strong>Alben mit Bildern gelöscht, Subalben verschoben</strong> nach %1$s<br />» %2$s',
	'LOG_ALBUM_EDIT'						=> '<strong>Album-Details geändert</strong><br />» %s',
	'LOG_ALBUM_MOVE_DOWN'					=> '<strong>Alben verschoben</strong> %1$s <strong>unter</strong> %2$s',
	'LOG_ALBUM_MOVE_UP'						=> '<strong>Alben verschoben</strong> %1$s <strong>über</strong> %2$s',
	'LOG_ALBUM_SYNC'						=> '<strong>Alben resynchronisiert</strong><br />» %s',

	'LOG_CLEAR_GALLERY'						=> 'Gallery-Protokoll gelöscht',

	'LOG_GALLERY_APPROVED'					=> '<strong>Bild freigeschalten</strong><br />» %s',
	'LOG_GALLERY_COMMENT_DELETED'			=> '<strong>Kommentar gelöscht</strong><br />» %s',
	'LOG_GALLERY_COMMENT_EDITED'			=> '<strong>Kommentar bearbeitet</strong><br />» %s',
	'LOG_GALLERY_DELETED'					=> '<strong>Bild gelöscht</strong><br />» %s',
	'LOG_GALLERY_EDITED'					=> '<strong>Bild bearbeitet</strong><br />» %s',
	'LOG_GALLERY_LOCKED'					=> '<strong>Bild gesperrt</strong><br />» %s',
	'LOG_GALLERY_MOVED'						=> '<strong>Bild verschoben</strong><br />» von %1$s nach %2$s',
	'LOG_GALLERY_REPORT_CLOSED'				=> '<strong>Meldung geschlossen</strong><br />» %s',
	'LOG_GALLERY_REPORT_DELETED'			=> '<strong>Meldung gelöscht</strong><br />» %s',
	'LOG_GALLERY_REPORT_OPENED'				=> '<strong>Meldung wieder geöffnet</strong><br />» %s',
	'LOG_GALLERY_UNAPPROVED'				=> '<strong>Erneute Freischaltung erzwungen</strong><br />» %s',
	'LOG_GALLERY_DISAPPROVED'				=> '<strong>Erneute Freischaltung erzwungen</strong><br />» %s',

	'LOGVIEW_VIEWALBUM'						=> 'Album anzeigen',
	'LOGVIEW_VIEWIMAGE'						=> 'Bild anzeigen',
));
