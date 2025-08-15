<?php
/**
 * phpBB Gallery - ACP Core Extension [French Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator pokyto (aka le.poke) <https://www.lestontonsfraggers.com>, inspired by darky <https://www.foruminfopc.fr/> and the phpBB-fr.com Team
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
	$lang = [];
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
	'ACCESS_CONTROL_ALL'			=> 'Tout le monde',
	'ACCESS_CONTROL_REGISTERED'		=> 'Utilisateurs enregistrés',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Utilisateurs enregistrés, sauf ceux ignorés',
	'ACCESS_CONTROL_FRIENDS'		=> 'Seulement ses amis',
	'ACCESS_CONTROL_SPECIAL_FRIENDS'		=> 'Seulement ses favoris',
	'ALBUMS'						=> 'Albums',
	'ALBUM_ACCESS'					=> 'Autoriser l’accès pour',
	'ALBUM_ACCESS_EXPLAIN'			=> 'Permet d’utiliser %1$sla liste de ses amis et ignorés%2$s, pour contrôler l’accès à votre album personnel. Cependant, les <strong>modérateurs</strong> peuvent <strong>toujours</strong> avoir accès à l’album.',
	'ALBUM_DESC'					=> 'Description de l’album',
	'ALBUM_NAME'					=> 'Titre de l’album',
	'ALBUM_PARENT'					=> 'Album parent',
	'ATTACHED_SUBALBUMS'			=> 'Sous-albums ajoutés.',

	'CREATE_PERSONAL_ALBUM'			=> 'Créer un album personnel',
	'CREATE_SUBALBUM'				=> 'Créer un sous-album',
	'CREATE_SUBALBUM_EXP'			=> 'Permet de créer un sous-album à sa galerie personnelle.',
	'CREATED_SUBALBUM'				=> 'Sous-album créé avec succès.',

	'DELETE_ALBUM'					=> 'Supprimer l’album',
	'DELETE_ALBUM_CONFIRM'			=> 'Confirmer la suppression de l’album ainsi que tous ses sous-albums et toutes les images contenues.',
	'DELETED_ALBUMS'				=> 'Album supprimé avec succès.',

	'EDIT'							=> 'Modifier',
	'EDIT_ALBUM'					=> 'Modifier l’album',
	'EDIT_SUBALBUM'					=> 'Modifier le sous-album',
	'EDIT_SUBALBUM_EXP'				=> 'Permet de modifier ses albums.',
	'EDITED_SUBALBUM'				=> 'Album modifié avec succès.',

	'GOTO'							=> 'Voir l’album',

	'MANAGE_SUBALBUMS'				=> 'Gérer ses sous-albums',
	'MISSING_ALBUM_NAME'			=> 'Merci de saisir un titre pour l’album.',

	'NEED_INITIALISE'				=> 'Aucun album personnel n’a été créé.',
	'NO_ALBUM_STEALING'				=> 'Il n’est pas autorisé de gérer les albums des autres utilisateurs.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'Le nombre maximum de sous-albums a été atteint dans l’album personnel.',
	'NO_PARENT_ALBUM'				=> '&laquo;-- Aucun parent',
	'NO_PERSALBUM_ALLOWED'			=> 'Il n’est pas autorisé de créer son album personnel.',
	'NO_PERSONAL_ALBUM'				=> 'Aucun album personnel n’a été créé. Il est possible de créer son album personnel, avec plusieurs ses sous-albums.<br />Dans les albums personnels, seul le propriétaire peut charger des images.',
	'NO_SUBALBUMS'					=> 'Aucun album ajouté',
	'NO_SUBSCRIPTIONS'				=> 'Aucun abonnement aux images n’a été configuré.',
	'NO_SUBSCRIPTIONS_ALBUM'		=> 'You are not subscribed to an album.',

	'PARSE_BBCODE'					=> 'Autoriser les BBCodes',
	'PARSE_SMILIES'					=> 'Autoriser les Smileys',
	'PARSE_URLS'					=> 'Autoriser les liens',
	'PERSONAL_ALBUM'				=> 'Album personnel',

	'UNSUBSCRIBE'					=> 'Arrêter de surveiller',
	'USER_ALLOW_COMMENTS'			=> 'Autoriser les utilisateurs à commenter ses images',

	'YOUR_SUBSCRIPTIONS'			=> 'Sur cette page il est possible de consulter les albums et images surveillés.',

	'WATCH_CHANGED'					=> 'Paramètres enregistrés',
	'WATCH_COM'						=> 'S’abonner par défaut aux images commentées',
	'WATCH_NOTE'					=> 'Cette option affecte seulement les nouvelles images. Toutes les autres images doivent être ajoutées avec l’option « S’abonner à l’image ».',
	'WATCH_OWN'						=> 'S’abonner par défaut à ses propres images',

	'RRC_ZEBRA'						=> 'Masquer les images des utilisateurs ignorés',
	'RRC_ZEBRA_EXPLAIN'				=> 'Permet de masquer les images provenant des utilisateurs ignorés dans les albums récents, aléatoires et les parties des commentaires affichées sur la page de l’index de la galerie photos.<br /><strong>Attention !</strong> Cette option ne masquera pas les images chargées par les utilisateurs ignorés dans les albums génériques (communs à tous les membres, voire publics).'
));
