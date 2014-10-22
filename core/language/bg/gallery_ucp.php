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
	'ACCESS_CONTROL_ALL'			=> 'Everyone',
	'ACCESS_CONTROL_REGISTERED'		=> 'Registered users',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Registered users, except your foes',
	'ACCESS_CONTROL_FRIENDS'		=> 'Only your friends',
	'ALBUMS'						=> 'Albums',
	'ALBUM_ACCESS'					=> 'Allow access for',
	'ALBUM_ACCESS_EXPLAIN'			=> 'You can use your %1$sFriends and Foes lists%2$s to control access to the album. However <strong>moderators</strong> can <strong>always</strong> access the album.',
	'ALBUM_DESC'					=> 'Album Description',
	'ALBUM_NAME'					=> 'Album Name',
	'ALBUM_PARENT'					=> 'Parent Album',
	'ATTACHED_SUBALBUMS'			=> 'Attached subalbums',

	'CREATE_PERSONAL_ALBUM'			=> 'Create personal album',
	'CREATE_SUBALBUM'				=> 'Create subalbum',
	'CREATE_SUBALBUM_EXP'			=> 'You may attach a new subalbum to your personal gallery.',
	'CREATED_SUBALBUM'				=> 'Subalbum successful created',

	'DELETE_ALBUM'					=> 'Delete Album',
	'DELETE_ALBUM_CONFIRM'			=> 'Delete Album, with all attached subalbums and images?',
	'DELETED_ALBUMS'				=> 'Albums successful deleted',

	'EDIT'							=> 'Edit',
	'EDIT_ALBUM'					=> 'Edit album',
	'EDIT_SUBALBUM'					=> 'Edit Subalbum',
	'EDIT_SUBALBUM_EXP'				=> 'You can edit your albums here.',
	'EDITED_SUBALBUM'				=> 'Album successful edited',

	'GOTO'							=> 'Go To',

	'MANAGE_SUBALBUMS'				=> 'Manage your subalbums',
	'MISSING_ALBUM_NAME'			=> 'Please insert a name for the album',

	'NEED_INITIALISE'				=> 'You don’t have a personal album yet.',
	'NO_ALBUM_STEALING'				=> 'You are not allowed to manage the Album of other users.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'You added your maximum of subalbums to your personal album',
	'NO_PARENT_ALBUM'				=> '&laquo;-- no parent',
	'NO_PERSALBUM_ALLOWED'			=> 'You don’t have the permissions create your personal album',
	'NO_PERSONAL_ALBUM'				=> 'You don’t have a personal album yet. Here you can create your personal album, with some subalbums.<br />In personal albums only the owner can upload images.',
	'NO_SUBALBUMS'					=> 'No Albums attached',
	'NO_SUBSCRIPTIONS'				=> 'You didn’t subscribe to any image.',

	'PARSE_BBCODE'					=> 'Parse BBCode',
	'PARSE_SMILIES'					=> 'Parse smilies',
	'PARSE_URLS'					=> 'Parse links',
	'PERSONAL_ALBUM'				=> 'Personal album',

	'UNSUBSCRIBE'					=> 'stop watching',
	'USER_ALLOW_COMMENTS'			=> 'Allow users to comment your images',

	'YOUR_SUBSCRIPTIONS'			=> 'Here you see albums and images you get notified on.',

	'WATCH_CHANGED'					=> 'Settings stored',
	'WATCH_COM'						=> 'Subscribe commented images by default',
	'WATCH_NOTE'					=> 'This option only affects on new images. All other images need to be added by the “subscribe image“ option.',
	'WATCH_OWN'						=> 'Subscribe own images by default',
));

?>