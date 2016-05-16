<?php
/**
*
* info_acp_gallery [Dutch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* [Dutch] translated by Dutch Translators (https://github.com/dutch-translators)
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
	'ACP_GALLERY_ALBUM_MANAGEMENT'		=> 'Beheer album',
	'ACP_GALLERY_ALBUM_PERMISSIONS'		=> 'Permissies',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY'=> 'Kopieer permissies',
	'ACP_GALLERY_CONFIGURE_GALLERY'		=> 'Configureer galerij',
	'ACP_GALLERY_LOGS'					=> 'Galerij log',
	'ACP_GALLERY_LOGS_EXPLAIN'			=> 'Toont alle moderator-acties van de galerij, zoals goed e/of afkeuren, sluiten, heropenen, gesloten meldingen en verwijderde afbeeldingen.',
	'ACP_GALLERY_MANAGE_ALBUMS'			=> 'Beheer albums',
	'ACP_GALLERY_OVERVIEW'				=> 'Overzicht',

	'GALLERY'							=> 'Galerij',
	'GALLERY_EXPLAIN'					=> 'Galerij afbeelding',
	'GALLERY_HELPLINE_ALBUM'			=> 'Galerij afbeelding: [album]image_id[/album], met deze BBCode kan je een afbeelding uit de galerij toevoegen aan je bericht.',
	'GALLERY_POPUP'						=> 'Galerij',
	'GALLERY_POPUP_HELPLINE'			=> 'Opent een popup-venster waarin je je eigen recente afbeeldingen kan selecteren en nieuwe afbeeldingen kan uploaden.',

	// A little line where you can give yourself some credits on the translation.
	//'GALLERY_TRANSLATION_INFO' => 'English “phpBB Gallery“-Translation by <a href="http://www.flying-bits.org/">nickvergessen</a>',
	'GALLERY_TRANSLATION_INFO'   => 'Nederlandse vertaling door <a href="https://github.com/dutch-translators">Dutch Translators</a>',

	'IMAGES'							=> 'Afbeeldingen',
	'IMG_BUTTON_UPLOAD_IMAGE'			=> 'Afbeelding uploaden',

	'PERSONAL_ALBUM'					=> 'Persoonlijk album',
	'PHPBB_GALLERY'						=> 'phpBB Galerij',

	'TOTAL_IMAGES_SPRINTF'				=> array(
		0		=> 'Aantal afbeeldingen <strong>0</strong>',
		1		=> 'Aantal afbeeldingen <strong>%d</strong>',
	),
));
