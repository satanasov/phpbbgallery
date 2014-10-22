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
	* @param \phpbb\config\config		$config		Config object
	* @param \phpbb\db\driver\driver	$db			Database object
	* @param \phpbb\user				$user		User object
	* @param \phpbbgallery\core\album\display	$display	Albums display object
	* @param \phpbbgallery\core\auth\auth	$gallery_auth	Gallery auth object
	* @param \phpbbgallery\core\user	$gallery_user	Gallery user object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver $db, \phpbb\user $user, \phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\user $gallery_user, \phpbbgallery\core\file\file $tool, $source_path, $medium_path, $mini_path, $watermark_file, $albums_table, $images_table)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->auth = $gallery_auth;
		$this->gallery_user = $gallery_user;
		$this->tool = $tool;
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
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function source($image_id)
	{
		$this->path = $this->path_source;
		$this->load_data($image_id);
		$this->check_auth();

		if (!file_exists($this->path_source . $this->data['image_filename']))
		{
			$sql = 'UPDATE ' . $this->table_images . '
				SET image_filemissing = 1
				WHERE image_id = ' . $image_id;
			$this->db->sql_query($sql);

			// trigger_error('IMAGE_NOT_EXIST');
			$this->error = 'image_not_exist.jpg';
		}

		$this->generate_image_src();
		// @todo Enable watermark
		// $this->use_watermark = $this->config['phpbb_gallery_watermark_enabled'] && $this->data['album_watermark'] && !$this->auth->acl_check('i_watermark', $this->data['album_id'], $this->data['album_user_id']);

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
				WHERE image_id = ' . $image_id;
			$this->db->sql_query($sql);
		}

		return $this->display(false);
	}

	/**
	* Image File Controller
	*	Route: gallery/image/{image_id}/medium
	*
	* @param	int		$image_id
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function medium($image_id)
	{
		$this->path = $this->path_medium;
		$this->load_data($image_id);
		$this->check_auth();
		$this->generate_image_src();

		$this->tool->set_image_options($this->config['phpbb_gallery_max_filesize'], $this->config['phpbb_gallery_max_height'], $this->config['phpbb_gallery_max_width']);
		$this->tool->set_image_data($this->image_src, $this->data['image_name']);
		if ($this->error || !$this->user->data['is_registered'])
		{
			$this->tool->disable_browser_cache();
		}

		$this->resize($image_id, $this->config['phpbb_gallery_medium_width'], $this->config['phpbb_gallery_medium_height'], 'filesize_medium');

		return $this->display(false);
	}

	/**
	* Image File Controller
	*	Route: gallery/image/{image_id}/mini
	*
	* @param	int		$image_id
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function mini($image_id)
	{
		$this->path = $this->path_mini;
		$this->load_data($image_id);
		$this->check_auth();
		$this->generate_image_src();

		$this->tool->set_image_options($this->config['phpbb_gallery_max_filesize'], $this->config['phpbb_gallery_max_height'], $this->config['phpbb_gallery_max_width']);
		$this->tool->set_image_data($this->image_src, $this->data['image_name']);
		if ($this->error || !$this->user->data['is_registered'])
		{
			$this->tool->disable_browser_cache();
		}

		$this->resize($image_id, $this->config['phpbb_gallery_thumbnail_width'], $this->config['phpbb_gallery_thumbnail_height'], 'filesize_cache');

		return $this->display(false);
	}

	public function load_data($image_id)
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
		}
	}

	public function check_auth()
	{
		// Check permissions
		if (($this->data['image_user_id'] != $this->user->data['user_id']) && ($this->data['image_status'] == \phpbbgallery\core\image\image::STATUS_ORPHAN))
		{
			// The image is currently being uploaded
			// trigger_error('NOT_AUTHORISED');
			$this->error = 'not_authorised.jpg';
		}

		if ((!$this->auth->acl_check('i_view', $this->data['album_id'], $this->data['album_user_id'])) || (!$this->auth->acl_check('m_status', $this->data['album_id'], $this->data['album_user_id']) && ($this->data['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED)))
		{
			// Missing permissions
			// trigger_error('NOT_AUTHORISED');
			$this->error = 'not_authorised.jpg';
		}
	}

	public function generate_image_src()
	{
		$this->image_src = $this->path  . $this->data['image_filename'];

		if ($this->data['image_filemissing'] || !file_exists($this->path_source . $this->data['image_filename']))
		{
//			$sql = 'UPDATE ' . $this->table_images . '
//				SET image_filemissing = 1
//				WHERE image_id = ' . $image_id;
//			$this->db->sql_query($sql);

			// trigger_error('IMAGE_NOT_EXIST');
			$this->error = 'image_not_exist.jpg';
		}

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
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function display()
	{
		$this->tool->set_last_modified($this->gallery_user->get_data('user_permissions_changed'));
		$this->tool->set_last_modified($this->config['phpbb_gallery_watermark_changed']);

		// Watermark
		if ($this->use_watermark)
		{
//			$this->tool->set_last_modified(@filemtime($this->path_watermark));
//			$this->tool->watermark_image($this->path_watermark, $this->config['phpbb_gallery_watermark_position'], $this->config['phpbb_gallery_watermark_height'], $this->config['phpbb_gallery_watermark_width']);
		}

		$this->tool->send_image_to_browser();
		exit;
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
}
