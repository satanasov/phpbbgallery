<?php
/**
*
* Gallery Notifications [Dutch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 Lucifer Lucifer@anavaro.com http://www.anavaro.com
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s heeft een afbeelding geüpload naar het album <strong>%1$s</strong> die wacht op goedkeuring ',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Afbeeldingen wachtend op goedkeuring',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Afbeelding goedgekeurd',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'De afbeeldingen in het album <strong>%1$s</strong> zijn goedgekeurd',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Nieuwe afbeeldingen',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'Er zijn nieuwe afbeeldingen geüpload naar het album <strong>%1$s</strong>',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Nieuwe reactie',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> heeft gereageerd op een afbeelding waarop je geabboneerd bent',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Nieuwe melding',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> heeft een afbeelding gemeld',
));
