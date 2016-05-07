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
 * This should be called Constants file
 * I'm going to use it for storing all constants and functions that return them
 * Naming convention (for functions should be classname_constant_name
 */

namespace phpbbgallery\core;

class block
{
	/**
	 * \phpbbgallery\core\album\album
	 *
	 * Constants defining some album properties
	 */
	const PUBLIC_ALBUM		= 0;

	const TYPE_CAT			= 0;
	const TYPE_UPLOAD		= 1;
	const TYPE_CONTEST		= 2;

	const ALBUM_OPEN		= 0;
	const ALBUM_LOCKED		= 1;

	/**
	 * Get locked
	 */
	public function get_album_status_locked()
	{
		return self::ALBUM_LOCKED;
	}

	static public function get_album_public()
	{
		return self::PUBLIC_ALBUM;
	}

	static public function get_album_type_upload()
	{
		return self::TYPE_UPLOAD;
	}

	/**
	 * \phpbbgallery\core\image\image
	 *
	 * Constants defining some image properties
	 */
	/**
	 * Only visible for moderators.
	 */
	const STATUS_UNAPPROVED	= 0;

	/**
	 * Visible for everyone with the i_view-permissions
	 */
	const STATUS_APPROVED	= 1;

	/**
	 * Visible for everyone with the i_view-permissions, but only moderators can comment.
	 */
	const STATUS_LOCKED		= 2;

	/**
	 * Orphan files are only visible for their author, because they're not yet ready uploaded.
	 */
	const STATUS_ORPHAN		= 3;

	/**
	 * Constants regarding the image contest relation
	 */
	const NO_CONTEST = 0;

	/**
	 * The image is element of an open contest. Only moderators can see the user_name of the user.
	 */
	const IN_CONTEST = 1;

	/**
	 * Functions for \phpbbgallery\core\image
	 */

	public function get_image_status_unapproved()
	{
		return self::STATUS_UNAPPROVED;
	}

	public function get_image_status_approved()
	{
		return self::STATUS_APPROVED;
	}

	public function get_image_status_locked()
	{
		return self::STATUS_LOCKED;
	}

	/**
	 * return int orphan status
	 */
	public function get_image_status_orphan()
	{
		return self::STATUS_ORPHAN;
	}

	public function get_no_contest()
	{
		return self::NO_CONTEST;
	}

	public function get_in_contest()
	{
		return self::IN_CONTEST;
	}

	/**
	 * Unspecified (to specify them)
	 */


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
