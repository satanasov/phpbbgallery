<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

namespace phpbbgallery\core\migrations;

class release_1_2_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}
	public function update_data()
	{
		return array(
			array('permission.add', array('a_gallery_manage', true, 'a_board')),
			array('permission.add', array('a_gallery_albums', true, 'a_board')),
			array('permission.add', array('a_gallery_cleanup', true, 'a_board')),

			// ACP
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'PHPBB_GALLERY')),
			array('module.add', array('acp', 'PHPBB_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\acp\main_module',
				'module_langname'	=> 'ACP_GALLERY_OVERVIEW',
				'module_mode'		=> 'overview',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_gallery_manage',
			))),
			array('module.add', array('acp', 'PHPBB_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\acp\config_module',
				'module_langname'	=> 'ACP_GALLERY_CONFIGURE_GALLERY',
				'module_mode'		=> 'main',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_gallery_manage',
			))),
			array('module.add', array('acp', 'PHPBB_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\acp\albums_module',
				'module_langname'	=> 'ACP_GALLERY_MANAGE_ALBUMS',
				'module_mode'		=> 'manage',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_gallery_albums',
			))),
			array('module.add', array('acp', 'PHPBB_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\acp\permissions_module',
				'module_langname'	=> 'ACP_GALLERY_ALBUM_PERMISSIONS',
				'module_mode'		=> 'manage',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_gallery_albums',
			))),
			array('module.add', array('acp', 'PHPBB_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\acp\permissions_module',
				'module_langname'	=> 'ACP_GALLERY_ALBUM_PERMISSIONS_COPY',
				'module_mode'		=> 'copy',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_gallery_albums',
			))),
			array('module.add', array('acp', 'PHPBB_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\acp\gallery_logs_module',
				'module_langname'	=> 'ACP_GALLERY_LOGS',
				'module_mode'		=> 'main',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_viewlogs',
			))),
			// Todo CLEANUP Add-on
			/*array('module.add', array('acp', 'PHPBB_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\acp\gallery_module',
				'module_langname'	=> 'ACP_GALLERY_CLEANUP',
				'module_mode'		=> 'cleanup',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_gallery_cleanup',
			))),*/

			// UCP
			array('module.add', array('ucp', '', 'UCP_GALLERY')),
			array('module.add', array('ucp', 'UCP_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\ucp\main_module',
				'module_langname'	=> 'UCP_GALLERY_SETTINGS',
				'module_mode'		=> 'manage_settings',
				'module_auth'		=> 'ext_phpbbgallery/core',
			))),
			array('module.add', array('ucp', 'UCP_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\ucp\main_module',
				'module_langname'	=> 'UCP_GALLERY_PERSONAL_ALBUMS',
				'module_mode'		=> 'manage_albums',
				'module_auth'		=> 'ext_phpbbgallery/core',
			))),
			array('module.add', array('ucp', 'UCP_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\ucp\main_module',
				'module_langname'	=> 'UCP_GALLERY_WATCH',
				'module_mode'		=> 'manage_subscriptions',
				'module_auth'		=> 'ext_phpbbgallery/core',
			))),
			//@todo move
/*			array('module.add', array('ucp', 'UCP_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\ucp\main_module',
				'module_langname'	=> 'UCP_GALLERY_FAVORITES',
				'module_mode'		=> 'manage_favorites',
				'module_auth'		=> 'ext_phpbbgallery/core',
			))),

			// Logs
			array('module.add', array('acp', 'ACP_FORUM_LOGS', array(
				'module_basename'	=> '\phpbbgallery\core\acp\gallery_logs_module',
				'module_langname'	=> 'ACP_GALLERY_LOGS',
				'module_mode'		=> 'main',
				'module_auth'		=> 'ext_phpbbgallery/core && acl_a_viewlogs',
			))),*/

			// @todo: ADD BBCODE
			array('custom', array(array(&$this, 'install_config'))),
		);
	}

	public function install_config()
	{
		global $config;

		foreach (self::$configs as $name => $value)
		{
			if (isset(self::$is_dynamic[$name]))
			{
				$config->set('phpbb_gallery_' . $name, $value, true);
			}
			else
			{
				$config->set('phpbb_gallery_' . $name, $value);
			}
		}

		return true;
	}

	static public $is_dynamic = array(
		'mvc_time',
		'mvc_version',

		'num_comments',
		'num_images',
		'num_pegas',

		'current_upload_dir_size',
	);

	static public $configs = array(
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
		'profile_user_images'	=> true,
		'profile_pega'			=> true,
		'prune_orphan_time'		=> 0,

		'rrc_gindex_comments'	=> false,
		'rrc_gindex_display'	=> 173,
		'rrc_gindex_mode'		=> 7,
		'rrc_gindex_pegas'		=> true,
		'rrc_profile_display'	=> 141,
		'rrc_profile_mode'		=> 3,
		//'rrc_profile_pegas'		=> true,
		'rrc_profile_items'		=> 4,

		'search_display'		=> 45,

		//'thumbnail_cache'		=> true,
		'thumbnail_height'		=> 160,
		//'thumbnail_infoline'	=> false,
		'thumbnail_quality'		=> 50,
		'thumbnail_width'		=> 240,
		//'viewtopic_icon'		=> true,
		//'viewtopic_images'		=> true,
		//'viewtopic_link'		=> false,

		'watermark_changed'		=> 0,
		'watermark_enabled'		=> true,
		'watermark_height'		=> 50,
		'watermark_position'	=> 20,
		'watermark_source'		=> 'ext/phpbbgallery/core/images/watermark.png',
		'watermark_width'		=> 200,

		'items_per_page'		=> 15,

		//Version
		'version'				=> '1.2.0',
	);
}
