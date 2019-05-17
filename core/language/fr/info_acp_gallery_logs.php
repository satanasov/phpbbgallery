<?php
/**
*
* phpBB Gallery. An extension for the phpBB Forum Software package.
* French translation by pokyto aka le.poke http://www.lestontonsfraggers.com (inspired by darky http://www.foruminfopc.fr/ and http://www.phpbb-fr.com/) & Galixte (http://www.galixte.com)
*
* @copyright (c) 2012 nickvergessen <http://www.flying-bits.org/> - 2018 Stanislav Atanasov <http://www.anavaro.com>
* @license GNU General Public License, version 2 (GPL-2.0-only)
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

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_LOG_GALLERY_MOD'						=> 'Journal des modérateurs',
	'ACP_LOG_GALLERY_MOD_EXP'					=> 'Journal des modérateurs',
	'ACP_LOG_GALLERY_ADM'						=> 'Journal des administrateurs',
	'ACP_LOG_GALLERY_ADM_EXP'					=> 'Journal des administrateurs',
	'ACP_LOG_GALLERY_SYSTEM'					=> 'Journal système',
	'ACP_LOG_GALLERY_SYSTEM_EXP'				=> 'Journal système',
	'LOG_GALLERY_SHOW_LOGS'						=> 'Afficher seulement',

	'SORT_USER_ID'							=> 'ID utilisateur',

	'LOG_ALBUM_ADD'							=> '<strong>Nouvel album créé</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUM'					=> '<strong>Album supprimé</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUMS'					=> '<strong>Album et ses sous-albums supprimés</strong><br />» %s',
	'LOG_ALBUM_DEL_MOVE_ALBUMS'				=> '<strong>Album supprimé et ses sous-albums déplacés</strong> vers %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES'				=> '<strong>Album supprimé et ses images déplacées </strong> vers %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_ALBUMS'		=> '<strong>Album supprimé, ses sous-albums et images déplacés</strong> vers %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_MOVE_ALBUMS'	=> '<strong>Album supprimé et images déplacées</strong> de %1$s <strong>et sous-albums</strong> vers %2$s<br />» %3$s',
	'LOG_ALBUM_DEL_IMAGES'					=> '<strong>Album et ses images supprimés</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_ALBUMS'			=> '<strong>Album, ses sous-albums et images supprimés</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_MOVE_ALBUMS'		=> '<strong>Album et ses images supprimé, sous-albums déplacés</strong> vers %1$s<br />» %2$s',
	'LOG_ALBUM_EDIT'						=> '<strong>Détails de l’album modifiés</strong><br />» %s',
	'LOG_ALBUM_MOVE_DOWN'					=> '<strong>Album déplacé</strong> %1$s <strong>en-dessous de</strong> %2$s',
	'LOG_ALBUM_MOVE_UP'						=> '<strong>Album déplacé</strong> %1$s <strong>au-dessus de</strong> %2$s',
	'LOG_ALBUM_SYNC'						=> '<strong>Album resynchronisé</strong><br />» %s',

	'LOG_CLEAR_GALLERY'					=> 'Journal de la galerie effacé',

	'LOG_GALLERY_APPROVED'				=> '<strong>Image validée</strong><br />» %s',
	'LOG_GALLERY_COMMENT_DELETED'		=> '<strong>Commentaire supprimé</strong><br />» %s',
	'LOG_GALLERY_COMMENT_EDITED'		=> '<strong>Commentaire modifié</strong><br />» %s',
	'LOG_GALLERY_DELETED'				=> '<strong>Image supprimée</strong><br />» %s',
	'LOG_GALLERY_EDITED'				=> '<strong>Image modifiée</strong><br />» %s',
	'LOG_GALLERY_LOCKED'				=> '<strong>Image verrouillée</strong><br />» %s',
	'LOG_GALLERY_MOVED'					=> '<strong>Image déplacée</strong><br />» from %1$s to %2$s',
	'LOG_GALLERY_REPORT_CLOSED'			=> '<strong>Rapport fermé</strong><br />» %s',
	'LOG_GALLERY_REPORT_DELETED'		=> '<strong>Rapport supprimé</strong><br />» %s',
	'LOG_GALLERY_REPORT_OPENED'			=> '<strong>Rapport ré-ouvert</strong><br />» %s',
	'LOG_GALLERY_UNAPPROVED'			=> '<strong>Image non approuvée</strong><br />» %s',
	'LOG_GALLERY_DISAPPROVED'			=> '<strong>Image refusée</strong><br />» %s',

	'LOGVIEW_VIEWALBUM'					=> 'Voir l’album',
	'LOGVIEW_VIEWIMAGE'					=> 'Voir l’image',
));
