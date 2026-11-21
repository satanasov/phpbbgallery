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
	'GALLERY_SUB_EXT_UNINSTALL' => array(
		1 => 'È necessario disinstallare l’estensione: <br /><strong>%s</strong><br /> prima di disinstallare l’estensione principale.',
		2 => 'È necessario disinstallare le estensioni: <br /><strong>%s</strong><br /> prima di disinstallare l’estensione principale.',
	),
));
