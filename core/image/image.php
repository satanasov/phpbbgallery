<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\image;

class image
{
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
	* construct
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\album\album $album,
								\phpbbgallery\core\config $gallery_config, \phpbb\controller\helper $helper, 
								$table_images)
	{
		$this->db = $db;
		$this->user = $user;
		$this->gallery_auth = $gallery_auth;
		$this->album = $album;
		$this->gallery_config = $gallery_config;
		$this->helper = $helper; 
		$this->table_images = $table_images;
	}
	/**
	* return int orphan status
	*/
	public function get_status_orphan()
	{
		return 3;
	}

	public function get_status_unaproved()
	{
		return 0;
	}

	public function get_status_aproved()
	{
		return 1;
	}

	public function get_status_locked()
	{
		return 2;
	}

	public function get_new_author_info($username)
	{
		// Who is the new uploader?
		if (!$username)
		{
			return false;
		}
		$user_id = 0;
		if ($username)
		{
			if (!function_exists('user_get_id_name'))
			{
				$phpbb_ext_gallery->url->_include('functions_user', 'phpbb');
			}
			user_get_id_name($user_id, $username);
		}

		if (empty($user_id))
		{
			return false;
		}

		$sql = 'SELECT username, user_colour, user_id
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id[0];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/**
	* Delete an image completly.
	*
	* @param	array		$images		Array with the image_id(s)
	* @param	array		$filenames	Array with filenames for the image_ids. If a filename is missing it's queried from the database.
	*									Format: $image_id => $filename
	* @param	bool		$skip_files	If set to true, we won't try to delete the source files.
	*/
	public function delete_images($images, $filenames = array(), $resync_albums = true, $skip_files = false)
	{
		global $phpbb_container, $table_prefix, $phpbb_dispatcher, $db;
		$image_tools = $phpbb_container->get('phpbbgallery.core.file.tool');
		$album = $phpbb_container->get('phpbbgallery.core.album');
		$phpbb_gallery_image_rating = new \phpbbgallery\core\rating($images);
		$phpbb_gallery_comment = $phpbb_container->get('phpbbgallery.core.comment');
		$phpbb_gallery_notification = new \phpbbgallery\core\notification();
		$phpbb_gallery_report = new \phpbbgallery\core\report();
		$phpbb_gallery_contest = new \phpbbgallery\core\contest();
		if (empty($images))
		{
			return;
		}

		if (!$skip_files)
		{
			// Delete the files from the disc...
			$need_filenames = array();
			foreach ($images as $image)
			{
				if (!isset($filenames[$image]))
				{
					$need_filenames[] = $image;
				}
			}
			$filenames = array_merge($filenames, self::get_filenames($need_filenames));
			$image_tools->delete($filenames);
		}

		// Delete the ratings...
		$phpbb_gallery_image_rating->delete_ratings($images);
		$phpbb_gallery_comment->delete_images($images);
		$phpbb_gallery_notification->delete_images($images);
		$phpbb_gallery_report->delete_images($images);

		$vars = array('images', 'filenames');
		extract($phpbb_dispatcher->trigger_event('gallery.core.image.delete_images', compact($vars)));

		$sql = 'SELECT image_album_id, image_contest_rank
			FROM ' . $table_prefix . 'gallery_images
			WHERE ' . $db->sql_in_set('image_id', $images) . '
			GROUP BY image_album_id, image_contest_rank';
		$result = $db->sql_query($sql);
		$resync_album_ids = $resync_contests = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['image_contest_rank'])
			{
				$resync_contests[] = (int) $row['image_album_id'];
			}
			$resync_album_ids[] = (int) $row['image_album_id'];
		}
		$db->sql_freeresult($result);
		$resync_contests = array_unique($resync_contests);
		$resync_album_ids = array_unique($resync_album_ids);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_images
			WHERE ' . $db->sql_in_set('image_id', $images);
		$db->sql_query($sql);

		// The images need to be deleted, before we grab the new winners.
		$phpbb_gallery_contest->resync_albums($resync_contests);
		if ($resync_albums)
		{
			foreach ($resync_album_ids as $album_id)
			{
				$album->update_info($album_id);
			}
		}

		return true;
	}

	/**
	* Get the real filenames, so we can load/delete/edit the image-file.
	*
	* @param	mixed		$images		Array or integer with the image_id(s)
	* @return	array		Format: $image_id => $filename
	*/
	public function get_filenames($images)
	{
		if (empty($images))
		{
			return array();
		}

		$filenames = array();
		$sql = 'SELECT image_id, image_filename
			FROM ' . GALLERY_IMAGES_TABLE . '
			WHERE ' . $db->sql_in_set('image_id', $images);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$filenames[(int) $row['image_id']] = $row['image_filename'];
		}
		$db->sql_freeresult($result);

		return $filenames;
	}

	/**
	* Generate link to image
	*
	* @param	string	$content	what's in the link: image_name, thumbnail, fake_thumbnail, medium or lastimage_icon
	* @param	string	$mode		where does the link leed to: highslide, lytebox, lytebox_slide_show, image_page, image, none
	* @param	int		$image_id
	* @param	string	$image_name
	* @param	int		$album_id
	* @param	bool	$is_gif		we need to know whether we display a gif, so we can use a better medium-image
	* @param	bool	$count		shall the image-link be counted as view? (Set to false from image_page.php to deny double increment)
	* @param	string	$additional_parameters		additional parameters for the url, (starting with &amp;)
	*/
	public function generate_link($content, $mode, $image_id, $image_name, $album_id, $is_gif = false, $count = true, $additional_parameters = '', $next_image = 0)
	{
		global $user, $phpbb_root_path, $phpEx;
		global $phpbb_ext_gallery;//@todo:

		$phpbb_ext_gallery_url = new \phpbbgallery\core\url($phpbb_root_path, $phpEx);

		$image_page_url = $this->helper->route('phpbbgallery_image', array('image_id' => $image_id));
		//$image_page_url = $phpbb_ext_gallery_url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id{$additional_parameters}");
		//$image_url = $phpbb_ext_gallery_url->append_sid('image', "album_id=$album_id&amp;image_id=$image_id{$additional_parameters}" . ((!$count) ? '&amp;view=no_count' : ''));
		$image_url = $phpbb_ext_gallery_url->show_image($image_id, 'medium');
		$thumb_url = $phpbb_ext_gallery_url->show_image($image_id, 'mini');
		$medium_url = $phpbb_ext_gallery_url->show_image($image_id, 'medium');
		//$medium_url = $phpbb_ext_gallery_url->append_sid('image', "mode=medium&amp;album_id=$album_id&amp;image_id=$image_id{$additional_parameters}");
		switch ($content)
		{
			case 'image_name':
				$shorten_image_name = (utf8_strlen(htmlspecialchars_decode($image_name)) > $this->gallery_config->get('shortnames') + 3) ? (utf8_substr(htmlspecialchars_decode($image_name), 0, $this->gallery_config->get('shortnames')) . '...') : ($image_name);
				$content = '<span style="font-weight: bold;">' . $shorten_image_name . '</span>';
			break;
			case 'image_name_unbold':
				$shorten_image_name = (utf8_strlen(htmlspecialchars_decode($image_name)) > $this->gallery_config->get('shortnames') + 3) ? (utf8_substr(htmlspecialchars_decode($image_name), 0, $this->gallery_config->get('shortnames')) . '...') : ($image_name);
				$content = $shorten_image_name;
			break;
			case 'thumbnail':
				$content = '<img src="{U_THUMBNAIL}" alt="{IMAGE_NAME}" title="{IMAGE_NAME}" />';
				$content = str_replace(array('{U_THUMBNAIL}', '{IMAGE_NAME}'), array($thumb_url, $image_name), $content);
			break;
			case 'fake_thumbnail':
				$content = '<img src="{U_THUMBNAIL}" alt="{IMAGE_NAME}" title="{IMAGE_NAME}" style="max-width: {FAKE_THUMB_SIZE}px; max-height: {FAKE_THUMB_SIZE}px;" />';
				$content = str_replace(array('{U_THUMBNAIL}', '{IMAGE_NAME}', '{FAKE_THUMB_SIZE}'), array($thumb_url, $image_name, $this->gallery_config->get('mini_thumbnail_size')), $content);
			break;
			case 'medium':
				$content = '<img src="{U_MEDIUM}" alt="{IMAGE_NAME}" title="{IMAGE_NAME}" />';
				$content = str_replace(array('{U_MEDIUM}', '{IMAGE_NAME}'), array($medium_url, $image_name), $content);
				//cheat for animated/transparent gifs
				if ($is_gif)
				{
					$content = '<img src="{U_MEDIUM}" alt="{IMAGE_NAME}" title="{IMAGE_NAME}" style="max-width: {MEDIUM_WIDTH_SIZE}px; max-height: {MEDIUM_HEIGHT_SIZE}px;" />';
					$content = str_replace(array('{U_MEDIUM}', '{IMAGE_NAME}', '{MEDIUM_HEIGHT_SIZE}', '{MEDIUM_WIDTH_SIZE}'), array($image_url, $image_name, $this->gallery_config->get('medium_height'), $this->gallery_config->get('medium_width')), $content);
				}
			break;
			case 'lastimage_icon':
				$content = $user->img('icon_topic_latest', 'VIEW_LATEST_IMAGE');
			break;
		}

		$url = $image_page_url;

		switch ($mode)
		{
			case 'image_page':
				$tpl = '<a href="{IMAGE_URL}" title="{IMAGE_NAME}">{CONTENT}</a>';
			break;
			case 'image_page_next':
				$tpl = '<a href="{IMAGE_URL}" title="{IMAGE_NAME}" class="right-box right">{CONTENT}</a>';
			break;
			case 'image_page_prev':
				$tpl = '<a href="{IMAGE_URL}" title="{IMAGE_NAME}" class="left-box left">{CONTENT}</a>';
			break;
			case 'image':
				$url = $image_url;
				$tpl = '<a href="{IMAGE_URL}" title="{IMAGE_NAME}">{CONTENT}</a>';
			break;
			case 'none':
				$tpl = '{CONTENT}';
			break;
			case 'next':
				if ($next_image)
				{
					$url = $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$next_image{$additional_parameters}");
					$tpl = '<a href="{IMAGE_URL}" title="{IMAGE_NAME}">{CONTENT}</a>';
				}
				else
				{
					$tpl = '{CONTENT}';
				}
			break;
			default:
				$url = $image_url;
				global $phpbb_dispatcher;

				$tpl = '{CONTENT}';

				$vars = array('mode', 'tpl');
				extract($phpbb_dispatcher->trigger_event('gallery.image.generate_link', compact($vars)));//@todo: Correctly identify the event
			break;
		}

		return str_replace(array('{IMAGE_URL}', '{IMAGE_NAME}', '{CONTENT}'), array($url, $image_name, $content), $tpl);
	}

	/**
	* Handle user- & total image_counter
	*
	* @param	array	$image_id_ary	array with the image_ids which changed their status
	* @param	bool	$add			are we adding or removing the images
	* @param	bool	$readd			is it possible that there are images which aren't really changed
	*/
	public function handle_counter($image_id_ary, $add, $readd = false)
	{
		global $db, $phpbb_ext_gallery, $table_prefix, $phpbb_container;

		if (empty($image_id_ary))
		{
			return;
		}

		
		$image_user = $phpbb_container->get('phpbbgallery.core.user');

		$num_images = $num_comments = 0;
		$sql = 'SELECT SUM(image_comments) comments
			FROM ' . $table_prefix . 'gallery_images
			WHERE image_status ' . (($readd) ? '=' : '<>') . ' ' . self::STATUS_UNAPPROVED . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary) . '
			GROUP BY image_user_id';
		$result = $db->sql_query($sql);
		$num_comments = $db->sql_fetchfield('comments');
		$db->sql_freeresult($result);

		$sql = 'SELECT COUNT(image_id) images, image_user_id
			FROM ' . $table_prefix . 'gallery_images
			WHERE image_status ' . (($readd) ? '=' : '<>') . ' ' . self::STATUS_UNAPPROVED . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary) . '
			GROUP BY image_user_id';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$sql_ary = array(
				'user_id'				=> (int) $row['image_user_id'],
				'user_images'			=> (int) $row['images'],
			);
			//@todo: phpbb_gallery_hookup::add_image($row['image_user_id'], (($add) ? $row['images'] : 0 - $row['images']));

			$num_images = $num_images + $row['images'];

			$image_user->set_user_id((int) $row['image_user_id'], false);
			$image_user->update_images((($add) ? $row['images'] : 0 - $row['images']));
		}
		$db->sql_freeresult($result);

		if ($add)
		{
			$this->gallery_config->inc('num_images', $num_images);
			$this->gallery_config->inc('num_comments', $num_comments);
		}
		else
		{
			$this->gallery_config->dec('num_images', $num_images);
			$this->gallery_config->dec('num_comments', $num_comments);
		}
	}

	public function get_image_data($image_id)
	{
		global $db, $table_prefix;
		if (empty($image_id))
		{
			return;
		}

		$sql = 'SELECT * FROM ' . $table_prefix . 'gallery_images WHERE image_id = ' . $image_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ($row)
		{
			return $row;
		}
	}
	
	/**
	* Approve image
	* @param (array)	$image_id_ary	The image ID array to be approved
	* @param (int)		$album_id	The album image is approved to (just save some queries for log)
	* return 0 on success
	*/
	public function approve_images($image_id_ary, $album_id)
	{
		global $db, $table_prefix;
		
		self::handle_counter($image_id_ary, true, true);

		$sql = 'UPDATE ' . $table_prefix . 'gallery_images 
			SET image_status = ' . self::STATUS_APPROVED . '
			WHERE image_status <> ' . self::STATUS_ORPHAN . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary);
		$db->sql_query($sql);

		$image_names = array();
		$sql = 'SELECT image_id, image_name
			FROM ' . $table_prefix . 'gallery_images 
			WHERE image_status <> ' . self::STATUS_ORPHAN . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_APPROVED', $row['image_name']);
		}
		$db->sql_freeresult($result);
	}

	/**
	* UnApprove image
	* @param (array)	$image_id_ary	The image ID array to be unapproved
	* @param (int)		$album_id	The album image is approved to (just save some queries for log)
	*/
	public function unapprove_images($image_id_ary, $album_id)
	{
		global $db, $table_prefix;
		
		self::handle_counter($image_id_ary, false);

		$sql = 'UPDATE ' . $table_prefix . 'gallery_images 
			SET image_status = ' . self::STATUS_UNAPPROVED . '
			WHERE image_status <> ' . self::STATUS_ORPHAN . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary);
		$db->sql_query($sql);

		$sql = 'SELECT image_id, image_name
			FROM ' . $table_prefix . 'gallery_images 
			WHERE image_status <> ' . self::STATUS_ORPHAN . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_UNAPPROVED', $row['image_name']);
		}
		$db->sql_freeresult($result);
	}
	/**
	* Move image
	* @oaram (int)	$image_id	The image that we want to move_uploaded_file
	* @param (int)	$album_id	The album we want to move image to
	*/
	public function move_image($image_id_ary, $album_id)
	{
		global $phpbb_container, $table_prefix, $db;
		$album = $phpbb_container->get('phpbbgallery.core.album');
		$report = $phpbb_container->get('phpbbgallery.core.report');

		$target_data = $album->get_info($album_id);

		//TO DO - Contests
		$sql = 'UPDATE ' . $table_prefix . 'gallery_images 
			SET image_album_id = ' . $album_id . '
			WHERE ' . $db->sql_in_set('image_id', $image_id_ary);
		$db->sql_query($sql);
		
		$report->move_images($image_id_ary, $album_id);

		foreach ($image_id_ary as $image)
		{
			// TO DO make log work
			//add_log('gallery', $album_id, $image, 'LOG_GALLERY_MOVED', $album_data['album_name'], $target_data['album_name']);
		}
		//You will need to take care for album sync for the target and source
	}

	/**
	* Lock images
	* @param (array)	$image_id_ary	Array of images we want to lock
	* @param (int)		$album_id		Album id, so we can log the action
	*/
	public function lock_images($image_id_ary, $album_id)
	{
		global $db, $table_prefix;
		self::handle_counter($image_id_ary, false);

		$sql = 'UPDATE ' . $table_prefix . 'gallery_images 
			SET image_status = ' . self::STATUS_LOCKED . '
			WHERE image_status <> ' . self::STATUS_ORPHAN . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary);
		$db->sql_query($sql);

		$sql = 'SELECT image_id, image_name
			FROM ' . $table_prefix . 'gallery_images 
			WHERE image_status <> ' . self::STATUS_ORPHAN . '
				AND ' . $db->sql_in_set('image_id', $image_id_ary);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_LOCKED', $row['image_name']);
		}
		$db->sql_freeresult($result);
	}
	
	/**
	* Get last image id
	* Return (int) image_id
	**/
	public function get_last_image()
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$public = $this->album->get_public_albums();
		$sql_order = 'image_id DESC';
		$sql_limit = 1;
		$sql = 'SELECT * 
			FROM ' . $this->table_images . '
			WHERE image_status <> ' . self::STATUS_ORPHAN . '
				AND ((' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . self::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('m_status'), false, true) . ') AND ' . $this->db->sql_in_set('image_album_id', $public, true, true) . '
			ORDER BY ' . $sql_order;
		$result = $this->db->sql_query_limit($sql, $sql_limit);

		$row = $this->db->sql_fetchrow($result);
		
		$this->db->sql_freeresult($result);
		
		return $row;
	}
}
