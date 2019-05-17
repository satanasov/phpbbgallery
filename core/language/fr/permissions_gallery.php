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

// Adding the permissions
$lang = array_merge($lang, array(
	'ACL_A_GALLERY_MANAGE'		=> 'Peut gérer les paramètres de la galerie photos.',
	'ACL_A_GALLERY_ALBUMS'		=> 'Peut gérer (ajouter, modifier & supprimer) les albums et les permissions.',
));
