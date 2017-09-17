<?php
/**
*
* @package phpBB Gallery - Info ACP Gallery Logs Extension [French]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* @translator fr (c) pokyto aka le.poke http://www.lestontonsfraggers.com inspired by darky - http://www.foruminfopc.fr/ and Team http://www.phpbb-fr.com/
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
	'ACP_LOG_GALLERY_MOD'						=> 'Journal des modérateurs',
	'ACP_LOG_GALLERY_MOD_EXP'						=> 'Journal des modérateurs',
	'ACP_LOG_GALLERY_ADM'						=> 'Journal des administrateurs',
	'ACP_LOG_GALLERY_ADM_EXP'						=> 'Journal des administrateurs',
	'ACP_LOG_GALLERY_SYSTEM'						=> 'Journal système',
	'ACP_LOG_GALLERY_SYSTEM_EXP'						=> 'Journal système',
	'LOG_GALLERY_SHOW_LOGS'						=> 'Montrer seulement',

	'SORT_USER_ID'							=> 'ID Utilisateur',

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
	'LOG_ALBUM_EDIT'						=> '<strong>Détails de l’album édités</strong><br />» %s',
	'LOG_ALBUM_MOVE_DOWN'					=> '<strong>Album déplacé</strong> %1$s <strong>en-dessous de</strong> %2$s',
	'LOG_ALBUM_MOVE_UP'						=> '<strong>Album déplacé</strong> %1$s <strong>au-dessus de</strong> %2$s',
	'LOG_ALBUM_SYNC'						=> '<strong>Album resynchronisé</strong><br />» %s',

	'LOG_CLEAR_GALLERY'					=> 'Journal de la Galerie effacée',

	'LOG_GALLERY_APPROVED'				=> '<strong>Image validée</strong><br />» %s',
	'LOG_GALLERY_COMMENT_DELETED'		=> '<strong>Commentaire supprimé</strong><br />» %s',
	'LOG_GALLERY_COMMENT_EDITED'		=> '<strong>Commentaire édité</strong><br />» %s',
	'LOG_GALLERY_DELETED'				=> '<strong>Image supprimée</strong><br />» %s',
	'LOG_GALLERY_EDITED'				=> '<strong>Image éditée</strong><br />» %s',
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
