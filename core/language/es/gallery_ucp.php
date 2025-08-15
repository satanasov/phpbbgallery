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
	'ACCESS_CONTROL_ALL'			=> 'Todos',
	'ACCESS_CONTROL_REGISTERED'		=> 'Usuarios registrados',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Usuarios registrados, excepto tus enemigos',
	'ACCESS_CONTROL_FRIENDS'		=> 'Sólo tus amigos',
	'ACCESS_CONTROL_SPECIAL_FRIENDS'		=> 'Sólo tus amigos especiales',
	'ALBUMS'						=> 'Álbumes',
	'ALBUM_ACCESS'					=> 'Permitir acceso para',
	'ALBUM_ACCESS_EXPLAIN'			=> 'Puede usar las listas %1$sLista de amigos y enemigos s%2$s para controlar el acceso al álbum. Sin embargo <strong>moderadores</strong> pueden <strong>siempre</strong> acceder al álbum. ',
	'ALBUM_DESC'					=> 'Descripción del álbum',
	'ALBUM_NAME'					=> 'Nombre del álbum',
	'ALBUM_PARENT'					=> 'Álbum padre',
	'ATTACHED_SUBALBUMS'			=> 'Subalbums adjuntos',

	'CREATE_PERSONAL_ALBUM'			=> 'Crear álbum personal',
	'CREATE_SUBALBUM'				=> 'Crear subalbum',
	'CREATE_SUBALBUM_EXP'			=> 'Puedes adjuntar un nuevo subalbum a tu galería personal.',
	'CREATED_SUBALBUM'				=> 'Subalbum creado correctamente',

	'DELETE_ALBUM'					=> 'Eliminar álbum',
	'DELETE_ALBUM_CONFIRM'			=> '¿Eliminar álbum, con todos los subalbums e imágenes adjuntos?',
	'DELETED_ALBUMS'				=> 'Álbumes eliminados correctamente',

	'EDIT'							=> 'Editar',
	'EDIT_ALBUM'					=> 'Editar álbum',
	'EDIT_SUBALBUM'					=> 'Editar Subalbum',
	'EDIT_SUBALBUM_EXP'				=> 'Puedes editar tus álbumes aquí.',
	'EDITED_SUBALBUM'				=> 'El álbum se ha editado correctamente',

	'GOTO'							=> 'Ir a',

	'MANAGE_SUBALBUMS'				=> 'Administrar sus subalbums',
	'MISSING_ALBUM_NAME'			=> 'Por favor, introduzca un nombre para el álbum',

	'NEED_INITIALISE'				=> 'No tienes un álbum personal todavía.',
	'NO_ALBUM_STEALING'				=> 'No puedes administrar el Álbum de otros usuarios.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'Agregaste tu máximo de subalbums a tu álbum personal',
	'NO_PARENT_ALBUM'				=> '&laquo;-- sin padre',
	'NO_PERSALBUM_ALLOWED'			=> 'No tienes permisos para crear tu álbum personal',
	'NO_PERSONAL_ALBUM'				=> 'Todavía no tienes un álbum personal. Aquí puedes crear tu álbum personal, con algunos subalbums.<br />En álbumes personales sólo el propietario puede subir imágenes. ',
	'NO_SUBALBUMS'					=> 'No hay álbumes adjuntos',
	'NO_SUBSCRIPTIONS'				=> 'No se ha suscrito a ninguna imagen.',
	'NO_SUBSCRIPTIONS_ALBUM'		=> 'You are not subscribed to an album.',

	'PARSE_BBCODE'					=> 'Analizar BBCode',
	'PARSE_SMILIES'					=> 'Analizar smilies',
	'PARSE_URLS'					=> 'Analizar enlaces',
	'PERSONAL_ALBUM'				=> 'Álbum personal',

	'UNSUBSCRIBE'					=> 'dejar de ver',
	'USER_ALLOW_COMMENTS'			=> 'Permitir a los usuarios comentar sus imágenes',

	'YOUR_SUBSCRIPTIONS'			=> 'Aquí ves los álbumes y las imágenes en las que recibes la notificación.',

	'WATCH_CHANGED'					=> 'Ajustes guardados',
	'WATCH_COM'						=> 'Suscribir imágenes comentadas por defecto',
	'WATCH_NOTE'					=> 'Esta opción sólo afecta a las nuevas imágenes. Todas las demás imágenes deben añadirse mediante la opción "suscribir imagen". ',
	'WATCH_OWN'						=> 'Suscribir imágenes propias por defecto',

	'RRC_ZEBRA'						=> 'Hide from foes in RRC',
	'RRC_ZEBRA_EXPLAIN'			=> 'Hide images in albums from foes in Recent, Random and Comments part of the index.<br /><strong>WARNING!</strong> This won\'t hide images uploaded in common/public albums.'
));
