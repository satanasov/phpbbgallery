<?php
/**
*
* Gallery Notifications [Spanish]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 Lucifer Lucifer@anavaro.com http://www.anavaro.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s subió imágenes para su aprobación en el álbum <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Imágenes en espera de aprobación',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Imágenes aprobadas',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Las imágenes del álbum <strong>%1$s</strong> fueron aprobadas',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Nuevas imágenes',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'Se subieron nuevas imágenes al álbum <strong>%1$s</strong>',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Nuevos comentarios',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> comentó la imagen que está viendo',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Nuevo informe de imagen',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> imagen reportada',
));
