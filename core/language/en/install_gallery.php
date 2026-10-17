<?php
/**
 * phpBB Gallery - ACP Core Extension
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
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
		1 => 'You must uninstall the extension: <br /><strong>%s</strong><br /> before uninstalling the core extension.',
		2 => 'You must uninstall the extensions: <br /><strong>%s</strong><br /> before uninstalling the core extension.',
	),
));
