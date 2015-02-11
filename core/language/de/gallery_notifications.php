<?php
/**
*
* Gallery Notifications [German]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 Lucifer Lucifer@anavaro.com http://www.anavaro.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*
* Übersetzt von franki (http://dieahnen.de/ahnenforum/)
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'		=> '%2$s hochgeladene Bilder warten auf Freigabe in Album <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Bilder Warten auf Freigabe',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Freigegebene Bilder',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Bilder im <strong>%1$s</strong> Album wurden freigegeben',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Neue Bilder',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'Neue Bilder wurden ins <strong>%1$s</strong> Album hochgeladen',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Neue Kommentare',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> Kommentare zu dem Bild welches Du gerade siehst',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Neuer Bild-Report',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> '<strong>%1$s</strong> gemeldetes Bild',
));
