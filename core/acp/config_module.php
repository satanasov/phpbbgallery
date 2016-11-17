<?php
/**
*
* @package Gallery - Config ACP Module
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\acp;

class config_module
{
	/**
	* This function is called, when the main() function is called.
	* You can use this function to add your language files, check for a valid mode, unset config options and more.
	*
	* @param	int		$id		The ID of the module
	* @param	string	$mode	The name of the mode we want to display
	* @return	void
	*/
	public function main($id, $mode)
	{
		// Check whether the mode is allowed.
		if (!isset($this->display_vars[$mode]))
		{
			trigger_error('NO_MODE', E_USER_ERROR);
		}

		global $config, $db, $user, $template, $cache, $phpbb_container, $phpbb_root_path, $phpEx, $phpbb_gallery_url;
		global $request, $config;

		$phpbb_gallery_url = $phpbb_container->get('phpbbgallery.core.url');
		$user->add_lang_ext('phpbbgallery/core', array('gallery', 'gallery_acp'));

		$submit = (isset($_POST['submit'])) ? true : false;
		$form_key = 'acp_time';
		add_form_key($form_key);

		// Create the toolio config object
		//$this->toolio_config = new phpbb_ext_gallery_core_config($config, $db, CONFIG_TABLE);
		switch ($mode)
		{
			case 'main':
				$vars = $this->get_display_vars('main');
			break;
		}
		// Init gallery block class
		$phpbb_ext_gallery_core_constants = $phpbb_container->get('phpbbgallery.core.block');
		// Init gallery configs class
		$phpbb_gallery_configs = new \phpbbgallery\core\config($config);
		$this->new_config = $phpbb_gallery_configs->get_all();
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc($request->variable('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($vars['vars'], $cfg_array, $error);
		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}

		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}
		//now we display the variables
		foreach ($vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}
			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				// Check for RRC-display-options
				if (isset($null['method']) && (($null['method'] == 'rrc_display') || ($null['method'] == 'rrc_modes')))
				{
					// Changing the value, casted by int to not mess up anything
					$config_value = (int) array_sum($request->variable($config_name, array(0)));
				}
				// Recalculate the Watermark-position
				if (isset($null['method']) && ($null['method'] == 'watermark_position'))
				{
					// Changing the value, casted by int to not mess up anything
					$config_value = $request->variable('watermark_position_x', 0) + $request->variable('watermark_position_y', 0);
				}
				if ($config_name == 'link_thumbnail')
				{
					$update_bbcode = $request->variable('update_bbcode', '');
					// Update the BBCode
					if ($update_bbcode)
					{
						if (!class_exists('acp_bbcodes'))
						{
							$phpbb_gallery_url->_include('acp/acp_bbcodes', 'phpbb');
							//include_once($phpbb_root_path . 'includes/acp/acp_bbcodes.' . $phpEx);
						}
						$acp_bbcodes = new \acp_bbcodes();
						$bbcode_match = '[image]{NUMBER}[/image]';
						$bbcode_tpl = $this->bbcode_tpl($config_value);

						$sql_ary = $acp_bbcodes->build_regexp($bbcode_match, $bbcode_tpl);
						$sql_ary = array_merge($sql_ary, array(
							'bbcode_match'			=> $bbcode_match,
							'bbcode_tpl'			=> $bbcode_tpl,
							'display_on_posting'	=> true,
							'bbcode_helpline'		=> 'GALLERY_HELPLINE_ALBUM',
						));

						$sql = 'UPDATE ' . BBCODES_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
							WHERE bbcode_tag = '" . $sql_ary['bbcode_tag'] . "'";
						$db->sql_query($sql);
						$cache->destroy('sql', BBCODES_TABLE);
					}
					}
				if ((strpos($config_name, 'watermark') !== false) && ($phpbb_gallery_configs->get($config_name) != $config_value))
				{
					$phpbb_gallery_configs->set('watermark_changed', time());
					// OK .. let's try and destroy watermarked images
					$cache_dir = @opendir($phpbb_gallery_url->path('thumbnail'));
					while ($cache_file = @readdir($cache_dir))
					{
						if (preg_match('/(\_wm.gif$|\_wm.png$|\_wm.jpg|\_wm.jpeg)$/is', $cache_file))
						{
							@unlink($phpbb_gallery_url->path('thumbnail') . $cache_file);
						}
					}
					@closedir($cache_dir);

					$medium_dir = @opendir($phpbb_gallery_url->path('medium'));
					while ($medium_file = @readdir($medium_dir))
					{
						if (preg_match('/(\_wm.gif$|\_wm.png$|\_wm.jpg|\_wm.jpeg)$/is', $medium_file))
						{
							@unlink($phpbb_gallery_url->path('medium') . $medium_file);
						}
					}
					@closedir($medium_dir);
					$upload_dir = @opendir($phpbb_gallery_url->path('upload'));
					while ($upload_file = @readdir($upload_dir))
					{
						if (preg_match('/(\_wm.gif$|\_wm.png$|\_wm.jpg|\_wm.jpeg)$/is', $upload_file))
						{
							@unlink($phpbb_gallery_url->path('upload') . $upload_file);
						}
					}
					@closedir($upload_dir);

					for ($i = 1; $i <= $phpbb_gallery_configs->get('current_upload_dir'); $i++)
					{
						$cache_dir = @opendir($phpbb_gallery_url->path('thumbnail') . $i . '/');
						while ($cache_file = @readdir($cache_dir))
						{
							if (preg_match('/(\_wm.gif$|\_wm.png$|\_wm.jpg|\_wm.jpeg)$/is', $cache_file))
							{
								@unlink($phpbb_gallery_url->path('thumbnail') . $i . '/' . $cache_file);
							}
						}
						@closedir($cache_dir);

						$medium_dir = @opendir($phpbb_gallery_url->path('medium') . $i . '/');
						while ($medium_file = @readdir($medium_dir))
						{
							if (preg_match('/(\_wm.gif$|\_wm.png$|\_wm.jpg|\_wm.jpeg)$/is', $medium_file))
							{
								@unlink($phpbb_gallery_url->path('medium') . $i . '/' . $medium_file);
							}
						}
						@closedir($medium_dir);
						$upload_dir = @opendir($phpbb_gallery_url->path('upload') . $i . '/');
						while ($upload_file = @readdir($upload_dir))
						{
							if (preg_match('/(\_wm.gif$|\_wm.png$|\_wm.jpg|\_wm.jpeg)$/is', $upload_file))
							{
								@unlink($phpbb_gallery_url->path('upload') . $upload_file);
							}
						}
						@closedir($upload_dir);
					}
				}
				$phpbb_gallery_configs->set($config_name, $config_value);
			}
		}
		if ($submit)
		{
			$cache->destroy('sql', CONFIG_TABLE);
			trigger_error($user->lang['GALLERY_CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$vars['title'] . '_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

		// Output relevant page
		foreach ($vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}
			if (isset($vars['append']))
			{
				$vars['append'] = (isset($user->lang[$vars['append']])) ? ' ' . $user->lang[$vars['append']] : $vars['append'];
			}

			$this->new_config[$config_key] = $phpbb_gallery_configs->get($config_key);

			$type = explode(':', $vars['type']);

			$l_explain = '';
			//$this->var_display($vars);
			if (isset($vars['explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXP'])) ? $user->lang[$vars['lang'] . '_EXP'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> (isset($vars['explain']) ? $vars['explain'] : ''),
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,
			));

			unset($this->display_vars['vars'][$config_key]);
		}
	}

	/**
	* Returns an array with the display_var array for the given mode
	* The returned display must have the two keys title and vars
	*		@key	string	title		The page title or lang key for the page title
	*		@key	array	vars		An array of tupels, one foreach config option we display:
	*					@key		The name of the config in the get_config_array() array.
	*								If the key starts with 'legend' a new box is opened with the value being the title of this box.
	*					@value		An array with several options:
	*						@key lang		Description for the config value (can be a language key)
	*						@key explain	Boolean whether the config has an explanation of not.
	*										If true, <lang>_EXP (and <lang>_EXPLAIN) is displayed as explanation
	*						@key validate	The config value can be validated as bool, int or string.
	*										Additional a min and max value can be specified for integers
	*										On strings the min and max value are the length of the string
	*										If your config value shall not be casted, remove the validate-key.
	*						@key type		The type of the config option:
	*										- Radio buttons:		Either with "Yes and No" (radio:yes_no) or "Enabled and Disabled" (radio:enabled_disabled) as description
	*										- Text/password field:	"text:<field-size>:<text-max-length>" and "password:<field-size>:<text-max-length>"
	*										- Select:				"select" requires the key "function" or "method" to be set which provides the html code for the options
	*										- Custom template:		"custom" requires the key "function" or "method" to be set which provides the html code
	*						@key function/method	Required when using type select and custom
	*						@key append		A language string that is appended after the config type (e.g. You can append 'px' to a pixel size field)
	* This last parameter is optional
	*		@key	string	tpl			Name of the template file we use to display the configs
	*
	* @param	string	$mode	The name of the mode we want to display
	* @return	array		See description above
	*/
	public function get_display_vars($mode)
	{
		global $phpbb_dispatcher;

		$return_ary = $this->display_vars[$mode];

		/**
		* Event to send the display vars
		* @event phpbbgallery.core.acp.config.get_display_vars
		* @var	string	mode		Mode we are requesting for
		* @var	array	return_ary	Array we are sending back
		* @since 1.2.0
		*/
		$vars = array('mode', 'return_ary');
		extract($phpbb_dispatcher->trigger_event('phpbbgallery.core.acp.config.get_display_vars', compact($vars)));

		$vars = array();
		$legend_count = 1;
		foreach ($return_ary['vars'] as $legend_name => $configs)
		{
			$vars['legend' . $legend_count] = $legend_name;
			foreach ($configs as $key => $options)
			{
				$vars[$key] = $options;
			}
			$legend_count++;
		}

		// Add one last legend for the buttons
		$vars['legend' . $legend_count] = '';
		$return_ary['vars'] = $vars;

		return $return_ary;
	}

	protected $display_vars = array(
		'main'	=> array(
			'title'	=> 'GALLERY_CONFIG',
			'vars'	=> array(
				'GALLERY_CONFIG'	=> array(
					'items_per_page'		=> array('lang' => 'ITEMS_PER_PAGE',		'validate' => 'int',	'type' => 'text:7:3',		'explain' => true),
					'allow_comments'		=> array('lang' => 'COMMENT_SYSTEM',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'comment_user_control'	=> array('lang' => 'COMMENT_USER_CONTROL',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					'comment_length'		=> array('lang' => 'COMMENT_MAX_LENGTH',	'validate' => 'int',	'type' => 'text:7:5',		'append' => 'CHARACTERS'),
					'allow_rates'			=> array('lang' => 'RATE_SYSTEM',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'max_rating'			=> array('lang' => 'RATE_SCALE',			'validate' => 'int',	'type' => 'text:7:2'),
					'allow_hotlinking'		=> array('lang' => 'HOTLINK_PREVENT',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'hotlinking_domains'	=> array('lang' => 'HOTLINK_ALLOWED',		'validate' => 'string',	'type' => 'text:40:255',	'explain' => true),
				),

				'ALBUM_SETTINGS'	=> array(
					'album_display'			=> array('lang' => 'RRC_DISPLAY_OPTIONS',	'validate' => 'int',	'type' => 'custom',			'method' => 'rrc_display'),
					'default_sort_key'		=> array('lang' => 'DEFAULT_SORT_METHOD',	'validate' => 'string',	'type' => 'custom',			'method' => 'sort_method_select'),
					'default_sort_dir'		=> array('lang' => 'DEFAULT_SORT_ORDER',	'validate' => 'string',	'type' => 'custom',			'method' => 'sort_order_select'),
					'album_images'			=> array('lang' => 'MAX_IMAGES_PER_ALBUM',	'validate' => 'int',	'type' => 'text:7:7',		'explain' => true),
					'mini_thumbnail_disp'	=> array('lang' => 'DISP_FAKE_THUMB',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'mini_thumbnail_size'	=> array('lang' => 'FAKE_THUMB_SIZE',		'validate' => 'int',	'type' => 'text:7:4',		'explain' => true,	'append' => 'PIXELS'),
				),

				'SEARCH_SETTINGS'	=> array(
					'search_display'		=> array('lang' => 'RRC_DISPLAY_OPTIONS',	'validate' => 'int',	'type' => 'custom',			'method' => 'rrc_display'),
				),

				'IMAGE_SETTINGS'	=> array(
					'num_uploads'			=> array('lang' => 'UPLOAD_IMAGES',			'validate' => 'int',	'type' => 'text:7:2'),
					'max_filesize'			=> array('lang' => 'MAX_FILE_SIZE',			'validate' => 'int',	'type' => 'text:12:9',		'append' => 'BYTES'),
					'max_width'				=> array('lang' => 'MAX_WIDTH',				'validate' => 'int',	'type' => 'text:7:5',		'append' => 'PIXELS'),
					'max_height'			=> array('lang' => 'MAX_HEIGHT',			'validate' => 'int',	'type' => 'text:7:5',		'append' => 'PIXELS'),
					'allow_resize'			=> array('lang' => 'RESIZE_IMAGES',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'allow_rotate'			=> array('lang' => 'ROTATE_IMAGES',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'jpg_quality'			=> array('lang' => 'JPG_QUALITY',			'validate' => 'int',	'type' => 'text:7:5',		'explain' => true),
					//'medium_cache'			=> array('lang' => 'MEDIUM_CACHE',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'medium_width'			=> array('lang' => 'RSZ_WIDTH',				'validate' => 'int',	'type' => 'text:7:4',		'append' => 'PIXELS'),
					'medium_height'			=> array('lang' => 'RSZ_HEIGHT',			'validate' => 'int',	'type' => 'text:7:4',		'append' => 'PIXELS'),
					'allow_gif'				=> array('lang' => 'GIF_ALLOWED',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'allow_jpg'				=> array('lang' => 'JPG_ALLOWED',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'allow_png'				=> array('lang' => 'PNG_ALLOWED',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'allow_zip'				=> array('lang' => 'ZIP_ALLOWED',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					'description_length'	=> array('lang' => 'IMAGE_DESC_MAX_LENGTH',	'validate' => 'int',	'type' => 'text:7:5',		'append' => 'CHARACTERS'),
					'disp_nextprev_thumbnail'	=> array('lang' => 'DISP_NEXTPREV_THUMB','validate' => 'bool',	'type' => 'radio:yes_no'),
					'disp_image_url'		=> array('lang' => 'VIEW_IMAGE_URL',		'validate' => 'bool',	'type' => 'radio:yes_no'),
				),

				'THUMBNAIL_SETTINGS'	=> array(
					//'thumbnail_cache'		=> array('lang' => 'THUMBNAIL_CACHE',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'gdlib_version'			=> array('lang' => 'GD_VERSION',			'validate' => 'int',	'type' => 'custom',			'method' => 'gd_radio'),
					'thumbnail_width'		=> array('lang' => 'THUMBNAIL_WIDTH',		'validate' => 'int',	'type' => 'text:7:3',		'append' => 'PIXELS'),
					'thumbnail_height'		=> array('lang' => 'THUMBNAIL_HEIGHT',		'validate' => 'int',	'type' => 'text:7:3',		'append' => 'PIXELS'),
					'thumbnail_quality'		=> array('lang' => 'THUMBNAIL_QUALITY',		'validate' => 'int',	'type' => 'text:7:3',		'explain' => true,	'append' => 'PERCENT'),
					//'thumbnail_infoline'	=> array('lang' => 'INFO_LINE',				'validate' => 'bool',	'type' => 'radio:yes_no'),
				),

				'WATERMARK_OPTIONS'	=> array(
					'watermark_enabled'		=> array('lang' => 'WATERMARK_IMAGES',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'watermark_source'		=> array('lang' => 'WATERMARK_SOURCE',		'validate' => 'string',	'type' => 'custom',			'explain' => true,	'method' => 'watermark_source'),
					'watermark_height'		=> array('lang' => 'WATERMARK_HEIGHT',		'validate' => 'int',	'type' => 'text:7:4',		'explain' => true,	'append' => 'PIXELS'),
					'watermark_width'		=> array('lang' => 'WATERMARK_WIDTH',		'validate' => 'int',	'type' => 'text:7:4',		'explain' => true,	'append' => 'PIXELS'),
					'watermark_position'	=> array('lang' => 'WATERMARK_POSITION',	'validate' => '',		'type' => 'custom',			'method' => 'watermark_position'),
				),

				'UC_LINK_CONFIG'	=> array(
					'link_thumbnail'		=> array('lang' => 'UC_THUMBNAIL',			'validate' => 'string',	'type' => 'custom',			'explain' => true,	'method' => 'uc_select'),
					'link_imagepage'		=> array('lang' => 'UC_IMAGEPAGE',			'validate' => 'string',	'type' => 'custom',			'explain' => true,	'method' => 'uc_select'),
					'link_image_name'		=> array('lang' => 'UC_IMAGE_NAME',			'validate' => 'string',	'type' => 'custom',			'method' => 'uc_select'),
					'link_image_icon'		=> array('lang' => 'UC_IMAGE_ICON',			'validate' => 'string',	'type' => 'custom',			'method' => 'uc_select'),
				),

				'RRC_GINDEX'	=> array(
					'rrc_gindex_mode'		=> array('lang' => 'RRC_GINDEX_MODE',		'validate' => 'int',	'type' => 'custom',			'explain' => true,	'method' => 'rrc_modes'),
					'rrc_gindex_comments'	=> array('lang' => 'RRC_GINDEX_COMMENTS',	'validate' => 'bool',	'type' => 'radio:yes_no'),
					//'rrc_gindex_contests'	=> array('lang' => 'RRC_GINDEX_CONTESTS',	'validate' => 'int',	'type' => 'text:7:3'),
					'rrc_gindex_display'	=> array('lang' => 'RRC_DISPLAY_OPTIONS',	'validate' => '',		'type' => 'custom',			'method' => 'rrc_display'),
					'rrc_gindex_pegas'		=> array('lang' => 'RRC_GINDEX_PGALLERIES',	'validate' => 'bool',	'type' => 'radio:yes_no'),
				),

				'PHPBB_INTEGRATION'	=> array(
					'disp_total_images'			=> array('lang' => 'DISP_TOTAL_IMAGES',				'validate' => 'bool',	'type' => 'radio:yes_no'),
					'profile_user_images'		=> array('lang' => 'DISP_USER_IMAGES_PROFILE',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'profile_pega'				=> array('lang' => 'DISP_PERSONAL_ALBUM_PROFILE',	'validate' => 'bool',	'type' => 'radio:yes_no'),
					'rrc_profile_mode'			=> array('lang' => 'RRC_PROFILE_MODE',				'validate' => 'int',	'type' => 'custom',			'explain' => true,	'method' => 'rrc_modes'),
					'rrc_profile_items'			=> array('lang' => 'RRC_PROFILE_ITEMS',				'validate' => 'int',	'type' => 'text:7:3'),
					'rrc_profile_display'		=> array('lang' => 'RRC_DISPLAY_OPTIONS',			'validate' => 'int',	'type' => 'custom',			'method' => 'rrc_display'),
					//'rrc_profile_pegas'			=> array('lang' => 'RRC_GINDEX_PGALLERIES',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					//'viewtopic_icon'			=> array('lang' => 'DISP_VIEWTOPIC_ICON',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					//'viewtopic_images'			=> array('lang' => 'DISP_VIEWTOPIC_IMAGES',			'validate' => 'bool',	'type' => 'radio:yes_no'),
					//'viewtopic_link'			=> array('lang' => 'DISP_VIEWTOPIC_LINK',			'validate' => 'bool',	'type' => 'radio:yes_no'),
				),

				'INDEX_SETTINGS'	=> array(
					'pegas_index_album'		=> array('lang' => 'PERSONAL_ALBUM_INDEX',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					//'pegas_index_random'	=> array('lang'	=> 'RANDOM_ON_INDEX',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					'pegas_index_rnd_count'	=> array('lang'	=> 'RANDOM_ON_INDEX_COUNT',	'validate' => 'int',	'type' => 'text:7:3'),
					//'pegas_index_recent'	=> array('lang'	=> 'RECENT_ON_INDEX',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					'pegas_index_rct_count'	=> array('lang'	=> 'RECENT_ON_INDEX_COUNT',	'validate' => 'int',	'type' => 'text:7:3'),
					'disp_login'			=> array('lang' => 'DISP_LOGIN',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					'disp_whoisonline'		=> array('lang' => 'DISP_WHOISONLINE',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'disp_birthdays'		=> array('lang' => 'DISP_BIRTHDAYS',		'validate' => 'bool',	'type' => 'radio:yes_no'),
					'disp_statistic'		=> array('lang' => 'DISP_STATISTIC',		'validate' => 'bool',	'type' => 'radio:yes_no'),
				),
			),
			//'tpl'	=> 'my_custom_templatefile',
		),
	);

	/**
	 * Disabled Radio Buttons
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function disabled_boolean($value, $key)
	{
		global $user;

		$tpl = '';

		$tpl .= "<label><input type=\"radio\" name=\"config[$key]\" value=\"1\" disabled=\"disabled\" class=\"radio\" /> " . $user->lang['YES'] . '</label>';
		$tpl .= "<label><input type=\"radio\" id=\"$key\" name=\"config[$key]\" value=\"0\" checked=\"checked\" disabled=\"disabled\"  class=\"radio\" /> " . $user->lang['NO'] . '</label>';

		return $tpl;
	}

	/**
	 * Select sort method
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function sort_method_select($value, $key)
	{
		global $user;

		$sort_method_options = '';

		$sort_method_options .= '<option' . (($value == 't') ? ' selected="selected"' : '') . " value='t'>" . $user->lang['TIME'] . '</option>';
		$sort_method_options .= '<option' . (($value == 'n') ? ' selected="selected"' : '') . " value='n'>" . $user->lang['IMAGE_NAME'] . '</option>';
		$sort_method_options .= '<option' . (($value == 'vc') ? ' selected="selected"' : '') . " value='vc'>" . $user->lang['GALLERY_VIEWS'] . '</option>';
		$sort_method_options .= '<option' . (($value == 'u') ? ' selected="selected"' : '') . " value='u'>" . $user->lang['USERNAME'] . '</option>';
		$sort_method_options .= '<option' . (($value == 'ra') ? ' selected="selected"' : '') . " value='ra'>" . $user->lang['RATING'] . '</option>';
		$sort_method_options .= '<option' . (($value == 'r') ? ' selected="selected"' : '') . " value='r'>" . $user->lang['RATES_COUNT'] . '</option>';
		$sort_method_options .= '<option' . (($value == 'c') ? ' selected="selected"' : '') . " value='c'>" . $user->lang['COMMENTS'] . '</option>';
		$sort_method_options .= '<option' . (($value == 'lc') ? ' selected="selected"' : '') . " value='lc'>" . $user->lang['NEW_COMMENT'] . '</option>';

		return "<select name=\"config[$key]\" id=\"$key\">$sort_method_options</select>";
	}

	/**
	 * Select sort order
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function sort_order_select($value, $key)
	{
		global $user;

		$sort_order_options = '';

		$sort_order_options .= '<option' . (($value == 'd') ? ' selected="selected"' : '') . " value='d'>" . $user->lang['SORT_DESCENDING'] . '</option>';
		$sort_order_options .= '<option' . (($value == 'a') ? ' selected="selected"' : '') . " value='a'>" . $user->lang['SORT_ASCENDING'] . '</option>';

		return "<select name=\"config[$key]\" id=\"$key\">$sort_order_options</select>";
	}

	/**
	 * Radio Buttons for GD library
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function gd_radio($value, $key)
	{
		global $phpbb_container;
		$phpbb_ext_gallery_core_file = $phpbb_container->get('phpbbgallery.core.file.tool');
		$key_gd1	= ($value == $phpbb_ext_gallery_core_file::GDLIB1) ? ' checked="checked"' : '';
		$key_gd2	= ($value == $phpbb_ext_gallery_core_file::GDLIB2) ? ' checked="checked"' : '';

		$tpl = '';

		$tpl .= "<label><input type=\"radio\" name=\"config[$key]\" value=\"" . $phpbb_ext_gallery_core_file::GDLIB1 . "\" $key_gd1 class=\"radio\" /> GD1</label>";
		$tpl .= "<label><input type=\"radio\" id=\"$key\" name=\"config[$key]\" value=\"" . $phpbb_ext_gallery_core_file::GDLIB2 . "\" $key_gd2  class=\"radio\" /> GD2</label>";

		return $tpl;
	}

	/**
	 * Display watermark
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function watermark_source($value, $key)
	{
		global $user;

		return generate_board_url() . "<br /><input type=\"text\" name=\"config[$key]\" id=\"$key\" value=\"$value\" size =\"40\" maxlength=\"125\" /><br /><img src=\"" . generate_board_url() . "/$value\" alt=\"" . $user->lang['WATERMARK'] . "\" />";
	}

	/**
	 * Display watermark
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function watermark_position($value, $key)
	{
		global $user;

		$phpbb_ext_gallery_core_constants = new \phpbbgallery\core\constants();

		$x_position_options = $y_position_options = '';

		$x_position_options .= '<option' . (($value & $phpbb_ext_gallery_core_constants::WATERMARK_TOP) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_constants::WATERMARK_TOP . "'>" . $user->lang['WATERMARK_POSITION_TOP'] . '</option>';
		$x_position_options .= '<option' . (($value & $phpbb_ext_gallery_core_constants::WATERMARK_MIDDLE) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_constants::WATERMARK_MIDDLE . "'>" . $user->lang['WATERMARK_POSITION_MIDDLE'] . '</option>';
		$x_position_options .= '<option' . (($value & $phpbb_ext_gallery_core_constants::WATERMARK_BOTTOM) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_constants::WATERMARK_BOTTOM . "'>" . $user->lang['WATERMARK_POSITION_BOTTOM'] . '</option>';

		$y_position_options .= '<option' . (($value & $phpbb_ext_gallery_core_constants::WATERMARK_LEFT) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_constants::WATERMARK_LEFT . "'>" . $user->lang['WATERMARK_POSITION_LEFT'] . '</option>';
		$y_position_options .= '<option' . (($value & $phpbb_ext_gallery_core_constants::WATERMARK_CENTER) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_constants::WATERMARK_CENTER . "'>" . $user->lang['WATERMARK_POSITION_CENTER'] . '</option>';
		$y_position_options .= '<option' . (($value & $phpbb_ext_gallery_core_constants::WATERMARK_RIGHT) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_constants::WATERMARK_RIGHT . "'>" . $user->lang['WATERMARK_POSITION_RIGHT'] . '</option>';

		// Cheating is an evil-thing, but most times it's successful, that's why it is used.
		return "<input type='hidden' name='config[$key]' value='$value' /><select name='" . $key . "_x' id='" . $key . "_x'>$x_position_options</select><select name='" . $key . "_y' id='" . $key . "_y'>$y_position_options</select>";
	}

	/**
	 * Select the link destination
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function uc_select($value, $key)
	{
		global $user;

		$sort_order_options = '';//phpbb_gallery_plugins::uc_select($value, $key);

		if ($key != 'link_imagepage')
		{
			$sort_order_options .= '<option' . (($value == 'image_page') ? ' selected="selected"' : '') . " value='image_page'>" . $user->lang['UC_LINK_IMAGE_PAGE'] . '</option>';
		}
		else
		{
			$sort_order_options .= '<option' . (($value == 'next') ? ' selected="selected"' : '') . " value='next'>" . $user->lang['UC_LINK_NEXT'] . '</option>';
		}
		$sort_order_options .= '<option' . (($value == 'image') ? ' selected="selected"' : '') . " value='image'>" . $user->lang['UC_LINK_IMAGE'] . '</option>';
		$sort_order_options .= '<option' . (($value == 'none') ? ' selected="selected"' : '') . " value='none'>" . $user->lang['UC_LINK_NONE'] . '</option>';

		return "<select name='config[$key]' id='$key'>$sort_order_options</select>"
			. (($key == 'link_thumbnail') ? '<br /><input class="checkbox" type="checkbox" name="update_bbcode" id="update_bbcode" value="update_bbcode" /><label for="update_bbcode">' .  $user->lang['UPDATE_BBCODE'] . '</label>' : '');
	}

	/**
	 * Select RRC-Config on gallery/index.php and in the profile
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function rrc_modes($value, $key)
	{
		global $user, $phpbb_container;

		$phpbb_ext_gallery_core_block = $phpbb_container->get('phpbbgallery.core.block');

		$rrc_mode_options = '';

		$rrc_mode_options .= "<option value='" . $phpbb_ext_gallery_core_block::MODE_NONE . "'>" . $user->lang['RRC_MODE_NONE'] . '</option>';
		$rrc_mode_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::MODE_RECENT) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::MODE_RECENT . "'>" . $user->lang['RRC_MODE_RECENT'] . '</option>';
		$rrc_mode_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::MODE_RANDOM) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::MODE_RANDOM . "'>" . $user->lang['RRC_MODE_RANDOM'] . '</option>';
		if ($key != 'rrc_profile_mode')
		{
			$rrc_mode_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::MODE_COMMENT) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::MODE_COMMENT . "'>" . $user->lang['RRC_MODE_COMMENTS'] . '</option>';
		}

		// Cheating is an evil-thing, but most times it's successful, that's why it is used.
		return "<input type='hidden' name='config[$key]' value='$value' /><select name='" . $key . "[]' multiple='multiple' id='$key'>$rrc_mode_options</select>";
	}

	/**
	 * Select RRC display options
	 * @param $value
	 * @param $key
	 * @return string
	 */
	function rrc_display($value, $key)
	{
		global $user, $phpbb_container;
		// Init gallery block class
		$phpbb_ext_gallery_core_block = $phpbb_container->get('phpbbgallery.core.block');

		$rrc_display_options = '';

		$rrc_display_options .= "<option value='" . $phpbb_ext_gallery_core_block::DISPLAY_NONE . "'>" . $user->lang['RRC_DISPLAY_NONE'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_ALBUMNAME) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_ALBUMNAME . "'>" . $user->lang['RRC_DISPLAY_ALBUMNAME'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_COMMENTS) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_COMMENTS . "'>" . $user->lang['RRC_DISPLAY_COMMENTS'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_IMAGENAME) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_IMAGENAME . "'>" . $user->lang['RRC_DISPLAY_IMAGENAME'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_IMAGETIME) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_IMAGETIME . "'>" . $user->lang['RRC_DISPLAY_IMAGETIME'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_IMAGEVIEWS) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_IMAGEVIEWS . "'>" . $user->lang['RRC_DISPLAY_IMAGEVIEWS'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_USERNAME) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_USERNAME . "'>" . $user->lang['RRC_DISPLAY_USERNAME'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_RATINGS) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_RATINGS . "'>" . $user->lang['RRC_DISPLAY_RATINGS'] . '</option>';
		$rrc_display_options .= '<option' . (($value & $phpbb_ext_gallery_core_block::DISPLAY_IP) ? ' selected="selected"' : '') . " value='" . $phpbb_ext_gallery_core_block::DISPLAY_IP . "'>" . $user->lang['RRC_DISPLAY_IP'] . '</option>';

		// Cheating is an evil-thing, but most times it's successful, that's why it is used.
		return "<input type='hidden' name='config[$key]' value='$value' /><select name='" . $key . "[]' multiple='multiple' id='$key'>$rrc_display_options</select>";
	}

	/**
	 * BBCode-Template
	 * @param $value
	 * @return string
	 */
	function bbcode_tpl($value)
	{
		global $phpbb_gallery_url;
		$gallery_url = $phpbb_gallery_url->path('full');

		if ($value == 'image_page')
		{
			$bbcode_tpl = '<a href="' . $gallery_url . 'image/{NUMBER}"><img src="' . $gallery_url . 'image/{NUMBER}/mini" alt="{NUMBER}" /></a>';
		}
		else if ($value == 'image')
		{
			$bbcode_tpl = '<a href="' . $gallery_url . 'image/{NUMBER}/source"><img src="' . $gallery_url . 'image/{NUMBER}/mini" alt="{NUMBER}" /></a>';
		}
		else
		{
			$bbcode_tpl = '<img src="' . $gallery_url . 'image/{NUMBER}/mini" alt="{NUMBER}" />';
		}

		return $bbcode_tpl;
	}
}
