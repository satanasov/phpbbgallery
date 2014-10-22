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
	public function __construct($mode = false, $display_options = false, $nums = false, $toggle_comments = '', $display_pegas = '')
	{
		$this->set_mode(($mode) ? $mode : (self::MODE_RECENT + self::MODE_RANDOM + self::MODE_COMMENT));
		$this->set_display(($display_options) ? $display_options : (self::DISPLAY_ALBUMNAME + self::DISPLAY_IMAGENAME + self::DISPLAY_IMAGETIME + self::DISPLAY_IMAGEVIEWS + self::DISPLAY_USERNAME + self::DISPLAY_IP));
		$this->set_num(($nums) ? $nums : array(1, 4, 5, 0));
		$this->set_toggle((is_bool($toggle_comments)) ? $toggle_comments : false);
		$this->set_pegas((is_bool($display_pegas)) ? $display_pegas : true);

		/*if (!phpbb_gallery::$loaded)
		{
			phpbb_gallery::init();
		} */

		global $user;
		$user->add_lang_ext('phpbbgallery/core', array('gallery_acp', 'gallery'));

		if (!function_exists('generate_text_for_display'))
		{
			phpbb_gallery_url::_include('message_parser', 'phpbb');
		}
	}

	/**
	* The name of the outer template loop for the imageblock and comments
	* You need to call this on your second code block,
	* when you want to view the module two times with different settings on the same page.
	*/
	private $template_block_images = 'imageblock';
	private $template_block_comments = 'commentrow';
	public function set_template_block_name($image_block, $comment_block = 'commentrow')
	{
		$this->template_block_images = $image_block;
		$this->template_block_comments = $comment_block;
	}

	/**
	* Modes that you want to display on the block.
	*/
	private $mode	= self::MODE_NONE;

	const MODE_NONE		= 0;
	const MODE_RECENT	= 1;
	const MODE_RANDOM	= 2;
	const MODE_COMMENT	= 4;

	/**
	* @param	array	$modes	Array of strings: 'none', 'recent', 'random', 'comment'
	*/
	public function set_modes($modes = array())
	{
		// Reset the mode
		$this->mode = self::MODE_NONE;

		$allowed_modes = array('recent', 'random', 'comment');
		foreach ($allowed_modes as $mode)
		{
			if (in_array($mode, $modes) && !($this->mode & constant("self::MODE_" . strtoupper($mode))))
			{
				$this->mode += constant("self::MODE_" . strtoupper($mode));
			}
		}
	}

	/**
	* @param	int		$new_mode	Integer between 0 and 7 (including bounds)
	*/
	public function set_mode($new_mode = self::MODE_NONE)
	{
		if (is_int($new_mode) && ($new_mode >= 0) && ($new_mode <= 7))
		{
			$this->mode = $new_mode;
		}
	}

	public function get_mode()
	{
		return $this->mode;
	}

	/**
	* Options which details of the images you want to view on the block.
	*/
	private $display		= self::DISPLAY_NONE;

	const DISPLAY_NONE			= 0;
	const DISPLAY_ALBUMNAME		= 1;
	const DISPLAY_COMMENTS		= 2;
	const DISPLAY_IMAGENAME		= 4;
	const DISPLAY_IMAGETIME		= 8;
	const DISPLAY_IMAGEVIEWS	= 16;
	const DISPLAY_USERNAME		= 32;
	const DISPLAY_RATINGS		= 64;
	const DISPLAY_IP			= 128;

	/**
	* @param	array	$modes	Array of strings:
	*							'none', 'albumname', 'comments', 'imagename', 'imagetime', 'imageviews', 'username', 'ratings', 'ip'
	*/
	public function set_display_options($options = array())
	{
		// Reset the mode
		$this->display = self::DISPLAY_NONE;

		$allowed_options = array('albumname', 'comments', 'imagename', 'imagetime', 'imageviews', 'username', 'ratings', 'ip');
		foreach ($allowed_options as $option)
		{
			if (in_array($option, $options) && !($this->display & constant("self::DISPLAY_" . strtoupper($option))))
			{
				$this->display += constant("self::DISPLAY_" . strtoupper($option));
			}
		}
	}

	/**
	* @param	int		$new_mode	Integer between 0 and 255 (including bounds)
	*/
	public function set_display($new_display = self::DISPLAY_NONE)
	{
		if (is_int($new_display) && ($new_display >= 0) && ($new_display <= 255))
		{
			$this->display = $new_display;
		}
	}

	public function get_display()
	{
		return $this->display;
	}

	/**
	* Some integer numbers
	*/
	private $num_rows		= 0;
	private $num_columns	= 0;
	private $num_comments	= 0;
	private $num_contests	= 0;
	private $num_sql_limit	= 0;

	public function set_nums($nums = array())
	{
		$allowed_nums = array('rows', 'columns', 'comments', 'contests');
		foreach ($allowed_nums as $num)
		{
			if (isset($nums[$num]) && is_int($nums[$num]))
			{
				$variable_name = 'num_' . $num;
				$this->$variable_name = $nums[$num];
			}
		}

		$this->num_sql_limit	= $this->num_rows * $this->num_columns;
	}

	/**
	* @param	array	$nums	Array of ints for:
	*							# of rows, # of columns, # of comments, # of contests
	*/
	public function set_num($nums)
	{
		if (sizeof($nums) == 4)
		{
			$this->num_rows			= (int) $nums[0];
			$this->num_columns		= (int) $nums[1];
			$this->num_comments		= (int) $nums[2];
			$this->num_contests		= (int) $nums[3];
			$this->num_sql_limit	= $this->num_rows * $this->num_columns;
		}
	}

	/**
	* Option to toggle or display the comments by default.
	*/
	private $toggle_comments = false;

	public function set_toggle($new_toggle)
	{
		if (is_bool($new_toggle) || ($new_toggle == 0) || ($new_toggle == 1))
		{
			$this->toggle_comments = (bool) $new_toggle;
		}
	}

	/**
	* Array of albums the images and comments are pulled from.
	* Empty array means all albums.
	*/
	private $albums = array();
	private $include_pegas = true;

	public function add_albums($album_id)
	{
		if (is_array($album_id))
		{
			$this->albums = array_unique(array_merge($this->albums, array_map('intval', $album_id)));
		}
		else if (is_int($album_id) && !in_array($album_id, $this->albums))
		{
			$this->albums[] = $album_id;
		}
	}
	public function set_pegas($new_pegas)
	{
		if (is_bool($new_pegas) || ($new_pegas == 0) || ($new_pegas == 1))
		{
			$this->include_pegas = (bool) $new_pegas;
		}
	}

	public function clear_albums()
	{
		$this->albums = array();
	}

	public function get_albums()
	{
		return $this->albums;
	}
	public function get_pegas()
	{
		return $this->include_pegas;
	}

	/**
	* Array of users the images and comments are pulled from.
	* Empty array means all users.
	*/
	private $users = array();

	public function add_users($user_id)
	{
		if (is_array($user_id))
		{
			$this->users = array_unique(array_merge($this->users, array_map('intval', $user_id)));
		}
		else if (is_int($user_id) && !in_array($user_id, $this->users))
		{
			$this->users[] = $user_id;
		}
	}

	public function clear_users()
	{
		$this->users = array();
	}

	public function get_users()
	{
		return $this->users;
	}

	/**
	* Wrapper-function for the total display
	*/
	public function display()
	{
		$this->get_album_permissions();
		$this->get_image_ids();
		$this->get_image_data();
		$this->display_images();
		$this->display_comments();
	}

	/**
	* Prepare sql_where_auth for the queries
	*/
	private $auth_moderate = array();
	private $auth_view = array();
	private $auth_comments = array();
	private $sql_where_auth = '';

	private function get_album_permissions()
	{
		global $db;

		$albums_is_empty = !empty($this->albums);

		$this->auth_moderate = phpbb_gallery::$auth->acl_album_ids('m_status', 'array', !$albums_is_empty, $this->get_pegas());
		if ($albums_is_empty)
		{
			$this->auth_moderate = array_intersect($this->auth_moderate, $this->albums);
		}
		$this->auth_view = array_diff(phpbb_gallery::$auth->acl_album_ids('i_view', 'array', !$albums_is_empty, $this->get_pegas()), $this->auth_moderate);
		if ($albums_is_empty)
		{
			$this->auth_view = array_intersect($this->auth_view, $this->albums);
		}
		if (phpbb_gallery_config::get('allow_comments') && ($this->mode & self::MODE_COMMENT) && $this->num_comments)
		{
			$this->auth_comments = phpbb_gallery::$auth->acl_album_ids('c_read', 'array', !$albums_is_empty, $this->get_pegas());
			if ($albums_is_empty)
			{
				$this->auth_comments = array_intersect($this->auth_comments, $this->albums);
			}
		}

		$this->sql_where_auth = '(';
		$this->sql_where_auth .= ((!empty($this->auth_view)) ? '(' . $db->sql_in_set('image_album_id', $this->auth_view) . ' AND image_status <> ' . phpbb_gallery_image::STATUS_UNAPPROVED . ((!empty($this->users)) ? ' AND image_contest = ' . phpbb_gallery_image::NO_CONTEST : '') . ')' : '');
		$this->sql_where_auth .= ((!empty($this->auth_moderate)) ? ((!empty($this->auth_view)) ? ' OR ' : '') . '(' . $db->sql_in_set('image_album_id', $this->auth_moderate, false, true) . ')' : '');

		if ($this->sql_where_auth == '(')
		{
			// User does not have permissions for any album, so we jsut return with 1=0 so there is no result:
			$this->sql_where_auth = '0 = 1';
			return;
		}
		$this->sql_where_auth .= (!empty($this->users)) ? ') AND ' . $db->sql_in_set('image_user_id', $this->users) : ')';
	}

	/**
	* Get image_ids to display
	*/
	private $images = array();
	private $recent_images = array();
	private $random_images = array();
	private $contest_images = array();

	private function get_image_ids()
	{
		global $db;

		$this->images = $this->recent_images = $this->random_images = $this->contest_images = array();
		// First step: grab all the IDs we are going to display ...
		if ($this->mode & self::MODE_RECENT)
		{
			$sql = 'SELECT image_id
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE ' . $this->sql_where_auth . '
					AND image_status <> ' . phpbb_gallery_image::STATUS_ORPHAN . '
				ORDER BY image_time DESC';
			$result = $db->sql_query_limit($sql, $this->num_sql_limit);

			while ($row = $db->sql_fetchrow($result))
			{
				$this->images[] = $row['image_id'];
				$this->recent_images[] = $row['image_id'];
			}
			$db->sql_freeresult($result);
		}
		if ($this->mode & self::MODE_RANDOM)
		{
			switch ($db->sql_layer)
			{
				case 'postgres':
					$random_sql = 'RANDOM()';
				break;
				case 'mssql':
				case 'mssql_odbc':
					$random_sql = 'NEWID()';
				break;
				default:
					$random_sql = 'RAND()';
				break;
			}

			$sql = 'SELECT image_id
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE ' . $this->sql_where_auth . '
					AND image_status <> ' . phpbb_gallery_image::STATUS_ORPHAN . '
				ORDER BY ' . $random_sql;
			$result = $db->sql_query_limit($sql, $this->num_sql_limit);

			while ($row = $db->sql_fetchrow($result))
			{
				$this->images[] = $row['image_id'];
				$this->random_images[] = $row['image_id'];
			}
			$db->sql_freeresult($result);
		}
		if ($this->num_contests)
		{
			$sql_array = array(
				'SELECT'		=> 'c.*, a.album_name',
				'FROM'			=> array(GALLERY_CONTESTS_TABLE => 'c'),

				'LEFT_JOIN'		=> array(
					array(
						'FROM'		=> array(GALLERY_ALBUMS_TABLE => 'a'),
						'ON'		=> 'a.album_id = c.contest_album_id',
					),
				),

				'WHERE'			=> $db->sql_in_set('c.contest_album_id', array_unique(array_merge($this->auth_view, $this->auth_moderate)), false, true) . ' AND c.contest_marked = ' . phpbb_gallery_image::NO_CONTEST,
				'ORDER_BY'		=> 'c.contest_start + c.contest_end DESC',
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query_limit($sql, $this->num_contests);

			while ($row = $db->sql_fetchrow($result))
			{
				$this->images[] = $row['contest_first'];
				$this->images[] = $row['contest_second'];
				$this->images[] = $row['contest_third'];
				$this->contest_images[$row['contest_id']] = array(
					'album_id'		=> $row['contest_album_id'],
					'album_name'	=> $row['album_name'],
					'images'		=> array($row['contest_first'], $row['contest_second'], $row['contest_third'])
				);
			}
			$db->sql_freeresult($result);
		}
		$this->images = array_unique($this->images);
	}

	/**
	* Query the image-table to get the data.
	*/
	private $images_data = array();
	private function get_image_data()
	{
		if (!empty($this->images))
		{
			global $db;

			$sql_array = array(
				'SELECT'		=> 'i.*, a.album_name, a.album_status, a.album_id, a.album_user_id',
				'FROM'			=> array(GALLERY_IMAGES_TABLE => 'i'),

				'LEFT_JOIN'		=> array(
					array(
						'FROM'		=> array(GALLERY_ALBUMS_TABLE => 'a'),
						'ON'		=> 'i.image_album_id = a.album_id',
					),
				),

				'WHERE'			=> $db->sql_in_set('i.image_id', $this->images, false, true),
				'ORDER_BY'		=> 'i.image_time DESC',
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$this->images_data[$row['image_id']] = $row;
			}
			$db->sql_freeresult($result);
		}
	}

	/**
	* Put the images into the template.
	*/
	private function display_images()
	{
		global $template, $user;

		if (!empty($this->recent_images))
		{
			$num = 0;
			$template->assign_block_vars($this->template_block_images, array(
				'U_BLOCK'			=> phpbb_gallery_url::append_sid('search', 'search_id=recent'),
				'BLOCK_NAME'		=> $user->lang['RECENT_IMAGES'],
				'S_COL_WIDTH'		=> (100 / $this->num_columns) . '%',
				'S_COLS'			=> $this->num_columns,
			));
			foreach ($this->recent_images as $image)
			{
				if (($num % $this->num_columns) == 0)
				{
					$template->assign_block_vars($this->template_block_images . '.imagerow', array());
				}
				phpbb_gallery_image::assign_block($this->template_block_images . '.imagerow.image', $this->images_data[$image], $this->images_data[$image]['album_status'], $this->get_display(), $this->images_data[$image]['album_user_id']);
				$num++;
			}
			while (($num % $this->num_columns) > 0)
			{
				$template->assign_block_vars($this->template_block_images . '.imagerow.no_image', array());
				$num++;
			}
		}

		if (!empty($this->random_images))
		{
			$num = 0;
			$template->assign_block_vars($this->template_block_images, array(
				'U_BLOCK'			=> phpbb_gallery_url::append_sid('search', 'search_id=random'),
				'BLOCK_NAME'		=> $user->lang['RANDOM_IMAGES'],
				'S_COL_WIDTH'		=> (100 / $this->num_columns) . '%',
				'S_COLS'			=> $this->num_columns,
			));
			foreach ($this->random_images as $image)
			{
				if (($num % $this->num_columns) == 0)
				{
					$template->assign_block_vars($this->template_block_images . '.imagerow', array());
				}
				phpbb_gallery_image::assign_block($this->template_block_images . '.imagerow.image', $this->images_data[$image], $this->images_data[$image]['album_status'], $this->get_display(), $this->images_data[$image]['album_user_id']);
				$num++;
			}
			while (($num % $this->num_columns) > 0)
			{
				$template->assign_block_vars($this->template_block_images . '.imagerow.no_image', array());
				$num++;
			}
		}

		if (!empty($this->contest_images))
		{
			foreach ($this->contest_images as $contest => $contest_data)
			{
				$num = 0;
				$template->assign_block_vars($this->template_block_images, array(
					'U_BLOCK'			=> phpbb_gallery_url::append_sid('album', 'album_id=' . $contest_data['album_id'] . '&amp;sk=ra&amp;sd=d'),
					'BLOCK_NAME'		=> sprintf($user->lang['CONTEST_WINNERS_OF'], $contest_data['album_name']),
					'S_CONTEST_BLOCK'	=> true,
					'S_COL_WIDTH'		=> '33%',
					'S_COLS'			=> 3,
				));
				foreach ($contest_data['images'] as $image)
				{
					if (($num % phpbb_gallery_contest::NUM_IMAGES) == 0)
					{
						$template->assign_block_vars($this->template_block_images . '.imagerow', array());
					}
					if (!empty($this->images_data[$image]))
					{
						phpbb_gallery_image::assign_block($this->template_block_images . '.imagerow.image', $this->images_data[$image], $this->images_data[$image]['album_status'], $this->get_display(), $this->images_data[$image]['album_user_id']);
						$num++;
					}
				}
				while (($num % phpbb_gallery_contest::NUM_IMAGES) > 0)
				{
					$template->assign_block_vars($this->template_block_images . '.imagerow.no_image', array());
					$num++;
				}
			}
		}

		$template->assign_vars(array(
			'S_THUMBNAIL_SIZE'	=> phpbb_gallery_config::get('thumbnail_height') + 20 + ((phpbb_gallery_config::get('thumbnail_infoline')) ? phpbb_gallery_constants::THUMBNAIL_INFO_HEIGHT : 0),
		));
	}

	/**
	* Query the comments and put them into the template.
	*/
	private function display_comments()
	{
		if (empty($this->auth_comments))
		{
			return;
		}

		global $auth, $db, $template, $user;
		$user->add_lang('viewtopic');

		$sql_array = array(
			'SELECT'		=> 'c.*, i.*',
			'FROM'			=> array(GALLERY_COMMENTS_TABLE => 'c'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(GALLERY_IMAGES_TABLE => 'i'),
					'ON'		=> 'c.comment_image_id = i.image_id',
				),
			),

			'WHERE'			=> $this->sql_where_auth . ' AND ' . $db->sql_in_set('i.image_album_id', $this->auth_comments, false, true),
			'ORDER_BY'		=> 'c.comment_id DESC',
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $this->num_comments);

		while ($row = $db->sql_fetchrow($result))
		{
			$image_id = (int) $row['image_id'];
			$album_id = (int) $row['image_album_id'];

			$template->assign_block_vars($this->template_block_comments, array(
				'U_COMMENT'		=> phpbb_gallery_url::append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id") . '#comment_' . $row['comment_id'],
				'COMMENT_ID'	=> $row['comment_id'],
				'TIME'			=> $user->format_date($row['comment_time']),
				'TEXT'			=> generate_text_for_display($row['comment'], $row['comment_uid'], $row['comment_bitfield'], 7),
				'U_DELETE'		=> (phpbb_gallery::$auth->acl_check('m_comments', $album_id) || (phpbb_gallery::$auth->acl_check('c_delete', $album_id) && ($row['comment_user_id'] == $user->data['user_id']) && $user->data['is_registered'])) ? phpbb_gallery_url::append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=delete&amp;comment_id=" . $row['comment_id']) : '',
				'U_QUOTE'		=> (phpbb_gallery::$auth->acl_check('c_post', $album_id)) ? phpbb_gallery_url::append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=add&amp;comment_id=" . $row['comment_id']) : '',
				'U_EDIT'		=> (phpbb_gallery::$auth->acl_check('m_comments', $album_id) || (phpbb_gallery::$auth->acl_check('c_edit', $album_id) && ($row['comment_user_id'] == $user->data['user_id']) && $user->data['is_registered'])) ? phpbb_gallery_url::append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=edit&amp;comment_id=" . $row['comment_id']) : '',
				'U_INFO'		=> ($auth->acl_get('a_')) ? phpbb_gallery_url::append_sid('mcp', 'mode=whois&amp;ip=' . $row['comment_user_ip']) : '',

				'UC_THUMBNAIL'			=> phpbb_gallery_image::generate_link('thumbnail', phpbb_gallery_config::get('link_thumbnail'), $row['image_id'], $row['image_name'], $row['image_album_id']),
				'UC_IMAGE_NAME'			=> phpbb_gallery_image::generate_link('image_name', phpbb_gallery_config::get('link_image_name'), $row['image_id'], $row['image_name'], $row['image_album_id']),
				'IMAGE_AUTHOR'			=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
				'IMAGE_TIME'			=> $user->format_date($row['image_time']),

				'POST_AUTHOR_FULL'		=> get_username_string('full', $row['comment_user_id'], $row['comment_username'], $row['comment_user_colour']),
				'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['comment_user_id'], $row['comment_username'], $row['comment_user_colour']),
				'POST_AUTHOR'			=> get_username_string('username', $row['comment_user_id'], $row['comment_username'], $row['comment_user_colour']),
				'U_POST_AUTHOR'			=> get_username_string('profile', $row['comment_user_id'], $row['comment_username'], $row['comment_user_colour']),
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_COMMENTS'	=> true,

			'DELETE_IMG'		=> $user->img('icon_post_delete', 'DELETE_COMMENT'),
			'EDIT_IMG'			=> $user->img('icon_post_edit', 'EDIT_COMMENT'),
			'QUOTE_IMG'			=> $user->img('icon_post_quote', 'QUOTE_COMMENT'),
			'INFO_IMG'			=> $user->img('icon_post_info', 'IP'),
			'MINI_POST_IMG'		=> $user->img('icon_post_target', 'COMMENT'),
			'PROFILE_IMG'		=> $user->img('icon_user_profile', 'READ_PROFILE'),
			'COLLAPSE_COMMENTS'	=> $this->toggle_comments,
		));
	}
}
