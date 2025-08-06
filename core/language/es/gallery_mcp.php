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
	'CHOOSE_ACTION' 				=> 'Seleccionar la acción deseada',

	'GALLERY_MCP_MAIN' 				=> 'Principal',
	'GALLERY_MCP_OVERVIEW' 			=> 'Descripción general',
	'GALLERY_MCP_QUEUE' 			=> 'Cola',
	'GALLERY_MCP_QUEUE_DETAIL' 		=> 'Detalles de la imagen',
	'GALLERY_MCP_REPORTED' 			=> 'Imágenes reportadas',
	'GALLERY_MCP_REPO_DONE' 		=> 'Informes cerrados',
	'GALLERY_MCP_REPO_OPEN' 		=> 'Abrir informes',
	'GALLERY_MCP_REPO_DETAIL' 		=> 'Detalles del informe',
	'GALLERY_MCP_UNAPPROVED' 		=> 'Imágenes en espera de aprobación',
	'GALLERY_MCP_APPROVED' 			=> 'Imágenes aprobadas',
	'GALLERY_MCP_LOCKED' 			=> 'Imágenes bloqueadas',
	'GALLERY_MCP_VIEWALBUM' 		=> 'Ver álbum',
	'GALLERY_MCP_ALBUM_OVERVIEW' 	=> 'Álbum moderado',

	'IMAGE_REPORTED' 				=> 'Se informó la imagen.',
	'IMAGE_UNAPPROVED' 				=> 'La imagen está a la espera de aprobación.',

	'MODERATE_ALBUM' 				=> 'Álbum moderado',

	'LATEST_IMAGES_REPORTED' 		=> 'Últimas imágenes reportadas',
	'LATEST_IMAGES_UNAPPROVED' 		=> 'Últimas 5 imágenes en espera de aprobación',

	'QUEUE_A_APPROVE' 				=> 'Aprobar imagen',
	'QUEUE_A_APPROVE2' 				=> '¿Aprobar imagen?',
	'QUEUE_A_APPROVE2_CONFIRM' 		=> '¿Estás seguro de que quieres aprobar esta imagen?',
	'QUEUE_A_DELETE' 				=> 'Eliminar imagen',
	'QUEUE_A_DELETE2' 				=> '¿Eliminar imagen?',
	'QUEUE_A_DELETE2_CONFIRM' 		=> '¿Está seguro de que desea eliminar esta imagen?',
	'QUEUE_A_LOCK' 					=> 'Bloquear comentarios de la imagen',
	'QUEUE_A_LOCK2' 				=> '¿Aprobar y bloquear comentarios de la imagen?',
	'QUEUE_A_LOCK2_CONFIRM' 		=> '¿Está seguro de que desea aprobar y bloquear los comentarios de esta imagen?',
	'QUEUE_A_MOVE' 					=> 'Mover imagen',
	'QUEUE_A_UNAPPROVE' 			=> 'No aprobar imagen',
	'QUEUE_A_UNAPPROVE2' 			=> '¿Aprobar la imagen?',
	'QUEUE_A_UNAPPROVE2_CONFIRM' 	=> '¿Está seguro de que desea suprimir esta imagen?',

	'QUEUE_STATUS_0' 				=> 'La imagen está a la espera de aprobación.',
	'QUEUE_STATUS_1' 				=> 'La imagen está aprobada.',
	'QUEUE_STATUS_2' 				=> 'La imagen está bloqueada.',

	'QUEUES_A_APPROVE' 				=> 'Aprobar imágenes',
	'QUEUES_A_APPROVE2' 			=> '¿Aprobar imágenes?',
	'QUEUES_A_APPROVE2_CONFIRM' 	=> '¿Estás seguro de que quieres aprobar estas imágenes?',
	'QUEUES_A_DELETE' 				=> 'Eliminar imágenes',
	'QUEUES_A_DELETE2' 				=> '¿Eliminar imágenes?',
	'QUEUES_A_DELETE2_CONFIRM' 		=> '¿Está seguro de que desea eliminar estas imágenes?',
	'QUEUES_A_LOCK' 				=> 'Bloquear imágenes',
	'QUEUES_A_LOCK2' 				=> '¿Bloquear imágenes?',
	'QUEUES_A_LOCK2_CONFIRM' 		=> '¿Seguro que quieres bloquear estas imágenes?',
	'QUEUES_A_MOVE' 				=> 'Mover imágenes',
	'QUEUES_A_UNAPPROVE' 			=> 'No aprobar imágenes',
	'QUEUES_A_UNAPPROVE2' 			=> '¿Aprobar imágenes?',
	'QUEUES_A_UNAPPROVE2_CONFIRM' 	=> '¿Estás seguro de que quieres desautorizar estas imágenes?',
	'QUEUES_A_DISAPPROVE2_CONFIRM' 	=> '¿Está seguro de que desea desautorizar estas imágenes?',

	'REPORT_A_CLOSE' 				=> 'Cerrar informe',
	'REPORT_A_CLOSE2' 				=> 'Cerrar informe?',
	'REPORT_A_CLOSE2_CONFIRM' 		=> '¿Está seguro de que desea cerrar este informe?',
	'REPORT_A_DELETE' 				=> 'Eliminar informe',
	'REPORT_A_DELETE2' 				=> '¿Eliminar informe?',
	'REPORT_A_DELETE2_CONFIRM' 		=> '¿Está seguro de que desea eliminar este informe?',
	'REPORT_A_OPEN' 				=> 'Informe abierto',
	'REPORT_A_OPEN2' 				=> '¿Informe abierto?',
	'REPORT_A_OPEN2_CONFIRM' 		=> '¿Está seguro de que desea abrir este informe?',

	'REPORT_NOT_FOUND' 				=> 'No se pudo encontrar el informe.',
	'REPORT_STATUS_1' 				=> 'El informe debe ser revisado.',
	'REPORT_STATUS_2' 				=> 'El informe está cerrado.',

	'REPORTS_A_CLOSE' 				=> 'Cerrar informes',
	'REPORTS_A_CLOSE2' 				=> 'Cerrar informes?',
	'REPORTS_A_CLOSE2_CONFIRM' 		=> '¿Está seguro de que desea cerrar estos informes?',
	'REPORTS_A_DELETE' 				=> 'Eliminar informes',
	'REPORTS_A_DELETE2' 			=> '¿Eliminar informes?',
	'REPORTS_A_DELETE2_CONFIRM' 	=> '¿Está seguro de que desea eliminar estos informes?',
	'REPORTS_A_OPEN' 				=> 'Abrir informes',
	'REPORTS_A_OPEN2'				=> '¿Informes abiertos?',
	'REPORTS_A_OPEN2_CONFIRM' 		=> '¿Está seguro de que desea abrir estos informes?',

	'REPORT_MOD' 					=> 'Editado por',
	'REPORT_CLOSED_BY'				=> 'Report closed by',
	'REPORTED_IMAGES' 				=> 'Imágenes reportadas',
	'REPORTER' 						=> 'Usuario de informes',
	'REPORTER_AND_ALBUM' 			=> 'Reportero y Álbum',

	'WAITING_APPROVED_IMAGE'		=> array(
		0 			=> 'No se han aprobado las imágenes.',
		1 			=> 'En total hay <span style="font-weight: bold;">1</span> imagen aprobada.',
		2 			=> 'En total se han aprobado <span style="font-weight: bold;">%s</span> imágenes aprobadas.',
	),
	'WAITING_DISAPPROVED_IMAGE'		=> array(
		0 			=> 'No hay imágenes rechazadas.',
		1 			=> 'En total hay <span style="font-weight: bold;">1</span> imagen rechazada.',
		2 			=> 'En total hay <span style="font-weight: bold;">%s </span> imágenes rechazadas.',
	),
	'WAITING_LOCKED_IMAGE'			=> array(
		0 			=> 'No hay imágenes bloqueadas.',
		1 			=> 'En total hay <span style="font-weight: bold;">1</span> imagen bloqueada.',
		2 			=> 'En total hay <span style="font-weight: bold;">%s </span> imágenes bloqueadas.',
	),
	'WAITING_REPORTED_DONE'			=> array(
		0 			=> 'No se han revisado los informes.',
		1 			=> 'En total hay <span style="font-weight: bold;">1</span> revisado.',
		2 			=> 'En total hay <span style="font-weight: bold;">%s </span> informes revisados.',
	),
	'WAITING_REPORTED_IMAGE'		=> array(
		0 			=> 'No hay informes para revisar.',
		1 			=> 'En total hay <span style="font-weight: bold;">1</span> informe para revisar.',
		2 			=> 'En total hay <span style="font-weight: bold;">%s </span> informes para revisar.',
	),
	'WAITING_UNAPPROVED_IMAGE'		=> array(
		0 			=> 'No hay imágenes en espera de aprobación.',
		1 			=> 'En total hay <span style="font-weight: bold;">1</span> imagen en espera de aprobación.',
		2 			=> 'En total hay <span style="font-weight: bold;">%s < span> imágenes en espera de aprobación.',
	),
	'DELETED_IMAGES'				=> array(
		0 			=> 'No se han eliminado las imágenes.',
		1 			=> 'En total hubo <span style="font-weight: bold;">1</span> imagen eliminada.',
		2 			=> 'En total hubo <span style="font-weight: bold;">%s </span> imágenes eliminadas.',
	),
	'MOVED_IMAGES'					=> array(
		0 			=> 'No se han movido imágenes.',
		1 			=> 'En total hubo <span style="font-weight: bold;">1</span> imagen movida.',
		2 			=> 'En total hubo <span style="font-weight: bold;">%s </span> imágenes movidas.',
	),
	'NO_WAITING_UNAPPROVED_IMAGE' => 'No hay imágenes en espera de aprobación.',
));
