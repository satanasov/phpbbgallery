<?php
/**
*
* @package phpBB Gallery - UCP Extension [French]
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
	'ACCESS_CONTROL_ALL'			=> 'Tout le monde',
	'ACCESS_CONTROL_REGISTERED'		=> 'Utilisateurs enregistrés',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Utilisateurs enregistrés, sauf vos ignorés',
	'ACCESS_CONTROL_FRIENDS'		=> 'Seulement vos amis',
	'ACCESS_CONTROL_SPECIAL_FRIENDS'		=> 'Seulement vos favoris',
	'ALBUMS'						=> 'Albums',
	'ALBUM_ACCESS'					=> 'Autoriser l’accès pour',
	'ALBUM_ACCESS_EXPLAIN'			=> 'Vous pouvez utiliser %1$sla liste de vos amis et ignorés%2$s, pour contrôler l’accès à votre album personnel. Cependant, les <strong>modérateurs</strong> peuvent <strong>toujours</strong> avoir accès à l’album.',
	'ALBUM_DESC'					=> 'Description de l’album',
	'ALBUM_NAME'					=> 'Titre de l’album',
	'ALBUM_PARENT'					=> 'Album parent',
	'ATTACHED_SUBALBUMS'			=> 'Sous-albums ajoutés',

	'CREATE_PERSONAL_ALBUM'			=> 'Créer un album personnel',
	'CREATE_SUBALBUM'				=> 'Créer un sous-album',
	'CREATE_SUBALBUM_EXP'			=> 'Vous pouvez ajouter un nouveau sous-album à votre galerie personnelle.',
	'CREATED_SUBALBUM'				=> 'Sous-album crée avec succès.',

	'DELETE_ALBUM'					=> 'Supprimer l’album',
	'DELETE_ALBUM_CONFIRM'			=> 'Supprimer l’album, avec tous les sous-albums et toutes les images qu’il contient?',
	'DELETED_ALBUMS'				=> 'Album supprimé avec succès.',

	'EDIT'							=> 'Éditer',
	'EDIT_ALBUM'					=> 'Éditer l’album',
	'EDIT_SUBALBUM'					=> 'Éditer le sous-album',
	'EDIT_SUBALBUM_EXP'				=> 'Vous pouvez éditer ici vos albums.',
	'EDITED_SUBALBUM'				=> 'Album édité avec succès.',

	'GOTO'							=> 'Voir l’album',

	'MANAGE_SUBALBUMS'				=> 'Gérer vos sous-albums',
	'MISSING_ALBUM_NAME'			=> 'Merci de saisir un titre d’album.',

	'NEED_INITIALISE'				=> 'Vous n’avez pas encore d’album personnel.',
	'NO_ALBUM_STEALING'				=> 'Vous n’êtes pas autorisé à gérer les albums d’autres utilisateurs.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'Vous avez ajouté le maximum de sous-albums à votre album personnel.',
	'NO_PARENT_ALBUM'				=> '&laquo;-- Aucun parent',
	'NO_PERSALBUM_ALLOWED'			=> 'Vous n’avez pas les permissions nécessaires pour créer votre album personnel.',
	'NO_PERSONAL_ALBUM'				=> 'Vous n’avez pas encore d’album personnel. Vous pouvez créer ici votre album personnel, avec plusieurs sous-albums.<br />Dans les albums personnels, seul le propriétaire peut charger des images.',
	'NO_SUBALBUMS'					=> 'Aucun album ajouté',
	'NO_SUBSCRIPTIONS'				=> 'Vous n’êtes abonné à aucune image.',

	'PARSE_BBCODE'					=> 'Autoriser les BBCodes',
	'PARSE_SMILIES'					=> 'Autoriser les Smileys',
	'PARSE_URLS'					=> 'Autoriser les liens',
	'PERSONAL_ALBUM'				=> 'Album personnel',

	'UNSUBSCRIBE'					=> 'Arrêter de surveiller',
	'USER_ALLOW_COMMENTS'			=> 'Autoriser les utilisateurs à commenter vos images',

	'YOUR_SUBSCRIPTIONS'			=> 'Vous pouvez voir ici les albums et les images auxquels vous êtes abonné.',

	'WATCH_CHANGED'					=> 'Paramètres enregistrés',
	'WATCH_COM'						=> 'S’abonner par défaut aux images commentées',
	'WATCH_NOTE'					=> 'Cette option affecte seulement les nouvelles images. Toutes les autres images doivent être ajoutées avec l’option « S’abonner à l’image ».',
	'WATCH_OWN'						=> 'Subscribe own images by default',
));
