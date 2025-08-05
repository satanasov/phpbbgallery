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
	'ACP_GALLERY_ALBUM_MANAGEMENT'       => 'Gestion des albums',
	'ACP_GALLERY_ALBUM_PERMISSIONS'      => 'Permissions',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY' => 'Copier les permissions',
	'ACP_GALLERY_CONFIGURE_GALLERY'      => 'Configuration de la Galerie',
	'ACP_GALLERY_LOGS'                   => 'Journal de la Galerie',
	'ACP_GALLERY_LOGS_EXPLAIN'           => 'Liste toutes les actions des modérateurs de la galerie, comme les validations, les refus, les albums/images verrouillé(e)s, déverrouillé(e)s, les rapports fermés, les images supprimées…',
	'ACP_GALLERY_MANAGE_ALBUMS'          => 'Gérer les albums',
	'ACP_GALLERY_OVERVIEW'               => 'Vue d’ensemble',

	'GALLERY'                  => 'Galerie',
	'GALLERY_EXPLAIN'          => 'Images de la Galerie',
	'GALLERY_HELPLINE_ALBUM'   => 'Images de la Galerie : au moyen de ce BBCode [image]image_id[/image], ajoutez dans votre message une image de la galerie.',
	'GALLERY_POPUP'            => 'Galerie',
	'GALLERY_POPUP_HELPLINE'   => 'Ouvrir une nouvelle fenêtre, où vous pouvez sélectionner vos images récentes et charger de nouvelles images.',

	// Please do not change the copyright.
	'GALLERY_COPYRIGHT'        => 'Powered by <a href="http://www.anavaro.com/">phpBB Gallery</a> &copy; 2016 <a href="http://www.anavaro.com/">Lucifer</a>',

	// A little line where you can give yourself some credits on the translation.
	'GALLERY_TRANSLATION_INFO' => '« phpBB Gallery » - Traduction française par <a href="http://www.lestontonsfraggers.com">pokyto aka le.poke</a> (inspiré par <a href="http://www.foruminfopc.fr/">darky</a> et l’<a href="http://www.phpbb-fr.com/">équipe phpbb-fr.com</a>) & par <a href="http://www.galixte.com" title="Galixte’s Projects">Galixte</a>',

	'IMAGES'                  => 'Images',
	'IMG_BUTTON_UPLOAD_IMAGE' => 'Charger une image',

	'PERSONAL_ALBUM' => 'Album personnel',
	'PHPBB_GALLERY'  => 'Galerie phpBB',

	'TOTAL_IMAGES_SPRINTF' => array(
		0 => '<strong>0</strong> images',
		1 => '<strong>%d</strong> images',
	),
));
