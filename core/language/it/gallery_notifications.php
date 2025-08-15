<?php
/**
 * phpBB Gallery - ACP Core Extension [Italian Translation]
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s immagini caricate in attesa di approvazione nell’album <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Immagini in attesa di approvazione',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Immagini approvate',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Le immagini nell’album <strong>%1$s</strong> sono state approvate',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_NOT_APPROVED'	=> 'Immagini non approvate',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_NOT_APPROVED'		=> 'Le immagini nell\'album <strong>%1$s</strong> non sono state approvate',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Nuove immagini',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'Nuove immagini sono state caricate nell’album <strong>%1$s</strong>',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Nuovi commenti',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> ha commentato su un’immagine che stai controllando',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Nuova segnalazione immagine',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> ha segnalato un’immagine',
));
