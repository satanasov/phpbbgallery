<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

namespace phpbbgallery\core;

class constants
{
	// GD library
	const GDLIB1 = 1;
	const GDLIB2 = 2;

	// Watermark positions
	const WATERMARK_TOP = 1;
	const WATERMARK_MIDDLE = 2;
	const WATERMARK_BOTTOM = 4;
	const WATERMARK_LEFT = 8;
	const WATERMARK_CENTER = 16;
	const WATERMARK_RIGHT = 32;

	// Additional constants
	const MODULE_DEFAULT_ACP = 31;
	const MODULE_DEFAULT_LOG = 25;
	const MODULE_DEFAULT_UCP = 0;
	const SEARCH_PAGES_NUMBER = 10;
	const THUMBNAIL_INFO_HEIGHT = 16;
}
