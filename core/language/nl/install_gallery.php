<?php
/**
 * phpBB Gallery - ACP Core Extension [Dutch Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator Dutch Translators <https://github.com/dutch-translators>
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
		1 => 'U moet de extensie: <br /><strong>%s</strong><br /> verwijderen voordat u de kern-extensie verwijdert.',
		2 => 'U moet de extensies: <br /><strong>%s</strong><br /> verwijderen voordat u de kern-extensie verwijdert.',
	),
));
