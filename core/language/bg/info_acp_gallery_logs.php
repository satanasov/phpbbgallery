<?php
/**
*
* info_acp_gallery_logs [English]
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
	'LOG_ALBUM_ADD'							=> '<strong>Created new album</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUM'					=> '<strong>Deleted album</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUMS'					=> '<strong>Deleted album and its subalbums</strong><br />» %s',
	'LOG_ALBUM_DEL_MOVE_ALBUMS'				=> '<strong>Deleted album and moved subalbums</strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES'				=> '<strong>Deleted album and moved images </strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_ALBUMS'		=> '<strong>Deleted album and its subalbums, moved images</strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_MOVE_ALBUMS'	=> '<strong>Deleted album, moved images</strong> to %1$s <strong>and subalbums</strong> to %2$s<br />» %3$s',
	'LOG_ALBUM_DEL_IMAGES'					=> '<strong>Deleted album and its images</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_ALBUMS'			=> '<strong>Deleted album, its images and subalbums</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_MOVE_ALBUMS'		=> '<strong>Deleted album and its images, moved subalbums</strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_EDIT'						=> '<strong>Edited album details</strong><br />» %s',
	'LOG_ALBUM_MOVE_DOWN'					=> '<strong>Moved album</strong> %1$s <strong>below</strong> %2$s',
	'LOG_ALBUM_MOVE_UP'						=> '<strong>Moved album</strong> %1$s <strong>above</strong> %2$s',
	'LOG_ALBUM_SYNC'						=> '<strong>Re-synchronised album</strong><br />» %s',

	'LOG_CLEAR_GALLERY'					=> 'Cleared Gallery log',

	'LOG_GALLERY_APPROVED'				=> '<strong>Approved image</strong><br />» %s',
	'LOG_GALLERY_COMMENT_DELETED'		=> '<strong>Deleted comment</strong><br />» %s',
	'LOG_GALLERY_COMMENT_EDITED'		=> '<strong>Edited comment</strong><br />» %s',
	'LOG_GALLERY_DELETED'				=> '<strong>Deleted image</strong><br />» %s',
	'LOG_GALLERY_EDITED'				=> '<strong>Edited image</strong><br />» %s',
	'LOG_GALLERY_LOCKED'				=> '<strong>Locked image</strong><br />» %s',
	'LOG_GALLERY_MOVED'					=> '<strong>Moved image</strong><br />» from %1$s to %2$s',
	'LOG_GALLERY_REPORT_CLOSED'			=> '<strong>Closed report</strong><br />» %s',
	'LOG_GALLERY_REPORT_DELETED'		=> '<strong>Deleted report</strong><br />» %s',
	'LOG_GALLERY_REPORT_OPENED'			=> '<strong>Reopened report</strong><br />» %s',
	'LOG_GALLERY_UNAPPROVED'			=> '<strong>Unapproved image</strong><br />» %s',

	'LOGVIEW_VIEWALBUM'					=> 'View album',
	'LOGVIEW_VIEWIMAGE'					=> 'View image',
));

?>