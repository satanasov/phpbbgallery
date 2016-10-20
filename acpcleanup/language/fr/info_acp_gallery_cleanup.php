<?php
/**
*
* @package Gallery - ACP CleanUp Extension [French]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* @translator fr (c) pokyto aka le.poke http://www.lestontonsfraggers.com inspired by darky - http://www.foruminfopc.fr/ and Team http://www.phpbb-fr.com/
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

$lang = array_merge($lang, array(
	'ACP_GALLERY_CLEANUP'				=> 'Nettoyage de la galerie',

	'ACP_GALLERY_CLEANUP_EXPLAIN'	=> 'Ici vous pouvez nettoyer la Galerie.',

	'CLEAN_AUTHORS_DONE'			=> 'Images sans auteur valide effacées.',
	'CLEAN_CHANGED'					=> 'Auteur changé en “Invité“.',
	'CLEAN_COMMENTS_DONE'			=> 'Commentaires sans auteur valide effacés.',
	'CLEAN_ENTRIES_DONE'			=> 'Fichiers sans entrées de la base de données effacés.',
	'CLEAN_GALLERY'					=> 'Nettoyer la Galerie',
	'CLEAN_GALLERY_ABORT'			=> 'Nettoyage interrompu!',
	'CLEAN_NO_ACTION'				=> 'Aucune action terminée. Quelque chose a échoué!',
	'CLEAN_PERSONALS_DONE'			=> 'Albums personnels sans propriétaire valide supprimés.',
	'CLEAN_PERSONALS_BAD_DONE'		=> 'Albums personnels des utilisateurs séléctionnés effacés.',
	'CLEAN_PRUNE_DONE'				=> 'Images délestées avec succès.',
	'CLEAN_PRUNE_NO_PATTERN'		=> 'Aucun critère de recherche.',
	'CLEAN_SOURCES_DONE'			=> 'Images sans fichier supprimées.',

	'CONFIRM_CLEAN'					=> 'Cette étape ne peut être annulée!',
	'CONFIRM_CLEAN_AUTHORS'			=> 'Supprimer les images sans auteur valide?',
	'CONFIRM_CLEAN_COMMENTS'		=> 'Supprimer les commentaires sans auteur valide?',
	'CONFIRM_CLEAN_ENTRIES'			=> 'Supprimer les fichiers sans entrées de la base de données?',
	'CONFIRM_CLEAN_PERSONALS'		=> 'Supprimer les albums personnels sans propriétaire valide?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_PERSONALS_BAD'	=> 'Supprimer les albums personnels des utilisateurs sélectionnés?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_SOURCES'			=> 'Supprimer les images sans fichier ?',
	'CONFIRM_PRUNE'					=> 'Supprimer toutes les images, qui ont les conditions suivantes:<br /><br />%s<br />',

	'PRUNE'							=> 'Délester',
	'PRUNE_ALBUMS'					=> 'Délester les albums',
	'PRUNE_CHECK_OPTION'			=> 'Utiliser cette option.',
	'PRUNE_COMMENTS'				=> 'Moins de x commentaires',
	'PRUNE_PATTERN_ALBUM_ID'		=> 'L’image est dans l’un des albums suivants:<br />&raquo; <strong>%s</strong>',
	'PRUNE_PATTERN_COMMENTS'		=> 'L’image a au moins <strong>%d</strong> commentaires.',
	'PRUNE_PATTERN_RATES'			=> 'L’image a au moins <strong>%d</strong> notes.',
	'PRUNE_PATTERN_RATE_AVG'		=> 'L’image a une note moyenne, inférieure à <strong>%s</strong>.',
	'PRUNE_PATTERN_TIME'			=> 'L’image a été chargée avant le « <strong>%s</strong> ».',
	'PRUNE_PATTERN_USER_ID'			=> 'L’image a été chargée par l’un des utilisateurs suivants:<br />&raquo; <strong>%s</strong>',
	'PRUNE_RATINGS'					=> 'Moins de x notes',
	'PRUNE_RATING_AVG'				=> 'Note moyenne inférieure à',
	'PRUNE_RATING_AVG_EXP'			=> 'Délester seulement les images avec une note moyenne inférieure à “<samp>x.yz</samp>“.',
	'PRUNE_TIME'					=> 'Chargées avant',
	'PRUNE_TIME_EXP'				=> 'Délester seulement les images qui ont été chargées avant le “<samp>YYYY-MM-DD</samp>“.',
	'PRUNE_USERNAME'				=> 'Chargées par',
	'PRUNE_USERNAME_EXP'			=> 'Délester seulement les images de certains utilisateurs. Pour délester les images des « invités », cochez la case en-dessous de la zone de saisie.',

	//Log
	'LOG_CLEANUP_DELETE_FILES'		=> '%s images sans entrées de la base de données ont été effacées.',
	'LOG_CLEANUP_DELETE_ENTRIES'	=> '%s images sans fichiers ont été effacées.',
	'LOG_CLEANUP_DELETE_NO_AUTHOR'	=> '%s images sans auteurs valide ont été effacées.',
	'LOG_CLEANUP_COMMENT_DELETE_NO_AUTHOR'	=> '%s commentaires sans auteurs valide ont été effacées.',

	'MOVE_TO_IMPORT'	=> 'Déplacer les images dans le dossier d’importation',
	'MOVE_TO_USER'		=> 'Attribuer à l’utilisateur',
	'MOVE_TO_USER_EXP'	=> 'Les images et les commentaires seront attribués à l’utilisateur que vous avez défini. Si aucun n’est sélectionné - “Anonyme“ sera utilisé.',
	'CLEAN_USER_NOT_FOUND'	=> 'L’utilisateur sélectionné n’existe pas!',
));
