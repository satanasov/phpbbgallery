<?php
/**
 * phpBB Gallery - ACP Core Extension [German Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator franki <https://dieahnen.de/ahnenforum/>
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
		1 => 'Sie müssen die Erweiterung deinstallieren: <br /><strong>%s</strong><br /> bevor Sie die Kern-Erweiterung deinstallieren können.',
		2 => 'Sie müssen die Erweiterungen deinstallieren: <br /><strong>%s</strong><br /> bevor Sie die Kern-Erweiterung deinstallieren können.',
	),
));
