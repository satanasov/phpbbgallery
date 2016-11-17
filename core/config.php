<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core;

class config
{
	private $phpbb_auth;
	private $phpbb_cache;
	private $phpbb_config;
	private $phpbb_db;
	private $phpbb_template;
	private $phpbb_user;

	private $phpbb_phpEx;
	private $phpbb_root_path;

	private $configs_array = array(
		'album_display'		=> 254,
		'album_images'		=> 2500,
		'allow_comments'	=> true,
		'allow_gif'			=> true,
		'allow_hotlinking'	=> true,
		'allow_jpg'			=> true,
		'allow_png'			=> true,
		'allow_rates'		=> true,
		'allow_resize'		=> true,
		'allow_rotate'		=> true,
		'allow_zip'			=> false,

		'captcha_comment'		=> true,
		'captcha_upload'		=> true,
		'comment_length'		=> 2000,
		'comment_user_control'	=> true,
		'contests_ended'		=> 0,
		'current_upload_dir_size'	=> 0,
		'current_upload_dir'	=> 0,

		'default_sort_dir'	=> 'd',
		'default_sort_key'	=> 't',
		'description_length'=> 2000,
		'disp_birthdays'			=> false,
		'disp_image_url'			=> true,
		'disp_login'				=> true,
		'disp_nextprev_thumbnail'	=> false,
		'disp_statistic'			=> true,
		'disp_total_images'			=> true,
		'disp_whoisonline'			=> true,

		'gdlib_version'		=> 2,

		'hotlinking_domains'	=> 'anavaro.com',

		'jpg_quality'			=> 100,

		'link_thumbnail'		=> 'image_page',
		'link_imagepage'		=> 'image',
		'link_image_name'		=> 'image_page',
		'link_image_icon'		=> 'image_page',

		'max_filesize'			=> 512000,
		'max_height'			=> 1024,
		'max_rating'			=> 10,
		'max_width'				=> 1280,
		'medium_cache'			=> true,
		'medium_height'			=> 600,
		'medium_width'			=> 800,
		'mini_thumbnail_disp'	=> true,
		'mini_thumbnail_size'	=> 70,
		'mvc_ignore'			=> 0,
		'mvc_time'				=> 0,
		'mvc_version'			=> '',

		'newest_pega_user_id'	=> 0,
		'newest_pega_username'	=> '',
		'newest_pega_user_colour'	=> '',
		'newest_pega_album_id'	=> 0,
		'num_comments'			=> 0,
		'num_images'			=> 0,
		'num_pegas'				=> 0,
		'num_uploads'			=> 10,

		'pegas_index_album'		=> false,
		//'pegas_index_random'	=> true,
		'pegas_index_rnd_count'	=> 4,
		//'pegas_index_recent'	=> true,
		'pegas_index_rct_count'	=> 4,
		'items_per_page'		=> 15,
		'profile_user_images'	=> true,
		'profile_pega'			=> true,
		'prune_orphan_time'		=> 0,

		'rrc_gindex_comments'	=> false,
		//'rrc_gindex_contests'	=> 1,
		'rrc_gindex_display'	=> 173,
		'rrc_gindex_mode'		=> 7,
		'rrc_gindex_pegas'		=> true,
		'rrc_profile_items'	=> 4,
		'rrc_profile_display'	=> 141,
		'rrc_profile_mode'		=> 3,
		//'rrc_profile_pegas'		=> true,

		'search_display'		=> 45,

		//'thumbnail_cache'		=> true,
		'thumbnail_height'		=> 160,
		//'thumbnail_infoline'	=> false,
		'thumbnail_quality'		=> 50,
		'thumbnail_width'		=> 240,

		'version'				=> '',
		//'viewtopic_icon'		=> true,
		//'viewtopic_images'		=> true,
		//'viewtopic_link'		=> false,

		'watermark_changed'		=> 0,
		'watermark_enabled'		=> true,
		'watermark_height'		=> 50,
		'watermark_position'	=> 20,
		'watermark_source'		=> 'gallery/images/watermark.png',
		'watermark_width'		=> 200,
	);

	/**
	 * Constructor
	 * @param \phpbb\config\config $config
	 */
	public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;

	}

	public function get_all()
	{
		$config_ary = array();
		foreach ($this->configs_array as $option => $default)
		{
			if (isset($this->config['phpbb_gallery_' . $option]))
			{
				$config_ary[$option] = $this->config['phpbb_gallery_' . $option];
			}
			else
			{
				$config_ary[$option] = $default;
			}
		}
		return $config_ary;
	}

	public function get($key)
	{
		if (isset($this->config['phpbb_gallery_' . $key]))
		{
			return $this->config['phpbb_gallery_' . $key];
		}
		else
		{
			return $this->configs_array[$key];
		}
	}

	public function set($name, $value)
	{
		$this->config->set('phpbb_gallery_' . $name, $value);
	}

	public function inc($name, $value)
	{
		$this->config->increment('phpbb_gallery_' . $name, (int) $value);
	}
	public function dec($name, $value)
	{
		$this->config->increment('phpbb_gallery_' . $name, (int) $value * -1);
	}
}
