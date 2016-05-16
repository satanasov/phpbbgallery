<?php
/**
*
* info_acp_gallery_logs [Dutch]
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
	'ACP_LOG_GALLERY_MOD'						=> 'Moderatorlog',
	'ACP_LOG_GALLERY_MOD_EXP'						=> 'Moderatorlog',
	'ACP_LOG_GALLERY_ADM'						=> 'Beheerderslog',
	'ACP_LOG_GALLERY_ADM_EXP'						=> 'Beheerderslog',
	'ACP_LOG_GALLERY_SYSTEM'						=> 'Systeemlog',
	'ACP_LOG_GALLERY_SYSTEM_EXP'						=> 'Systeemlog',
	'LOG_GALLERY_SHOW_LOGS'						=> 'Toon alleen',

	'SORT_USER_ID'							=> 'Gebruikers-ID',

	'LOG_ALBUM_ADD'							=> '<strong>Nieuw album aangemaakt</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUM'					=> '<strong>Album verwijderd</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUMS'					=> '<strong>Album met subalbums verwijderd</strong><br />» %s',
	'LOG_ALBUM_DEL_MOVE_ALBUMS'				=> '<strong>Album verwijderd en subalbums verplaatst</strong> naar %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES'				=> '<strong>Album verwijderd en afbeeldingen verplaatst</strong> naar %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_ALBUMS'		=> '<strong>Album met subalbums verwijderd, afbeeldingen verplaatst</strong> naar %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_MOVE_ALBUMS'	=> '<strong>Album verwijderd, afbeeldingen verplaatst</strong> naar %1$s <strong>en subalbums</strong> naar %2$s<br />» %3$s',
	'LOG_ALBUM_DEL_IMAGES'					=> '<strong>Album met zijn afbeeldingen verwijderd</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_ALBUMS'			=> '<strong>Album verwijderd, samen met zijn afbeeldingen en subalbums</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_MOVE_ALBUMS'		=> '<strong>Album met afbeeldingen verwijderd, subalbums verplaatst</strong> naar %1$s<br />» %2$s',
	'LOG_ALBUM_EDIT'						=> '<strong>Album details gewijzigd</strong><br />» %s',
	'LOG_ALBUM_MOVE_DOWN'					=> '<strong>Album verplaatst</strong> %1$s <strong>beneden</strong> %2$s',
	'LOG_ALBUM_MOVE_UP'						=> '<strong>Album verplaatst</strong> %1$s <strong>boven</strong> %2$s',
	'LOG_ALBUM_SYNC'						=> '<strong>Album gesynchroniseerd</strong><br />» %s',

	'LOG_CLEAR_GALLERY'					=> 'Galerij log geleegd',

	'LOG_GALLERY_APPROVED'				=> '<strong>Afbeelding goedgekeurd</strong><br />» %s',
	'LOG_GALLERY_COMMENT_DELETED'		=> '<strong>Reactie verwijderd</strong><br />» %s',
	'LOG_GALLERY_COMMENT_EDITED'		=> '<strong>Reactie gewijzigd</strong><br />» %s',
	'LOG_GALLERY_DELETED'				=> '<strong>Afbeelding verwijderd</strong><br />» %s',
	'LOG_GALLERY_EDITED'				=> '<strong>Afbeelding gewijzigd</strong><br />» %s',
	'LOG_GALLERY_LOCKED'				=> '<strong>Afbeelding gesloten</strong><br />» %s',
	'LOG_GALLERY_MOVED'					=> '<strong>Afbeelding verplaatst</strong><br />» van %1$s naar %2$s',
	'LOG_GALLERY_REPORT_CLOSED'			=> '<strong>Melding gesloten</strong><br />» %s',
	'LOG_GALLERY_REPORT_DELETED'		=> '<strong>Melding verwijderd</strong><br />» %s',
	'LOG_GALLERY_REPORT_OPENED'			=> '<strong>Melding heropend</strong><br />» %s',
	'LOG_GALLERY_UNAPPROVED'			=> '<strong>Afbeelding wachtend op goedkeuring</strong><br />» %s',
	'LOG_GALLERY_DISAPPROVED'			=> '<strong>Afbeelding afgekeurd</strong><br />» %s',

	'LOGVIEW_VIEWALBUM'					=> 'Ga naar album',
	'LOGVIEW_VIEWIMAGE'					=> 'Ga naar afbeelding',
));
