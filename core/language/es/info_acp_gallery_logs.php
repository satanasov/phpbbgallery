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
	'ACP_LOG_GALLERY_MOD'						=> 'Registro del moderador',
	'ACP_LOG_GALLERY_MOD_EXP'					=> 'Registro del moderador',
	'ACP_LOG_GALLERY_ADM'						=> 'Registro de administrador',
	'ACP_LOG_GALLERY_ADM_EXP'					=> 'Registro de administrador',
	'ACP_LOG_GALLERY_SYSTEM'					=> 'Registro del sistema',
	'ACP_LOG_GALLERY_SYSTEM_EXP'				=> 'Registro del sistema',
	'LOG_GALLERY_SHOW_LOGS'						=> 'Mostrar sólo',

	'SORT_USER_ID'							=> 'ID de usuario',

	'LOG_ALBUM_ADD'							=> '<strong>Creó un nuevo álbum</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUM'					=> '<strong>Álbum eliminado</strong><br />» %s',
	'LOG_ALBUM_DEL_ALBUMS'					=> '<strong>Álbum eliminado y sus subalbums</strong><br />» %s',
	'LOG_ALBUM_DEL_MOVE_ALBUMS'				=> '<strong>Álbum eliminado y subalbums movidos</strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES'				=> '<strong>Álbum eliminado y imágenes movidas </strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_ALBUMS'		=> '<strong>Álbum eliminado y sus subalbumos imágenes movidas</strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_DEL_MOVE_IMAGES_MOVE_ALBUMS'	=> '<strong>Álbum eliminado imágenes movidas</strong> to %1$s <strong>y subalbums</strong> to %2$s<br />» %3$s',
	'LOG_ALBUM_DEL_IMAGES'					=> '<strong>Álbum eliminado y sus imágenes</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_ALBUMS'			=> '<strong>Álbum eliminado sus imágenes y subalbums</strong><br />» %s',
	'LOG_ALBUM_DEL_IMAGES_MOVE_ALBUMS'		=> '<strong>Álbum eliminado y sus imágenes subalbums movidos</strong> to %1$s<br />» %2$s',
	'LOG_ALBUM_EDIT'						=> '<strong>Detalles del álbum editado</strong><br />» %s',
	'LOG_ALBUM_MOVE_DOWN'					=> '<strong>Álbum movido</strong> %1$s <strong>abajo</strong> %2$s',
	'LOG_ALBUM_MOVE_UP'						=> '<strong>Álbum movido</strong> %1$s <strong>arriba</strong> %2$s',
	'LOG_ALBUM_SYNC'						=> '<strong>Álbum re-sincronizado</strong><br />» %s',

	'LOG_CLEAR_GALLERY'					=> 'Archivo de la galería borrada',

	'LOG_GALLERY_APPROVED'				=> '<strong>Imagen aprobada</strong><br />» %s',
	'LOG_GALLERY_COMMENT_DELETED'		=> '<strong>Comentarios eliminados</strong><br />» %s',
	'LOG_GALLERY_COMMENT_EDITED'		=> '<strong>Comentario editado</strong><br />» %s',
	'LOG_GALLERY_DELETED'				=> '<strong>Imagen eliminada</strong><br />» %s',
	'LOG_GALLERY_EDITED'				=> '<strong>Imagen editada</strong><br />» %s',
	'LOG_GALLERY_LOCKED'				=> '<strong>Imagen bloqueada</strong><br />» %s',
	'LOG_GALLERY_MOVED'					=> '<strong>Imagen movida</strong><br />» from %1$s to %2$s',
	'LOG_GALLERY_REPORT_CLOSED'			=> '<strong>Informe cerrado</strong><br />» %s',
	'LOG_GALLERY_REPORT_DELETED'		=> '<strong>Informe eliminado</strong><br />» %s',
	'LOG_GALLERY_REPORT_OPENED'			=> '<strong>Informe reabierto</strong><br />» %s',
	'LOG_GALLERY_UNAPPROVED'			=> '<strong>Imagen no aprobada</strong><br />» %s',
	'LOG_GALLERY_DISAPPROVED'			=> '<strong>Imagen rechazada</strong><br />» %s',

	'LOGVIEW_VIEWALBUM'					=> 'Ver álbum',
	'LOGVIEW_VIEWIMAGE'					=> 'Ver imagen',
));
