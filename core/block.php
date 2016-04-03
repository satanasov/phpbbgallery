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

class block
{
	/**
	 * Modes that you want to display on the block.
	 */

	const MODE_NONE = 0;
	const MODE_RECENT = 1;
	const MODE_RANDOM = 2;
	const MODE_COMMENT = 4;

	/**
	 * Options which details of the images you want to view on the block.
	 */
	const DISPLAY_NONE = 0;
	const DISPLAY_ALBUMNAME = 1;
	const DISPLAY_COMMENTS = 2;
	const DISPLAY_IMAGENAME = 4;
	const DISPLAY_IMAGETIME = 8;
	const DISPLAY_IMAGEVIEWS = 16;
	const DISPLAY_USERNAME = 32;
	const DISPLAY_RATINGS = 64;
	const DISPLAY_IP = 128;
}