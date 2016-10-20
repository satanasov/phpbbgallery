<?php
/**
*
* @package phpBB Gallery - Info ACP Extension [French]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* @translator fr (c) pokyto aka le.poke http://www.lestontonsfraggers.com inspired by darky - http://www.foruminfopc.fr/ and Team http://www.phpbb-fr.com/
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
	'ACP_GALLERY_ALBUM_MANAGEMENT'		=> 'Gestion des albums',
	'ACP_GALLERY_ALBUM_PERMISSIONS'		=> 'Permissions',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY'=> 'Copier les permissions',
	'ACP_GALLERY_CONFIGURE_GALLERY'		=> 'Configuration de la Galerie',
	'ACP_GALLERY_LOGS'					=> 'Journal de la Galerie',
	'ACP_GALLERY_LOGS_EXPLAIN'			=> 'Liste toutes les actions des modérateurs de la galerie, comme les validations, les refus, les albmus/images verrouillé(e)s, déverrouillé(e)s, les rapports fermés, les images supprimées...',
	'ACP_GALLERY_MANAGE_ALBUMS'			=> 'Gérer les albums',
	'ACP_GALLERY_OVERVIEW'				=> 'Vue d’ensemble',

	'GALLERY'							=> 'Galerie',
	'GALLERY_EXPLAIN'					=> 'Images de la Galerie',
	'GALLERY_HELPLINE_ALBUM'			=> 'Images de la Galerie : avec ce BBCode [image]image_id[/image], vous pouvez ajouter une image de la galerie dans votre message.',
	'GALLERY_POPUP'						=> 'Galerie',
	'GALLERY_POPUP_HELPLINE'			=> 'Ouvrir une nouvelle fenêtre, où vous pouvez sélectionner vos récentes images et charger de nouvelles images.',

	// A little line where you can give yourself some credits on the translation.
	//'GALLERY_TRANSLATION_INFO'			=> 'English “phpBB Gallery“-Translation by <a href="http://www.flying-bits.org/">nickvergessen</a>',
	'GALLERY_TRANSLATION_INFO'			=> '« phpBB Gallery » - Traduction française par <a href="http://www.lestontonsfraggers.com">pokyto aka le.poke</a> inspiré par <a href="http://www.foruminfopc.fr/">darky</a> et l’<a href="http://www.phpbb-fr.com/">équipe phpbb-fr.com</a>',

	'IMAGES'							=> 'Images',
	'IMG_BUTTON_UPLOAD_IMAGE'			=> 'Charger une image',

	'PERSONAL_ALBUM'					=> 'Album personnel',
	'PHPBB_GALLERY'						=> 'Galerie phpBB',

	'TOTAL_IMAGES_SPRINTF'				=> array(
		0		=> '<strong>0</strong> images',
		1		=> '<strong>%d</strong> images',
	),
));
