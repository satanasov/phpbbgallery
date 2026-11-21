<?php
/**
 * phpBB Gallery - ACP Core Extension [Bulgarian Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator Lucifer <https://www.anavaro.com>
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
	'GALLERY_SUB_EXT_UNINSTALL' => array(
		1 => 'Трябва да деинсталирате разширението: <br /><strong>%s</strong><br /> преди да деинсталирате основното разширение.',
		2 => 'Трябва да деинсталирате разширенията: <br /><strong>%s</strong><br /> преди да деинсталирате основното разширение.',
	),
));
