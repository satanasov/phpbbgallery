<?php
/**
*
* @package Gallery -  Info UCP Extension [French]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* @translator fr (c) pokyto aka le.poke http://www.lestontonsfraggers.com inspired by darky - http://www.foruminfopc.fr/ and Team http://www.phpbb-fr.com/
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
	'UCP_GALLERY'						=> 'Galerie d’images',
	'UCP_GALLERY_PERSONAL_ALBUMS'		=> 'Gérer les albums personnels',
	'UCP_GALLERY_SETTINGS'				=> 'Paramètres personnels',
	'UCP_GALLERY_WATCH'					=> 'Gérer les abonnements',
));
