<?php
/**
*
* phpBB Gallery. An extension for the phpBB Forum Software package.
* French translation by pokyto aka le.poke http://www.lestontonsfraggers.com (inspired by darky http://www.foruminfopc.fr/ and http://www.phpbb-fr.com/) & Galixte (http://www.galixte.com)
*
* @copyright (c) 2012 nickvergessen <http://www.flying-bits.org/> - 2018 Stanislav Atanasov <http://www.anavaro.com>
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
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
	$lang = array();
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
	'NOTIFICATION_PHPBBGALLERY_IMAGE_FOR_APPROVAL'	=> '%2$s images chargées sont en attente d’approbation dans l’album <strong>%1$s</strong>',
	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_FOR_APPROVE'	=> 'Images en attente d’approbation',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_APPROVED'	=> 'Images approuvées',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_APPROVED'		=> 'Les images de l’album <strong>%1$s</strong> ont été approuvées',

	'NOTIFICATION_TYPE_PHPBBGALLERY_IMAGE_NOT_APPROVED'	=> 'Images non approuvées',
	'NOTIFICATION_PHPBBGALLERY_IMAGE_NOT_APPROVED'		=> 'Les images de l’album <strong>%1$s</strong> n’ont pas été approuvées',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_IMAGE'	=> 'Nouvelles images',
	'NOTIFICATION_PHPBBGALLERY_NEW_IMAGE'		=> 'De nouvelles images ont été chargées dans l’album <strong>%1$s</strong>',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT'	=> 'Nouveaux commentaires',
	'NOTIFICATION_PHPBBGALLERY_NEW_COMMENT'			=> '<strong>%1$s</strong> a commenté une image que vous suivez',

	'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT'	=> 'Nouvelle image rapportée',
	'NOTIFICATION_PHPBBGALLERY_NEW_REPORT'			=> 'L’image <strong>%1$s</strong> a été rapportée',
));
