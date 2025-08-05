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
	'ACP_GALLERY_ALBUM_MANAGEMENT'       => 'Оправление на албуми',
	'ACP_GALLERY_ALBUM_PERMISSIONS'      => 'Права',
	'ACP_GALLERY_ALBUM_PERMISSIONS_COPY' => 'Копиране на права',
	'ACP_GALLERY_CONFIGURE_GALLERY'      => 'Настройка на галерията',
	'ACP_GALLERY_LOGS'                   => 'Лог на галерията',
	'ACP_GALLERY_LOGS_EXPLAIN'           => 'Тук виждате всички модераторски действия като одобрение, отхвърляне, заключване, и тн.',
	'ACP_GALLERY_MANAGE_ALBUMS'          => 'Оправление на албуми',
	'ACP_GALLERY_OVERVIEW'               => 'Преглед',

	'GALLERY'                  => 'Галерия',
	'GALLERY_EXPLAIN'          => 'Галерия',
	'GALLERY_HELPLINE_ALBUM'   => 'Изображение: [image]image_id[/image], с този BBCode можете да добавите изборажение към поста си.',
	'GALLERY_POPUP'            => 'Галерия',
	'GALLERY_POPUP_HELPLINE'   => 'Отвори popup където можете да изберете последните си избражения и да добавите нови.',

	// Please do not change the copyright.
	'GALLERY_COPYRIGHT'        => 'Powered by <a href="http://www.anavaro.com/">phpBB Gallery</a> &copy; 2016 <a href="http://www.anavaro.com/">Lucifer</a>',

	// A little line where you can give yourself some credits on the translation.
	'GALLERY_TRANSLATION_INFO' => 'Българският превод на "phpBB Gallery" е направен от <a href="http://www.anavaro.com">Lucifer</a>',

	'IMAGES'                  => 'Изображение',
	'IMG_BUTTON_UPLOAD_IMAGE' => 'Качи изображение',

	'PERSONAL_ALBUM' => 'Личен албум',
	'PHPBB_GALLERY'  => 'phpBB Gallery',

	'TOTAL_IMAGES_SPRINTF' => array(
		0 => 'Общо изображения <strong>0</strong>',
		1 => 'Общо изображения <strong>%d</strong>',
	),
));
