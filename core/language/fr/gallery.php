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
	'ADD_UPLOAD_FIELD'				=> 'Ajouter une autre image',
	'ALBUM'							=> 'Album',
	'ALBUM_IS_CATEGORY'				=> 'Vous êtes dans une catégorie d’albums.<br />Vous ne pouvez pas charger d’images dans cette catégorie.',
	'ALBUM_LOCKED'					=> 'Verrouillé',
	'ALBUM_NAME'					=> 'Nom de l’album',
	'ALBUM_NOT_EXIST'				=> 'Cet album n’existe pas.',
	'ALBUM_PERMISSIONS'				=> 'Permissions de l’album',
	'ALBUM_REACHED_QUOTA'			=> 'Cet album a atteint son quota d’images. Vous ne pouvez pas en charger plus.<br/>Merci de contacter un administrateur pour plus d’informations.',
	'ALBUM_UPLOAD_NEED_APPROVAL'		=> 'Vos images ont été chargées avec succès.<br /><br />La fonction de validation des images est activée, donc vos images doivent être validées par un(e) administrateur(trice) ou un(e) modérateur(trice), avant d’être publiées.',
	'ALBUM_UPLOAD_NEED_APPROVAL_ERROR'	=> 'Certaines images ont été chargées avec succès.<br /><br />La fonction de validation des images est activée, donc vos images doivent être validées par un(e) administrateur(trice) ou un(e) modérateur(trice), avant d’être publiées.<br /><br /><p class="error">%s</p>',
	'ALBUM_UPLOAD_SUCCESSFUL'		=> 'Vos images ont été chargées avec succès.',
	'ALBUM_UPLOAD_SUCCESSFUL_ERROR'	=> 'Certaines images ont été chargées avec succès.<br /><br /><span class="error">%s</span>',
	'ALBUMS_MARKED'					=> 'Tous les albums ont été marqués comme lus.',
	'ALL'							=> 'Tous',
	'ALL_IMAGES'					=> 'Toutes les images',
	'ALLOW_COMMENTS'				=> 'Autoriser les commentaires pour cette image.',
	'ALLOW_COMMENTS_ARY'			=> array(
		0	=> 'Autoriser les commentaires pour cette image.',
		2	=> 'Autoriser les commentaires pour ces images.',
	),
	'ALLOWED_FILETYPES'				=> 'Types de fichiers autorisés',
	'APPROVE'						=> 'Valider',
	'DISAPPROVE'					=> 'Refuser',
	'APPROVE_IMAGE'					=> 'Valider l’image',

	//@todo
	'ALBUM_COMMENT_CAN'			=> 'Vous <strong>pouvez</strong> publier des commentaires dans cet album.',
	'ALBUM_COMMENT_CANNOT'		=> 'Vous <strong>ne pouvez pas</strong> publier de commentaires dans cet album.',
	'ALBUM_DELETE_CAN'			=> 'Vous <strong>pouvez</strong> supprimer vos images de cet album.',
	'ALBUM_DELETE_CANNOT'		=> 'Vous <strong>ne pouvez pas</strong> supprimer vos images de cet album.',
	'ALBUM_EDIT_CAN'			=> 'Vous <strong>pouvez</strong> modifier vos images dans cet album.',
	'ALBUM_EDIT_CANNOT'			=> 'Vous <strong>ne pouvez pas</strong> modifier vos images dans cet album.',
	'ALBUM_RATE_CAN'			=> 'Vous <strong>pouvez</strong> noter les images de cet album.',
	'ALBUM_RATE_CANNOT'			=> 'Vous <strong>ne pouvez pas</strong> noter les images de cet album.',
	'ALBUM_UPLOAD_CAN'			=> 'Vous <strong>pouvez</strong> charger de nouvelles images dans cet album.',
	'ALBUM_UPLOAD_CANNOT'		=> 'Vous <strong>ne pouvez pas</strong> charger de nouvelles images dans cet album.',
	'ALBUM_VIEW_CAN'			=> 'Vous <strong>pouvez</strong> voir les images de cet album.',
	'ALBUM_VIEW_CANNOT'			=> 'Vous <strong>ne pouvez pas</strong> voir les images de cet album.',

	'BAD_UPLOAD_FILE_SIZE'			=> 'Le fichier envoyé est trop grand.',
	'BBCODES'						=> 'BBCodes',
	'BROWSING_ALBUM'				=> 'Utilisateurs parcourant cet album : %1$s',
	'BROWSING_ALBUM_GUEST'			=> 'Utilisateurs parcourant cet album : %1$s and %2$d guest',
	'BROWSING_ALBUM_GUESTS'			=> 'Utilisateurs parcourant cet album : %1$s and %2$d guests',

	'CHANGE_AUTHOR'					=> 'Modifier l’auteur',
	'CHANGE_IMAGE_STATUS'			=> 'Modifier le statut de l’image',
	'CLICK_RETURN_ALBUM'			=> 'Cliquez %sici%s pour retourner à l’album.',
	'CLICK_RETURN_IMAGE'			=> 'Cliquez %sici%s pour retourner à l’image.',
	'CLICK_RETURN_INDEX'			=> 'Cliquez %sici%s pour retourner à la page d’index.',
	'COMMENT'						=> 'Commentaire',
	'COMMENT_IMAGE'					=> 'Publier un commentaire sur une image de l’album %s',
	'COMMENT_LENGTH'				=> 'Saisissez votre commentaire ici, il ne peut pas contenir plus de <strong>%d</strong> caractères.',
	'COMMENT_ON'					=> 'Commentaire sur',
	'COMMENT_STORED'				=> 'Votre commentaire a été enregistré avec succès.',
	'COMMENT_TOO_LONG'				=> 'Votre commentaire est trop long.',
	'COMMENTS'						=> 'Commentaires',
	'CONTEST_COMMENTS_STARTS'		=> 'Les commentaires sur les images de ce concours sont autorisés à partir du %s.',
	'CONTEST_ENDED'					=> 'Ce concours s’est terminé le %s.',
	'CONTEST_ENDS'					=> 'Ce concours se termine le %s.',
	'CONTEST_RATING_STARTED'		=> 'Les votes du concours ont débuté le %s.',
	'CONTEST_RATING_STARTS'			=> 'Les votes du concours débutent le %s.',
	'CONTEST_RATING_ENDED'			=> 'Les votes du concours se sont terminés le %s.',
	'CONTEST_RATING_HIDDEN'			=> 'Cachée',
	'CONTEST_RESULT'				=> 'Concours',
	'CONTEST_RESULT_1'				=> 'Vainqueur',
	'CONTEST_RESULT_2'				=> 'Deuxième',
	'CONTEST_RESULT_3'				=> 'Troisième',
	'CONTEST_RESULT_HIDDEN'			=> 'La note de cette image est cachée, jusqu’à la fin du concours le %s.',
	'CONTEST_STARTED'				=> 'Le concours a débuté le %s.',
	'CONTEST_STARTS'				=> 'Le concours débute le %s.',
	'CONTEST_USERNAME'				=> '<strong>Concours</strong>',
	'CONTEST_USERNAME_LONG'			=> '<strong>Concours</strong> » Le nom d’utilisateur est caché, jusqu’à la fin du concours le %s.',
	'CONTEST_IMAGE_DESC'			=> '<strong>Concours</strong> » La description de l’image est cachée, jusqu’à la fin du concours le %s.',
	'CONTEST_WINNERS_OF'			=> 'Vainqueur du concours « %s ».',
	'CONTINUE'						=> 'Continuer',

	'DATABASE_NOT_UPTODATE'			=> 'Votre base de données n’est pas à la même version que vos fichiers. Merci de mettre à jour votre base de données.',
	'DELETE_COMMENT'				=> 'Supprimer le commentaire',
	'DELETE_COMMENT2'				=> 'Supprimer le commentaire ?',
	'DELETE_COMMENT2_CONFIRM'		=> 'Êtes-vous sûr de vouloir supprimer ce commentaire ?',
	'DELETE_IMAGE'					=> 'Supprimer',
	'DELETE_IMAGE2'					=> 'Supprimer l’image ?',
	'DELETE_IMAGE2_CONFIRM'			=> 'Êtes-vous sûr de vouloir supprimer cette image ?',
	'DELETED_COMMENT'				=> 'Commentaire supprimé',
	'DELETED_COMMENT_NOT'			=> 'Commentaire non supprimé',
	'DELETED_IMAGE'					=> 'Image supprimée',
	'DELETED_IMAGE_NOT'				=> 'Image non supprimée',
	'DESC_TOO_LONG'					=> 'Votre description est trop longue.',
	'DESCRIPTION_LENGTH'			=> 'Saisissez votre description ici. Elle ne peut pas contenir plus de <strong>%d</strong> caractères.',
	'DETAILS'						=> 'Détails',
	'DISALLOWED_EXTENSION'			=> 'L’extension de cette image n’est pas autorisée.',
	'DONT_RATE_IMAGE'				=> 'Ne pas noter l’image',

	'EDIT_COMMENT'					=> 'Modifier le commentaire',
	'EDIT_IMAGE'					=> 'Modifier',
	'IMAGE_EDITED_TIME_TOTAL'				=> 'Dernière modification par %s le %s ; modifié %d fois',
	'IMAGE_EDITED_TIMES_TOTAL'			=> 'Dernière modification par %s le %s ; modifié %d fois',

	'FILE'							=> 'Fichier',
	'FILE_SIZE'						=> 'Taille du fichier',
	'FILETYPE_MIMETYPE_MISMATCH'	=> 'Le type de fichier « <strong>%1$s</strong> » ne correspond pas au mime-type (%2$s).',
	'FILETYPES_GIF'					=> 'gif',
	'FILETYPES_JPG'					=> 'jpg',
	'FILETYPES_PNG'					=> 'png',
	'FILETYPES_WEBP'				=> 'webp',
	'FILETYPES_ZIP'					=> 'zip',

	'FULL_EDITOR_GALLERY'					=> 'Éditeur complet',

	'GALLERY_IMAGE'					=> 'Image',
	'GALLERY_IMAGES'				=> 'Images',
	'GALLERY_VIEWS'					=> 'Vues',

	'IGNORE_NOTUPTODATE_MESSAGE'		=> 'Rappelez-le moi dans une semaine',
	'IMAGE_ALREADY_REPORTED'			=> 'L’image a été déjà rapportée.',
	'IMAGE_BBCODE'						=> 'BBCode image',
	'IMAGE_COMMENTS_DISABLED'			=> 'Les commentaires de cette image sont désactivés.',
	'IMAGE_DAY'							=> '%.2f images par jour',
	'IMAGE_DESC'						=> 'Description de l’image',
	'IMAGE_HEIGHT'						=> 'Hauteur de l’image',
	'IMAGE_INSERTED'					=> 'Image insérée',
	'IMAGE_LOCKED'						=> 'Désolé, cette image est verrouillée. Vous ne pouvez pas publier de commentaires sur cette image.',
	'IMAGE_NAME'						=> 'Nom de l’image',
	'IMAGE_NOT_EXIST'					=> 'Cette image n’existe pas.',
	'IMAGE_PCT'							=> '%.2f%% de toutes les images',
	'IMAGE_STATUS'						=> 'Statut',
	'IMAGE_URL'							=> 'Lien',
	'IMAGE_VIEWS'						=> 'Vues',
	'IMAGE_WIDTH'						=> 'Largeur de l’image',
	'IMAGES_REPORTED_SUCCESSFULLY'		=> 'L’image a été rapportée avec succès.',
	'IMAGES_UPDATED_SUCCESSFULLY'		=> 'Les informations de votre image ont été mises à jour avec succès.',
	'INSERT_IMAGE_POST'					=> 'Insérer l’image dans le message',
	'INVALID_USERNAME'					=> 'Votre nom d’utilisateur est invalide.',
	'INVALID_IMAGE'				    	=> 'Image non valide',
	'FILE_DISALLOWED_EXTENSION'	    	=> 'L’extension du fichier n’est pas autorisée.',
	'FILE_WRONG_FILESIZE'		    	=> 'Taille du fichier incorrecte.',

	'LAST_COMMENT'					=> 'Dernier commentaire',
	'LAST_IMAGE'					=> 'Dernière image',
	'LAST_IMAGE_BY'					=> 'Dernière image par',
	'LOGIN_EXPLAIN_UPLOAD'			=> 'Vous devez être enregistré et connecté pour pouvoir charger des images dans cette galerie.',

	'MARK_ALBUMS_READ'				=> 'Marquer les albums comme lus',
	'MAX_DIMENSIONS'				=> 'Dimensions maximales',
	'MAX_FILE_SIZE'					=> 'Taille maximale des fichiers',
	'MAX_HEIGHT'					=> 'Hauteur maximale de l’image',
	'MAX_WIDTH'						=> 'Largeur maximale de l’image',
	'MISSING_COMMENT'				=> 'Aucun message saisi.',
	'MISSING_IMAGE_NAME'			=> 'Vous devez spécifier un nom lorsque vous modifiez une image.',
	'MISSING_MODE'					=> 'Aucun mode sélectionné',
	'MISSING_REPORT_REASON'			=> 'Vous devez mentionner une raison, pour rapporter une image.',
	'MISSING_SLIDESHOW_PLUGIN'		=> 'Aucun plugin de diaporama trouvé. Contactez l’administrateur du forum.',
	'MISSING_SUBMODE'				=> 'Aucun sous-mode sélectionné',
	'MISSING_USERNAME'				=> 'Aucun nom d’utilisateur sélectionné',
	'MOVE_TO_ALBUM'					=> 'Déplacer vers l’album',
	'MOVE_TO_PERSONAL'				=> 'Déplacer vers l’album personnel',
	'MOVE_TO_PERSONAL_MOD'			=> 'Lorsque vous sélectionnez cette option, l’image est déplacée dans l’album personnel de l’utilisateur. Si l’utilisateur n’en a pas, il sera créé automatiquement.',
	'MOVE_TO_PERSONAL_EXPLAIN'		=> 'Lorsque vous sélectionnez cette option, l’image est déplacée dans l’album personnel de l’utilisateur. Si l’utilisateur n’en a pas, il sera créé automatiquement.',

	'NEW_COMMENT'					=> 'Nouveau commentaire',
	'NEW_IMAGES'					=> 'Nouvelles images',
	'NEWEST_PGALLERY'				=> 'Dernière galerie personnelle : %s',
	'NO_ALBUMS'						=> 'Il n’y a aucun album dans cette galerie.',
	'NO_COMMENTS'					=> 'Aucun commentaire',
	'NO_IMAGES'						=> 'Aucune image',
	'NO_IMAGES_FOUND'				=> 'Aucune image trouvée.',
	'NO_NEW_IMAGES'					=> 'Aucune nouvelle image',
	'NO_IMAGES_LONG'				=> 'Il n’y a aucune image dans cet album.',
	'NOT_ALLOWED_FILE_TYPE'			=> 'Ce type de fichier n’est pas autorisé.',
	'NOT_RATED'						=> 'Aucune note',

	'NO_WRITE_ACCESS'				=> 'Le répertoire de téléchargement est manquant ou phpBB n’a pas d’accès en écriture.<br>Merci de contacter l’administrateur du forum.',

	'ORDER'							=> 'Trier par',
	'ORIG_FILENAME'					=> 'Prenez le nom du fichier comme nom de l’image (le champ d’insertion n’a pas de fonction)',
	'OUT_OF_RANGE_VALUE'			=> 'La valeur est hors limites',
	'OWN_IMAGES'					=> 'Vos images',

	'PERCENT'						=> '%',
	'PERSONAL_ALBUMS'				=> 'Albums personnels',
	'PLUGIN_CLASS_MISSING'			=> 'Erreur du plugin : La classe « %s » n’a pas pu être trouvée.',
	'POST_COMMENT'					=> 'Publier un commentaire',
	'POST_COMMENT_RATE_IMAGE'		=> 'Publier un commentaire et noter l’image',
	'POSTER'						=> 'Publier',

	'QUOTA_REACHED'					=> 'Le nombre d’images, que vous êtes autorisé à charger, a été atteint.',
	'QUOTE_COMMENT'					=> 'Citer le commentaire',

	'RANDOM_IMAGES'					=> 'Images aléatoires',
	'RATE_IMAGE'					=> 'Noter l’image',
	'RATES_COUNT'					=> 'Nombre de notes',
	'RATING'						=> 'Note',
	'RATING_STRINGS'				=> array(
		0	=> 'Aucune',
		1	=> '%2$s (1 note)',
		2	=> '%2$s (%1$s notes)',
	),
	'RATING_STRINGS_USER'			=> array(
		1	=> '%2$s (1 note, votre note : %3$s)',
		2	=> '%2$s (%1$s notes, votre note : %3$s)',
	),
	'RATING_SUCCESSFUL'				=> 'L’image a été notée avec succès.',
	'READ_REPORT'					=> 'Lire le rapport',
	'RECENT_COMMENTS'				=> 'Derniers commentaires',
	'RECENT_IMAGES'					=> 'Dernières images',
	'REPORT_IMAGE'					=> 'Rapporter l’image',
	'RETURN_ALBUM'					=> '%sRetourner au dernier album visité%s',
	'ROTATE_IMAGE'					=> 'Faire pivoter l’image',
	'ROTATE_LEFT'					=> '90° à gauche',
	'ROTATE_NONE'					=> 'aucun',
	'ROTATE_RIGHT'					=> '90° à droite',
	'ROTATE_UPSIDEDOWN'				=> '180° à l’envers',
	'RETURN_TO_GALLERY'				=> 'Retour à la galerie',

	'SEARCH_ALBUM'					=> 'Rechercher dans cet album…',
	'SEARCH_ALBUMS'					=> 'Rechercher dans les albums',
	'SEARCH_ALBUMS_EXPLAIN'			=> 'Rechercher dans le ou les albums sélectionnés. La recherche s’effectue automatiquement dans les sous-albums si l’option « Rechercher dans les sous-albums » n’est pas désactivée.',
	'SEARCH_COMMENTS'				=> 'Seulement les commentaires',
	'SEARCH_CONTEST'				=> 'Vainqueurs du concours',
	'SEARCH_IMAGE_COMMENTS'			=> 'Nom des images, descriptions et commentaires',
	'SEARCH_IMAGE_VALUES'			=> 'Seulement le nom des images et les descriptions',
	'SEARCH_IMAGENAME'				=> 'Seulement le nom des images',
	'SEARCH_RANDOM'					=> 'Images aléatoires',
	'NO_SEARCH_RESULTS_RANDOM'		=> 'Il n’y a pas d’images ou vous n’avez pas l’autorisation pour les voir.',
	'SEARCH_RECENT'					=> 'Dernières images',
	'NO_SEARCH_RESULTS_RECENT'		=> 'Il n’y a pas d’images ou vous n’avez pas l’autorisation pour les voir.',
	'SEARCH_RECENT_COMMENTS'		=> 'Derniers commentaires',
	'NO_SEARCH_RESULTS_RECENT_COMMENTS'	=> 'Il n’y a pas de commentaires ou vous n’avez pas l’autorisation pour les voir.',
	'SEARCH_SUBALBUMS'				=> 'Rechercher dans les sous-albums',
	'SEARCH_TOPRATED'				=> 'Images les mieux notées',
	'SEARCH_USER_IMAGES'			=> 'Rechercher les images de l’utilisateur',
	'SEARCH_USER_IMAGES_OF'			=> 'Images de %s',
	'SELECT_ALBUM'					=> 'Sélectionner un album',
	'SHOW_PERSONAL_ALBUM_OF'		=> 'Afficher l’album personnel de %s',
	'SLIDE_SHOW'					=> 'Diaporama',
	'SLIDE_SHOW_HIGHSLIDE'			=> 'Pour démarrer le diaporama, cliquez sur le nom d’une image et cliquez sur l’icône « Lecture » :',
	'SLIDE_SHOW_LYTEBOX'			=> 'Pour démarrer le diaporama, cliquez sur le nom d’une image :',
	'SLIDE_SHOW_SHADOWBOX'			=> 'Pour démarrer le diaporama, cliquez sur le nom d’une image :',
	'SORT_ASCENDING'				=> 'Ordre croissant',
	'SORT_DEFAULT'					=> 'Par défaut',
	'SORT_DESCENDING'				=> 'Ordre décroissant',
	'STATUS'						=> 'Statut',
	'SUBALBUMS'						=> 'Sous-albums',
	'SUBALBUM'						=> 'Sous-album',

	'THUMBNAIL_SIZE'				=> 'Taille de la miniature (pixels)',
	'TOTAL_COMMENTS_SPRINTF'		=> array(
		0	=> '<strong>0</strong> commentaires',
		1	=> '<strong>%d</strong> commentaire',
		2	=> '<strong>%d</strong> commentaires',
	),
	'TOTAL_IMAGES'					=> 'Total d’images',
	'TOTAL_IMAGES_SPRINTF'			=> array(
		0	=> 'Pas d’images',
		1	=> '%d image',
		2	=> '%d images',
	),
	'TOTAL_PEGAS_SHORT_SPRINTF'		=> array(
		0	=> '0 galeries personnelles',
		1	=> '%d galeries personnelle',
		2	=> '%d galeries personnelles',
	),
	'TOTAL_PEGAS_SPRINTF'		=> array(
		0	=> '<strong>0</strong> galeries personnelles',
		1	=> '<strong>%d</strong> galeries personnelle',
		2	=> '<strong>%d</strong> galeries personnelles',
	),

	'UNLOCK_IMAGE'					=> 'Déverrouiller l’image',
	'UNWATCH_ALBUM'					=> 'Ne plus surveiller l’album',
	'UNWATCH_IMAGE'					=> 'Ne plus surveiller l’image',
	'UNWATCH_PEGAS'					=> 'Ne pas souscrire à de nouvelles galeries privées',
	'UNWATCHED_ALBUM'				=> 'Vous ne serez plus informé des nouvelles images de cet album.',
	'UNWATCHED_ALBUMS'				=> 'Vous ne serez plus informé des nouvelles images de ces albums.',
	'UNWATCHED_IMAGE'				=> 'Vous ne serez plus informé des nouveaux commentaires de cette image.',
	'UNWATCHED_IMAGES'				=> 'Vous ne serez plus informé des nouveaux commentaires de ces images.',
	'UNWATCHED_PEGAS'				=> 'Vous n’êtes plus abonné automatiquement aux nouvelles galeries personnelles.',
	'UPLOAD_ERROR'					=> 'Pendant le chargement du fichier « %1$s », l’erreur suivante est survenue :<br />» %2$s',
	'UPLOAD_IMAGE'					=> 'Charger une image',
	'UPLOAD_IMAGE_SIZE_TOO_BIG'		=> 'Les dimensions de votre image sont trop grandes.',
	'UPLOAD_NO_FILE'				=> 'Vous devez saisir le chemin et le nom du fichier.',
	'UPLOADED_BY_USER'				=> 'Chargée par',
	'UPLOADED_ON_DATE'				=> 'Chargée le',
	'USE_SAME_NAME'					=> 'Utilisez le même nom d’image et la même description pour toutes les images.',
	'USE_NUM'						=> 'Ajouter {NUM} pour les numéroter. Commencer à compter à partir de :',
	'USER_REACHED_QUOTA'			=> array(
		0	=> 'Vous n’êtes pas autorisé à charger <strong>toutes</strong> les images.<br />Merci de contacter un administrateur pour plus d’informations.',
		1	=> 'Vous n’êtes pas autorisé à charger plus d’<strong>1</strong> image.<br />Merci de contacter un administrateur pour plus d’informations.',
		2	=> 'Vous n’êtes pas autorisé à charger plus de <strong>%s</strong> images.<br />Merci de contacter un administrateur pour plus d’informations.',
	),
	'USER_REACHED_QUOTA_SHORT'		=> array(
		0	=> 'Vous n’êtes pas autorisé à charger <strong>toutes</strong> les images.',
		1	=> 'Vous n’êtes pas autorisé à charger plus d’<strong>1</strong> image.',
		2	=> 'Vous n’êtes pas autorisé à charger plus de <strong>%s</strong> images.',
	),
	'USERNAME_BEGINS_WITH'			=> 'Le nom d’utilisateur commence par',
	'USERS_PERSONAL_ALBUMS'			=> 'Albums Personnels des Utilisateurs',

	'VISIT_GALLERY'					=> 'Visiter le galerie de l’utilisateur',

	'VIEW_ALBUM'					=> 'Voir l’album',
	'VIEW_ALBUM_IMAGES'				=> array(
		1	=> '1 image',
		2	=> '%s images',
	),
	'VIEW_IMAGE'					=> 'Voir l’image',
	'VIEW_IMAGE_COMMENTS'			=> array(
		1	=> '1 commentaire',
		2	=> '%s commentaires',
	),
	'VIEW_LATEST_IMAGE'				=> 'Voir la dernière image',
	'VIEW_SEARCH_RECENT'			=> 'Voir les dernières images',
	'VIEW_SEARCH_RANDOM'			=> 'Voir les images aléatoires',
	'VIEW_SEARCH_COMMENTED'			=> 'Voir les derniers commentaires',
	'VIEW_SEARCH_CONTESTS'			=> 'Voir les vainqueurs du concours',
	'VIEW_SEARCH_TOPRATED'			=> 'Voir les images les mieux notées',
	'VIEW_SEARCH_SELF'				=> 'Voir vos images',
	'VIEWING_ALBUM'					=> 'Voir l’album %s',
	'VIEWING_IMAGE'					=> 'Voir l’image de l’album %s',

	'WATCH_ALBUM'					=> 'S’abonner à l’album',
	'WATCH_IMAGE'					=> 'S’abonner à l’image',
	'WATCH_PEGAS'					=> 'S’abonner aux nouvelles galeries personnelles',
	'WATCHING_ALBUM'				=> 'Vous êtes maintenant informé des nouvelles images de cet album.',
	'WATCHING_IMAGE'				=> 'Vous êtes maintenant informé des nouveaux commentaires de cette image.',
	'WATCHING_PEGAS'				=> 'Vous êtes maintenant automatiquement abonné aux nouvelles galeries personnelles.',

	'YOUR_COMMENT'					=> 'Votre commentaire',
	'YOUR_PERSONAL_ALBUM'			=> 'Votre Album Personnel',
	'YOUR_RATING'					=> 'Vos notes',

	'IMAGES_MOVED'					=> array(
		1	=>	'Image déplacée',
		2	=> 	'%s images déplacées',
	),

	'QUICK_MOD'	=> 'Sélectionner l’action du modérateur',
	'WRONG_FILESIZE'	=> 'L’image est plus lourde que la limite de poids autorisée.',
	'UNREAD_IMAGES'		=> 'Unviewed images',
	'NO_UNREAD_IMAGES'	=> 'No unviewed images',

	// Versions 1.2.1 additions
	'GALLERY_DROP'		=> 'Déposez vos images ici',
));
