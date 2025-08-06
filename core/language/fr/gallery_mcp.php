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
	'CHOOSE_ACTION'					=> 'Sélectionner l’action désirée',

	'GALLERY_MCP_MAIN'				=> 'Principal',
	'GALLERY_MCP_OVERVIEW'			=> 'Aperçu',
	'GALLERY_MCP_QUEUE'				=> 'File d’attente',
	'GALLERY_MCP_QUEUE_DETAIL'		=> 'Détails de l’image',
	'GALLERY_MCP_REPORTED'			=> 'Images rapportées',
	'GALLERY_MCP_REPO_DONE'			=> 'Rapports fermés',
	'GALLERY_MCP_REPO_OPEN'			=> 'Rapports ouverts',
	'GALLERY_MCP_REPO_DETAIL'		=> 'Détails du rapport',
	'GALLERY_MCP_UNAPPROVED'		=> 'Images en attente de validation',
	'GALLERY_MCP_APPROVED'			=> 'Images validées',
	'GALLERY_MCP_LOCKED'			=> 'Images verrouillées',
	'GALLERY_MCP_VIEWALBUM'			=> 'Voir l’album',
	'GALLERY_MCP_ALBUM_OVERVIEW'	=> 'Modérer l’album',

	'IMAGE_REPORTED'				=> 'Cette image a été rapportée.',
	'IMAGE_UNAPPROVED'				=> 'Cette image est en attente de validation.',

	'MODERATE_ALBUM'				=> 'Modérer l’album',

	'LATEST_IMAGES_REPORTED'		=> 'Les 5 derniers rapports',
	'LATEST_IMAGES_UNAPPROVED'		=> 'Les 5 dernières images en attente de validation',

	'QUEUE_A_APPROVE'				=> 'Valider l’image',
	'QUEUE_A_APPROVE2'				=> 'Valider l’image ?',
	'QUEUE_A_APPROVE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir valider cette image ?',
	'QUEUE_A_DELETE'				=> 'Supprimer l’image',
	'QUEUE_A_DELETE2'				=> 'Supprimer l’image ?',
	'QUEUE_A_DELETE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir supprimer cette image ?',
	'QUEUE_A_LOCK'					=> 'Verrouiller les commentaires sur l’image',
	'QUEUE_A_LOCK2'					=> 'Approuver et verrouiller les commentaires sur l’image ?',
	'QUEUE_A_LOCK2_CONFIRM'			=> 'Êtes-vous sûr de vouloir approuver et bloquer les commentaires sur cette image ?',
	'QUEUE_A_MOVE'					=> 'Déplacer l’image',
	'QUEUE_A_UNAPPROVE'				=> 'Refuser l’image',
	'QUEUE_A_UNAPPROVE2'			=> 'Refuser l’image ?',
	'QUEUE_A_UNAPPROVE2_CONFIRM'	=> 'Êtes-vous sûr de vouloir refuser cette image ?',

	'QUEUE_STATUS_0'				=> 'L’image est attente de validation.',
	'QUEUE_STATUS_1'				=> 'L’image est validée.',
	'QUEUE_STATUS_2'				=> 'L’image est verrouillée.',

	'QUEUES_A_APPROVE'				=> 'Valider les images',
	'QUEUES_A_APPROVE2'				=> 'Valider les images ?',
	'QUEUES_A_APPROVE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir valider ces images ?',
	'QUEUES_A_DELETE'				=> 'Supprimer les images',
	'QUEUES_A_DELETE2'				=> 'Supprimer les images ?',
	'QUEUES_A_DELETE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir supprimer ces images ?',
	'QUEUES_A_LOCK'					=> 'Verrouiller les images',
	'QUEUES_A_LOCK2'				=> 'Verrouiller les images ?',
	'QUEUES_A_LOCK2_CONFIRM'		=> 'Êtes-vous sûr de vouloir verrouiller ces images ?',
	'QUEUES_A_MOVE'					=> 'Déplacer les images',
	'QUEUES_A_UNAPPROVE'			=> 'Refuser les images',
	'QUEUES_A_UNAPPROVE2'			=> 'Refuser les images ?',
	'QUEUES_A_UNAPPROVE2_CONFIRM'	=> 'Êtes-vous sûr de vouloir refuser ces images ?',
	'QUEUES_A_DISAPPROVE2_CONFIRM'	=> 'Êtes-vous sûr de vouloir refuser ces images ?',

	'REPORT_A_CLOSE'				=> 'Fermer le rapport',
	'REPORT_A_CLOSE2'				=> 'Fermer le rapport ?',
	'REPORT_A_CLOSE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir fermer ce rapport ?',
	'REPORT_A_DELETE'				=> 'Supprimer le rapport',
	'REPORT_A_DELETE2'				=> 'Supprimer le rapport ?',
	'REPORT_A_DELETE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir supprimer ce rapport ?',
	'REPORT_A_OPEN'					=> 'Ouvrir le rapport',
	'REPORT_A_OPEN2'				=> 'Ouvrir le rapport ?',
	'REPORT_A_OPEN2_CONFIRM'		=> 'Êtes-vous sûr de vouloir ouvrir ce rapport ?',

	'REPORT_NOT_FOUND'				=> 'Le rapport n’a pas pu être trouvé.',
	'REPORT_STATUS_1'				=> 'Le rapport doit être revu.',
	'REPORT_STATUS_2'				=> 'Le rapport est fermé.',

	'REPORTS_A_CLOSE'				=> 'Fermer les rapports',
	'REPORTS_A_CLOSE2'				=> 'Fermer les rapports ?',
	'REPORTS_A_CLOSE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir fermer ces rapports ?',
	'REPORTS_A_DELETE'				=> 'Supprimer les rapports',
	'REPORTS_A_DELETE2'				=> 'Supprimer les rapports ?',
	'REPORTS_A_DELETE2_CONFIRM'		=> 'Êtes-vous sûr de vouloir supprimer ces rapports ?',
	'REPORTS_A_OPEN'				=> 'Ouvrir les rapports',
	'REPORTS_A_OPEN2'				=> 'Ouvrir les rapports ?',
	'REPORTS_A_OPEN2_CONFIRM'		=> 'Êtes-vous sûr de vouloir ouvrir ces rapports ?',

	'REPORT_MOD'					=> 'Modifié par',
	'REPORT_CLOSED_BY'				=> 'Rapport clôturé par',
	'REPORTED_IMAGES'				=> 'Images rapportées',
	'REPORTER'						=> 'Utilisateur rapporté',
	'REPORTER_AND_ALBUM'			=> 'Rapport & Album',

	'WAITING_APPROVED_IMAGE'		=> array(
		0			=> 'Aucune image rapportée.',
		1			=> 'Il y a au total <span style="font-weight: bold;">1</span> image validée.',
		2			=> 'Il y a au total <span style="font-weight: bold;">%s</span> images approved.',
	),
	'WAITING_DISAPPROVED_IMAGE'		=> array(
		0			=> 'Aucune image désapprouvé.',
		1			=> 'Il y a au total <span style="font-weight: bold;">1</span> image désapprouvée.',
		2			=> 'Il y a au total <span style="font-weight: bold;">%s</span> images désapprouvées.',
	),
	'WAITING_LOCKED_IMAGE'			=> array(
		0			=> 'Aucune image verrouillée.',
		1			=> 'Il y a au total <span style="font-weight: bold;">1</span> image verrouillée.',
		2			=> 'Il y a au total <span style="font-weight: bold;">%s</span> images verrouillées.',
	),
	'WAITING_REPORTED_DONE'			=> array(
		0			=> 'Aucun rapport revu.',
		1			=> 'Il y a au total <span style="font-weight: bold;">1</span> rapport revu.',
		2			=> 'Il y a au total <span style="font-weight: bold;">%s</span> rapports revus.',
	),
	'WAITING_REPORTED_IMAGE'		=> array(
		0			=> 'Aucun rapport à examiner.',
		1			=> 'Il y a au total <span style="font-weight: bold;">1</span> rapport à examiner.',
		2			=> 'Il y a au total <span style="font-weight: bold;">%s</span> rapports à examiner.',
	),
	'WAITING_UNAPPROVED_IMAGE'		=> array(
		0			=> 'Aucune image en attente de validation.',
		1			=> 'Il y a au total <span style="font-weight: bold;">1</span> image en attente de validation.',
		2			=> 'Il y a au total <span style="font-weight: bold;">%s</span> images en attente de validation.',
	),
	'DELETED_IMAGES'		=> array(
		0			=> 'Aucune image supprimée.',
		1			=> 'Au total <span style="font-weight: bold;">1</span> image a été supprimée.',
		2			=> 'Au total <span style="font-weight: bold;">%s</span> images ont été supprimées.',
	),
	'MOVED_IMAGES'		=> array(
		0			=> 'Aucune image n’a été déplacée.',
		1			=> 'Au total <span style="font-weight: bold;">1</span> image a été déplacée.',
		2			=> 'Au total <span style="font-weight: bold;">%s</span> images ont été déplacées.',
	),
	'NO_WAITING_UNAPPROVED_IMAGE'	=> 'Aucune image en attente d’approbation.',
));
