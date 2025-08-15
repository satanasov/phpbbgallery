<?php
/**
 * phpBB Gallery - ACP Core Extension [German Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator franki <https://dieahnen.de/ahnenforum/>
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

$lang = array_merge($lang, array(
	'ACP_GALLERY_ALBUM_MANAGEMENT'       => 'Album-Verwaltung',
	'ACP_GALLERY_ALBUM_PERMISSIONS'      => 'Berechtigungen',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY' => 'Berechtigungen kopieren',
	'ACP_GALLERY_CONFIGURE_GALLERY'      => 'Galerie konfigurieren',
	'ACP_GALLERY_LOGS'                   => 'Galerie-Protokoll',
	'ACP_GALLERY_LOGS_EXPLAIN'           => 'Diese Liste zeigt alle Vorgänge, die von Moderatoren an Bildern und Kommentaren durchgeführt wurden.',
	'ACP_GALLERY_MANAGE_ALBUMS'          => 'Alben verwalten',
	'ACP_GALLERY_OVERVIEW'               => 'Übersicht',

	'GALLERY'                  => 'Galerie',
	'GALLERY_EXPLAIN'          => 'Bilder Galerie',
	'GALLERY_HELPLINE_ALBUM'   => 'Galerie-Bild: [image]image_id[/image], mit diesem BBCode kannst du Bilder aus der Galerie in deinen Beitrag einfügen.',
	'GALLERY_POPUP'            => 'Galerie',
	'GALLERY_POPUP_HELPLINE'   => 'Öffne ein Popup in dem du deine neuesten Bilder auswählen und neue Bilder hochladen kannst.',

	// Please do not change the copyright.
	'GALLERY_COPYRIGHT'        => 'Powered by <a href="http://www.anavaro.com/">phpBB Gallery</a> &copy; 2016 <a href="http://www.anavaro.com/">Lucifer</a>',

	// A little line where you can give yourself some credits on the translation.
	//'GALLERY_TRANSLATION_INFO'		=> 'English “phpBB Gallery“-Translation by <a href="http://www.flying-bits.org/">nickvergessen</a>',
	'GALLERY_TRANSLATION_INFO' => 'Übersetzt von franki <a href="http://dieahnen.de/ahnenforum/">Die Ahnen</a>',

	'IMAGES'                  => 'Bilder',
	'IMG_BUTTON_UPLOAD_IMAGE' => 'Bild hochladen',

	'PERSONAL_ALBUM' => 'Persönliches Album',
	'PHPBB_GALLERY'  => 'phpBB Galerie',

	'TOTAL_IMAGES_SPRINTF' => array(
		0 => 'Bilder insgesamt: <strong>0</strong>',
		1 => 'Bilder insgesamt: <strong>%d</strong>',
	),
));
