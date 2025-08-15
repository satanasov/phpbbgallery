<?php
/**
 * phpBB Gallery - ACP Core Extension [Spanish Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator
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
	'ACP_GALLERY_ALBUM_MANAGEMENT'		=> 'Gestión de álbumes',
	'ACP_GALLERY_ALBUM_PERMISSIONS'		=> 'Permisos',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY'=> 'Copiar permisos',
	'ACP_GALLERY_CONFIGURE_GALLERY'		=> 'Configurar galería',
	'ACP_GALLERY_LOGS'					=> 'Registro de la galería',
	'ACP_GALLERY_LOGS_EXPLAIN'			=> 'Esto enumera todas las acciones del moderador de la galería como aprobar desaprobar bloquear desbloquear cerrar informes y eliminar imágenes.',
	'ACP_GALLERY_MANAGE_ALBUMS'			=> 'Gestionar álbumes',
	'ACP_GALLERY_OVERVIEW'				=> 'Descripción general',

	'GALLERY'							=> 'Galería',
	'GALLERY_EXPLAIN'					=> 'Galería de imágenes',
	'GALLERY_HELPLINE_ALBUM'			=> 'Imagen de la galería: [image]image_id[/image], con este BBCode puedes añadir una imagen de la galería a tu publicación.',
	'GALLERY_POPUP'						=> 'Galería',
	'GALLERY_POPUP_HELPLINE'			=> 'Abre una ventana emergente donde puedes seleccionar tus imágenes recientes y subir nuevas imágenes.',

	// Please do not change the copyright.
	'GALLERY_COPYRIGHT'        => 'Powered by <a href="http://www.anavaro.com/">phpBB Gallery</a> &copy; 2016 <a href="http://www.anavaro.com/">Lucifer</a>',

	// Una pequeña línea donde puedes darte algunos créditos sobre la traducción.
	//'GALLERY_TRANSLATION_INFO'			=> 'English “phpBB Gallery“-Translation by <a href="http://www.flying-bits.org/">nickvergessen</a>',
	'GALLERY_TRANSLATION_INFO'			=> '',

	'IMAGES'							=> 'Imágenes',
	'IMG_BUTTON_UPLOAD_IMAGE'			=> 'Subir imagen',

	'PERSONAL_ALBUM'					=> 'Álbum personal',
	'PHPBB_GALLERY'						=> 'Galería phpBB',

	'TOTAL_IMAGES_SPRINTF'				=> array(
		0		=> 'Total de imágenes <strong>0</strong>',
		1		=> 'Total de imágenes <strong>%d</strong>',
	),
));
