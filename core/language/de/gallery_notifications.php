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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'		=> '%2$s hochgeladene Bilder für Freigabe in Album <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Bilder Warten auf die Freigabe',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Approved images',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Images in album <strong>%1$s</strong> were approved',
));
