<?php
/**
 * phpBB Gallery - ACP Core Extension [Russian Translation]
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
		1 => 'Вы должны удалить расширение: <br /><strong>%s</strong><br /> перед удалением основного расширения.',
		2 => 'Вы должны удалить расширения: <br /><strong>%s</strong><br /> перед удалением основного расширения.',
	),
));
