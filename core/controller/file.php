<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

class file
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\db\driver\driver */
	protected $db;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbbgallery\core\auth\auth */
	protected $auth;

	/* @var \phpbbgallery\core\user */
	protected $gallery_user;

	/* @var string */
	protected $path_source;

	/* @var string */
	protected $path_medium;

	/* @var string */
	protected $path_mini;

	/* @var string */
	protected $path_watermark;

	/* @var string */
	protected $table_albums;

	/* @var string */
	protected $table_images;

	/* @var string */
	protected $path;

	/* @var array */
	protected $data;

	/* @var string */
	protected $error;

	/* @var string */
	protected $image_src;

	/* @var boolean */
	protected $use_watermark = false;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config Config object
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db Database object
	 * @param \phpbb\user $user User object
	 * @param \phpbbgallery\core\auth\auth $gallery_auth Gallery auth object
	 * @param \phpbbgallery\core\user $gallery_user Gallery user object
	 * @param \phpbbgallery\core\file\file $tool
	 * @param \phpbb\request\request $request
	 * @param $source_path
	 * @param $medium_path
	 * @param $mini_path
	 * @param $watermark_file
	 * @param $albums_table
	 * @param $images_table
	 * @internal param \phpbbgallery\core\album\display $display Albums display object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbbgallery\core\auth\auth $gallery_auth,
	\phpbbgallery\core\user $gallery_user, \phpbbgallery\core\file\file $tool, \phpbb\request\request $request,
	$source_path, $medium_path, $mini_path, $watermark_file, $albums_table, $images_table)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->auth = $gallery_auth;
		$this->gallery_user = $gallery_user;
		$this->tool = $tool;
		$this->request = $request;
		$this->path_source = $source_path;
		$this->path_medium = $medium_path;
		$this->path_mini = $mini_path;
		$this->path_watermark = $watermark_file;
		$this->table_albums = $albums_table;
		$this->table_images = $images_table;
	}

	/**
	* Image File Controller
	*	Route: gallery/image/{image_id}/source
	*
	* @param	int		$image_id
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function source($image_id)
	{
		$this->auth->load_user_premissions($this->user->data['user_id']);
		$this->path = $this->path_source;
		$this->load_data($image_id);
		$this->check_auth();

		if (!file_exists($this->path_source . $this->data['image_filename']))
		{
			$sql = 'UPDATE ' . $this->table_images . '
				SET image_filemissing = 1
				WHERE image_id = ' . (int) $image_id;
			$this->db->sql_query($sql);

			// trigger_error('IMAGE_NOT_EXIST');
			$this->error = 'image_not_exist.jpg';
			$this->data['image_filename'] = 'image_not_exist.jpg';
			$this->data['image_name'] = 'Image is missing!';
			$this->data['image_user_id'] = 1;
			$this->data['image_status'] = 2;
			$this->data['album_id'] = 0;
			$this->data['album_user_id'] = 1;
			$this->data['image_filemissing'] = 0;
			$this->data['image_filemissing'] = 0;
			$this->data['album_watermark'] = 0;
		}

		$this->generate_image_src();
		// @todo Enable watermark

		$this->use_watermark = $this->config['phpbb_gallery_watermark_enabled'] && $this->data['album_watermark'] && !$this->auth->acl_check('i_watermark', $this->data['album_id'], $this->data['album_user_id']);

		$this->tool->set_image_options($this->config['phpbb_gallery_max_filesize'], $this->config['phpbb_gallery_max_height'], $this->config['phpbb_gallery_max_width']);
		$this->tool->set_image_data($this->image_src, $this->data['image_name']);
		if ($this->error || !$this->user->data['is_registered'])
		{
			$this->tool->disable_browser_cache();
		}

		if (!$this->user->data['is_bot'] && !$this->error)
		{
			$sql = 'UPDATE ' . $this->table_images . '
				SET image_view_count = image_view_count + 1
				WHERE image_id = ' . (int) $image_id;
			$this->db->sql_query($sql);
		}

		return $this->display();
	}

	/**
	* Image File Controller
	*	Route: gallery/image/{image_id}/medium
	*
	* @param	int		$image_id
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function medium($image_id)
	{

		$this->path = $this->path_medium;
		$this->load_data($image_id);
		$this->check_auth();

		$this->generate_image_src();

		if (!file_exists($this->image_src))
		{
			$this->resize($image_id, $this->config['phpbb_gallery_medium_width'], $this->config['phpbb_gallery_medium_height'], 'filesize_medium');
			$this->generate_image_src();
		}
		$this->auth->load_user_premissions($this->user->data['user_id']);
		$this->use_watermark = $this->config['phpbb_gallery_watermark_enabled'] && $this->data['album_watermark'] && !$this->auth->acl_check('i_watermark', $this->data['album_id'], $this->data['album_user_id']);
		$this->tool->set_image_options($this->config['phpbb_gallery_max_filesize'], $this->config['phpbb_gallery_max_height'], $this->config['phpbb_gallery_max_width']);
		$this->tool->set_image_data($this->image_src, $this->data['image_name']);
		if ($this->error || !$this->user->data['is_registered'])
		{
			$this->tool->disable_browser_cache();
		}

		$this->resize($image_id, $this->config['phpbb_gallery_medium_width'], $this->config['phpbb_gallery_medium_height'], 'filesize_medium');

		return $this->display();
	}

	/**
	* Image File Controller
	*	Route: gallery/image/{image_id}/mini
	*
	* @param	int		$image_id
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function mini($image_id)
	{
		$this->path = $this->path_mini;
		$this->load_data($image_id);
		$this->check_auth();
		$this->generate_image_src();

		if (!file_exists($this->image_src))
		{
			$this->resize($image_id, $this->config['phpbb_gallery_thumbnail_width'], $this->config['phpbb_gallery_thumbnail_height'], 'filesize_cache');
			$this->generate_image_src();
		}
		$this->tool->set_image_options($this->config['phpbb_gallery_max_filesize'], $this->config['phpbb_gallery_max_height'], $this->config['phpbb_gallery_max_width']);
		$this->tool->set_image_data($this->image_src, $this->data['image_name']);
		if ($this->error || !$this->user->data['is_registered'])
		{
			$this->tool->disable_browser_cache();
		}

		$this->resize($image_id, $this->config['phpbb_gallery_thumbnail_width'], $this->config['phpbb_gallery_thumbnail_height'], 'filesize_cache');

		return $this->display();
	}

	public function load_data($image_id)
	{
		if ($image_id == 0)
		{
			$this->error = 'image_not_exist.jpg';
			$this->data['image_filename'] = 'image_not_exist.jpg';
			$this->data['image_name'] = 'Image is missing!';
			$this->data['image_user_id'] = 1;
			$this->data['image_status'] = 2;
			$this->data['album_id'] = 0;
			$this->data['album_user_id'] = 1;
			$this->data['image_filemissing'] = 0;
			$this->data['image_filemissing'] = 0;
			$this->data['album_watermark'] = 0;
		}
		else
		{
			$sql = 'SELECT *
				FROM ' . $this->table_images . ' i
				LEFT JOIN ' . $this->table_albums . ' a
					ON (i.image_album_id = a.album_id)
				WHERE i.image_id = ' . (int) $image_id;
			$result = $this->db->sql_query($sql);
			$this->data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$this->data || !$this->data['album_id'])
			{
				// Image or album does not exist
				// trigger_error('INVALID_IMAGE');
				$this->error = 'not_authorised.jpg';
				$this->data['image_filename'] = 'not_authorised.jpg';
				$this->data['image_name'] = 'You are not authorized!';
				$this->data['image_user_id'] = 1;
				$this->data['image_status'] = 2;
				$this->data['album_id'] = 0;
				$this->data['album_user_id'] = 1;
				$this->data['image_filemissing'] = 0;
				$this->data['image_filemissing'] = 0;
				$this->data['album_watermark'] = 0;

			}
		}
	}

	public function check_auth()
	{
		$this->auth->load_user_premissions($this->user->data['user_id']);
		//$zebra_array = $this->auth->get_user_zebra($this->user->data['user_id']);
		// Check permissions
		if (($this->data['image_user_id'] != $this->user->data['user_id']) && ($this->data['image_status'] == \phpbbgallery\core\block::STATUS_ORPHAN))
		{
			// The image is currently being uploaded
			// trigger_error('NOT_AUTHORISED');
			$this->error = 'not_authorised.jpg';
			$this->data['image_filename'] = 'not_authorised.jpg';
			$this->data['image_name'] = 'You are not authorized!';
			$this->data['image_user_id'] = 1;
			$this->data['image_status'] = 2;
			$this->data['album_id'] = 0;
			$this->data['album_user_id'] = 1;
			$this->data['image_filemissing'] = 0;
			$this->data['album_watermark'] = 0;
		}
		if ((!$this->auth->acl_check('i_view', $this->data['album_id'], $this->data['album_user_id'])) || (!$this->auth->acl_check('m_status', $this->data['album_id'], $this->data['album_user_id']) && ($this->data['image_status'] == \phpbbgallery\core\block::STATUS_UNAPPROVED)))
		{
			// Missing permissions
			// trigger_error('NOT_AUTHORISED');
			$this->error = 'not_authorised.jpg';
			$this->data['image_filename'] = 'not_authorised.jpg';
			$this->data['image_name'] = 'You are not authorized!';
			$this->data['image_user_id'] = 1;
			$this->data['image_status'] = 2;
			$this->data['album_id'] = 0;
			$this->data['album_user_id'] = 1;
			$this->data['image_filemissing'] = 0;
			$this->data['album_watermark'] = 0;
		}
		/*if ($this->auth->get_zebra_state($zebra_array, (int) $this->data['album_user_id']) < (int) $this->data['album_auth_access'] && !$this->error)
		{
			// Zebra parameters not met
			// trigger_error('NOT_AUTHORISED');
			$this->error = 'not_authorised.jpg';
			$this->data['image_filename'] = 'not_authorised.jpg';
			$this->data['image_name'] = 'You are not authorized!';
			$this->data['image_user_id'] = 1;
			$this->data['image_status'] = 2;
			$this->data['album_id'] = 0;
			$this->data['album_user_id'] = 1;
			$this->data['image_filemissing'] = 0;
			$this->data['album_watermark'] = 0;
		}*/
	}

	public function generate_image_src()
	{
		$this->image_src = $this->path  . $this->data['image_filename'];

		if ($this->data['image_filemissing'] || !file_exists($this->path_source . $this->data['image_filename']))
		{
			$sql = 'UPDATE ' . $this->table_images . '
				SET image_filemissing = 1
				WHERE image_id = ' . (int) $this->data['image_id'];
			$this->db->sql_query($sql);

			// trigger_error('IMAGE_NOT_EXIST');
			$this->error = 'image_not_exist.jpg';
			$this->data['image_filename'] = 'image_not_exist.jpg';
			$this->data['image_name'] = 'Image is missing!';
			$this->data['image_user_id'] = 1;
			$this->data['image_status'] = 2;
			$this->data['album_id'] = 0;
			$this->data['album_user_id'] = 1;
			$this->data['image_filemissing'] = 0;
			$this->data['image_filemissing'] = 0;
			$this->data['album_watermark'] = 0;
		}

		$this->check_hot_link();

		// There was a reason to not display the image, so we send an error-image
		if ($this->error)
		{
			$this->data['image_filename'] = $this->user->data['user_lang'] . '_' . $this->error;
			if (!file_exists($this->path . $this->data['image_filename']))
			{
				$this->data['image_filename'] = $this->error;
			}
			$this->image_src = $this->path . $this->data['image_filename'];
			$this->use_watermark = false;
		}
	}

	/**
	* Image File Controller
	*	Route: gallery/image/{image_id}/x
	*
	* @return \Symfony\Component\HttpFoundation\BinaryFileResponseResponse A Symfony Response object
	*/
	public function display()
	{
		$this->tool->set_last_modified($this->gallery_user->get_data('user_permissions_changed'));
		$this->tool->set_last_modified($this->config['phpbb_gallery_watermark_changed']);

		// Watermark
		if ($this->use_watermark)
		{
			//$this->tool->set_last_modified(@filemtime($this->path_watermark));
			//$this->tool->watermark_image($this->path_watermark, $this->config['phpbb_gallery_watermark_position'], $this->config['phpbb_gallery_watermark_height'], $this->config['phpbb_gallery_watermark_width']);
			$this->tool->set_last_modified(@filemtime($this->config['phpbb_gallery_watermark_source']));
			$this->tool->watermark_image($this->config['phpbb_gallery_watermark_source'], $this->config['phpbb_gallery_watermark_position'], $this->config['phpbb_gallery_watermark_height'], $this->config['phpbb_gallery_watermark_width']);
		}

		// Let's check image is loaded
		if (!$this->tool->image_content_type)
		{
			$this->tool->image_content_type = $this->tool->mimetype_by_filename($this->tool->image_source);
			if (!$this->tool->image_content_type)
			{
				trigger_error('NO_MIMETYPE_MATCHED');
			}
		}

		$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($this->tool->image_source);

		$response->headers->set('Pragma', 'public');
		$response->headers->set('Content-Type', $this->tool->image_content_type);
		if ($this->tool->is_ie_greater7($this->user->browser))
		{
			$response->headers->set('X-Content-Type-Options', 'nosniff');
		}
		if (empty($this->user->browser) || (!$this->tool->is_ie_greater7($this->user->browser) && (strpos(strtolower($this->user->browser), 'msie') !== false)))
		{
			$response->headers->set('Content-Disposition', 'attachment; ' . $this->tool->header_filename(htmlspecialchars_decode($this->tool->image_name) . '.' . ($this->tool->image_type == 'jpeg' ? 'jpg' : $this->tool->image_type)));
			if (empty($this->user->browser) || (strpos(strtolower($this->user->browser), 'msie 6.0') !== false))
			{
				$response->headers->set('expires', '-1');
			}
		}
		else
		{
			$response->headers->set('Content-Disposition', 'inline; ' . $this->tool->header_filename(htmlspecialchars_decode($this->tool->image_name)) . '.' . ($this->tool->image_type == 'jpeg' ? 'jpg' : $this->tool->image_type));
			if ($this->tool->is_ie_greater7($this->user->browser))
			{
				$response->headers->set('X-Download-Options', 'noopen');
			}
		}

		return $response;
		//return $response;

	}

	protected function resize($image_id, $resize_width, $resize_height, $store_filesize = '', $put_details = false)
	{
		if (!file_exists($this->image_src))
		{
			$this->tool->set_image_data($this->path_source . $this->data['image_filename']);
			$this->tool->read_image(true);

			$image_size['file'] = $this->tool->image_size['file'];
			$image_size['width'] = $this->tool->image_size['width'];
			$image_size['height'] = $this->tool->image_size['height'];

			$this->tool->set_image_data($this->image_src);

			if (($image_size['width'] > $resize_width) || ($image_size['height'] > $resize_height))
			{
				$this->tool->create_thumbnail($resize_width, $resize_height, $put_details, \phpbbgallery\core\file\file::THUMBNAIL_INFO_HEIGHT, $image_size);
			}

//			if ($phpbb_ext_gallery->config->get($mode . '_cache'))
//			{
			$this->tool->write_image($this->image_src, $this->config['phpbb_gallery_jpg_quality'], false);

			if ($store_filesize)
			{
				$this->data[$store_filesize] = @filesize($this->image_src);
				$sql = 'UPDATE ' . $this->table_images . '
					SET ' . $this->db->sql_build_array('UPDATE', array(
						$store_filesize => $this->data[$store_filesize],
					)) . '
					WHERE ' . $this->db->sql_in_set('image_id', $image_id);
				$this->db->sql_query($sql);
			}

//			}
		}
	}

	protected function check_hot_link()
	{
		if (!$this->config['phpbb_gallery_allow_hotlinking'])
		{
			$haystak = array();
			$haystak = explode(',', $this->config['phpbb_gallery_hotlinking_domains']);
			//add one extra array - current phpbbdomain
			$haystak[] = $this->config['server_name'];
			$referrer = $this->request->server('HTTP_REFERER', '');
			$not_hl = false;
			foreach ($haystak as $var)
			{
				if (!empty($var))
				{
					if (strpos($referrer, $var) > 0 || empty($referrer))
					{
						$not_hl = true;
					}
				}
			}
			if (!$not_hl)
			{
				$this->error = 'no_hotlinking.jpg';
				$this->data['image_filename'] = 'no_hotlinking.jpg';
				$this->data['image_name'] = 'Hot linking not allowed';
				$this->data['image_user_id'] = 1;
				$this->data['image_status'] = 2;
				$this->data['album_id'] = 0;
				$this->data['album_user_id'] = 1;
				$this->data['image_filemissing'] = 0;
				$this->data['image_filemissing'] = 0;
				$this->data['album_watermark'] = 0;
			}
		}
	}
}
