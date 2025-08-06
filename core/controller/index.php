<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\controller;

class index
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\db\driver\driver */
	protected $db;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language  */
	protected $language;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbbgallery\core\album\display */
	protected $display;

	/** @var \phpbbgallery\core\config  */
	protected $gallery_config;

	/** @var \phpbbgallery\core\auth\auth  */
	protected $gallery_auth;

	/** @var \phpbbgallery\core\search  */
	protected $gallery_search;

	/** @var \phpbb\pagination  */
	protected $pagination;

	/** @var \phpbbgallery\core\user  */
	protected $gallery_user;

	/** @var \phpbbgallery\core\image\image  */
	protected $image;

	/* @var string */
	protected $root_path;

	/* @var string */
	protected $php_ext;

	const RRC_MODE_RECENT_COMMENTS = 4;
	const RRC_MODE_RANDOM_IMAGES   = 2;
	const RRC_MODE_RECENT_IMAGES   = 1;
	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth                                          $auth      Auth object
	 * @param \phpbb\config\config                                      $config    Config object
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db        Database object
	 * @param \phpbb\request\request                                    $request   Request object
	 * @param \phpbb\template\template                                  $template  Template object
	 * @param \phpbb\user                                               $user      User object
	 * @param \phpbb\language\language                                  $language
	 * @param \phpbb\controller\helper                                  $helper    Controller helper object
	 * @param \phpbbgallery\core\album\display                          $display   Albums display object
	 * @param \phpbbgallery\core\config                                 $gallery_config
	 * @param \phpbbgallery\core\auth\auth                              $gallery_auth
	 * @param \phpbb\pagination                                         $pagination
	 * @param \phpbbgallery\core\user                                   $gallery_user
	 * @param \phpbbgallery\core\search                                 $gallery_search
	 * @param \phpbbgallery\core\image\image                            $image
	 * @param string                                                    $root_path Root path
	 * @param string                                                    $php_ext   php file extension
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\language\language $language,
		\phpbb\controller\helper $helper, \phpbbgallery\core\album\display $display, \phpbbgallery\core\config $gallery_config,
		\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\search $gallery_search, \phpbb\pagination $pagination,
		\phpbbgallery\core\user $gallery_user, \phpbbgallery\core\image\image $image,
		$root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->helper = $helper;
		$this->display = $display;
		$this->gallery_config = $gallery_config;
		$this->gallery_auth = $gallery_auth;
		$this->gallery_search = $gallery_search;
		$this->pagination = $pagination;
		$this->gallery_user = $gallery_user;
		$this->image = $image;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Index Controller
	*	Route: gallery
	*
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function base()
	{
		// Display login box for guests and an error for users
		$this->gallery_auth->load_user_permissions($this->user->data['user_id']);
		$get_albums = $this->gallery_auth->acl_album_ids('a_list');
		if (empty($get_albums) && !$this->user->data['is_registered'])
		{
			login_box();
		}
		$this->language->add_lang(array('gallery'), 'phpbbgallery/core');
		$this->display->display_albums(false, $this->config['load_moderators']);

		if ($this->gallery_config->get('pegas_index_album'))
		{
			$this->display->display_albums('personal', $this->config['load_moderators']);
		}
		else
		{
			$last_image = $this->image->get_last_image();
			if (empty($last_image))
			{
				$last_image['image_id'] = 0;
			}
			switch ($this->gallery_config->get('link_image_icon'))
			{
				case 'image_page':
					$action_image = $this->helper->route('phpbbgallery_core_image', array('image_id' => $last_image['image_id']));
				break;
				case 'image':
					$action_image = $this->helper->route('phpbbgallery_core_image_file_source', array('image_id' => $last_image['image_id']));
				break;
				default:
					$action_image = false;
				break;
			}

			$alphabet = range('a', 'z');
			$alpha_links = [];
			foreach ($alphabet as $char)
			{
				$alpha_links[] = '<a href="' . append_sid($this->helper->route('phpbbgallery_core_personal'), 'first_char=' . $char) . '">' . strtoupper($char) . '</a>';
			}
			$alpha_links[] = '<a href="' . append_sid($this->helper->route('phpbbgallery_core_personal'), 'first_char=other') . '">#</a>';

			$this->template->assign_vars(array(
				'S_USERS_PERSONAL_GALLERIES'	=> true,
				'U_USERS_PERSONAL_GALLERIES' => $this->helper->route('phpbbgallery_core_personal'),
				'U_PERSONAL_GALLERIES_IMAGES'	=> $this->gallery_config->get('num_images'),
				'U_PERSONAL_GALLERIES_LAST_IMAGE'	=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $last_image['image_id'])),
				'U_IMAGENAME'	=> ($last_image['image_id'] > 0) ? $last_image['image_name'] : false,
				'U_IMAGE_ACTION'	=> $action_image,
				'U_IMAGENAME_ACTION'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $last_image['image_id'])),
				'U_TIME'	=> ($last_image['image_id'] > 0) ?  $this->user->format_date($last_image['image_time']) : false,
				'U_UPLOADER'	=> ($last_image['image_id'] > 0) ? get_username_string('full', $last_image['image_user_id'], $last_image['image_username'], $last_image['image_user_colour']) : false,
				'ALPHABET_NAVIGATION' => implode('&nbsp;', $alpha_links),
			));
			$this->gallery_user->set_user_id($this->user->data['user_id']);
			$personal_album = $this->gallery_user->get_own_root_album();
			if ($personal_album > 0)
			{
				$this->template->assign_vars(array(
					'S_PERSONAL_ALBUM'	=> true,
					'U_PERSONAL_ALBUM'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $personal_album)),
					'U_PERSONAL_ALBUM_USER'	=> $this->user->data['username'],
					'U_PERSONAL_ALBUM_COLOR'	=> $this->user->data['user_colour'],
				));
			}
		}

		if ($this->gallery_config->get('rrc_gindex_mode'))
		{
			$config_value = $this->gallery_config->get('rrc_gindex_mode');

			$recent_comments = ($config_value & self::RRC_MODE_RECENT_COMMENTS) !== 0;
			$random_images   = ($config_value & self::RRC_MODE_RANDOM_IMAGES) !== 0;
			$recent_images   = ($config_value & self::RRC_MODE_RECENT_IMAGES) !== 0;

			// Now before build random and recent ... let's check if we have images that can build it
			if ($recent_images)
			{
				$this->template->assign_vars(array(
					'U_RECENT'	=> true,
				));
				$this->gallery_search->recent($this->gallery_config->get('pegas_index_rct_count'), -1);
			}
			if ($random_images)
			{
				$this->template->assign_vars(array(
					'U_RANDOM'	=> true,
				));
				$this->gallery_search->random($this->gallery_config->get('pegas_index_rnd_count'));
			}
			if ($recent_comments)
			{
				$this->template->assign_vars(array(
					'U_RECENT_COMMENTS'	=> true,
					'S_RECENT_COMMENTS' => $this->helper->route('phpbbgallery_core_search_commented'),
					'COMMENTS_EXPAND'	=> $this->gallery_config->get('rrc_gindex_comments') ? true : false,
				));
				$this->gallery_search->recent_comments($this->gallery_config->get('items_per_page'), 0, false);
			}
		}
		$this->display_legend();
		$this->display_birthdays();
		$this->assign_dropdown_links('phpbbgallery_core_index');

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->language->lang('GALLERY'),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_index'),
		));

		return $this->helper->render('gallery/index_body.html', $this->language->lang('GALLERY'), 200, $this->gallery_config->get('disp_whoisonline'));
	}

	/**
	 * Personal Index Controller
	 *    Route: gallery/users
	 *
	 * @param $page
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function personal($page)
	{
		// Display login box for guests and an error for users
		$this->gallery_auth->load_user_permissions($this->user->data['user_id']);
		$get_albums = $this->gallery_auth->acl_album_ids('a_list');
		if (empty($get_albums) && !$this->user->data['is_registered'])
		{
			login_box();
		}
		$this->language->add_lang(array('gallery'), 'phpbbgallery/core');
		$this->display->album_start = ($page - 1) * $this->gallery_config->get('items_per_page');
		$this->display->album_limit = $this->gallery_config->get('items_per_page');
		$this->display->album_mode = 'personal';
		$this->display->display_albums('personal', $this->config['load_moderators']);

		$this->pagination->generate_template_pagination(array(
			'routes' => array(
				'phpbbgallery_core_personal',
				'phpbbgallery_core_personal_page',),
				'params' => array()), 'pagination', 'page', $this->display->albums_total, $this->display->album_limit, $this->display->album_start
		);

		$this->template->assign_vars(array(
			'TOTAL_ALBUMS'	=> $this->language->lang('TOTAL_PEGAS_SHORT_SPRINTF', $this->display->albums_total),
		));

		if (!$this->gallery_config->get('pegas_index_album'))
		{
			$this->assign_dropdown_links('phpbbgallery_core_personal');
		}

		$first_char = $this->request->variable('first_char', '');
		$s_char_options = '<option value=""' . ((!$first_char) ? ' selected="selected"' : '') . '>' . $this->user->lang('ALL') . '</option>';
		// Loop the ASCII: a-z
		for ($i = 97; $i < 123; $i++)
		{
			$s_char_options .= '<option value="' . chr($i) . '"' . (($first_char == chr($i)) ? ' selected="selected"' : '') . '>' . chr($i - 32) . '</option>';
		}
		$s_char_options .= '<option value="other"' . (($first_char == 'other') ? ' selected="selected"' : '') . '>#</option>';

		$this->template->assign_vars(array(
			'S_CHAR_OPTIONS'				=> $s_char_options,
		));

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->language->lang('GALLERY'),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->language->lang('PERSONAL_ALBUMS'),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_personal'),
		));

		return $this->helper->render('gallery/index_body.html', $this->language->lang('PERSONAL_ALBUMS'));
	}

	protected function assign_dropdown_links($base_route)
	{
		$this->gallery_auth->load_user_permissions($this->user->data['user_id']);

		// Now let's get display options
		$show_comments = $show_random = $show_recent = false;
		$show_options = $this->gallery_config->get('rrc_gindex_mode');
		if ($show_options >= 4)
		{
			$show_comments = true;
			$show_options = $show_options - 4;
		}
		if ($show_options >= 2)
		{
			$show_random = true;
			$show_options = $show_options - 2;
		}
		if ($show_options == 1)
		{
			$show_recent = true;
		}
		$this->template->assign_vars(array(
			'TOTAL_IMAGES'		=> ($this->gallery_config->get('disp_statistic')) ? $this->language->lang('TOTAL_IMAGES_SPRINTF', $this->gallery_config->get('num_images')) : '',
			'TOTAL_COMMENTS'	=> ($this->gallery_config->get('allow_comments')) ? $this->language->lang('TOTAL_COMMENTS_SPRINTF', $this->gallery_config->get('num_comments')) : '',
			'TOTAL_PGALLERIES'	=> ($this->gallery_auth->acl_check('a_list', \phpbbgallery\core\auth\auth::PERSONAL_ALBUM)) ? $this->language->lang('TOTAL_PEGAS_SPRINTF', $this->gallery_config->get('num_pegas')) : '',
			'NEWEST_PGALLERIES'	=> ($this->gallery_config->get('num_pegas')) ? sprintf($this->language->lang('NEWEST_PGALLERY'), '<a href="' . $this->helper->route('phpbbgallery_core_album', array('album_id' => $this->gallery_config->get('newest_pega_album_id'))) . '" '. ($this->gallery_config->get('newest_pega_user_colour') ? 'class="username-coloured" style="color: #' . $this->gallery_config->get('newest_pega_user_colour') . ';"' : 'class="username"') . '>' . $this->gallery_config->get('newest_pega_username') . '</a>') : '',
		));

		$this->template->assign_vars(array(
			'U_MCP'		=> ($this->gallery_auth->acl_check_global('m_')) ? $this->helper->route('phpbbgallery_core_moderate') : '',
			'U_MARK_ALBUMS'					=> ($this->user->data['is_registered']) ? $this->helper->route($base_route, array('hash' => generate_link_hash('global'), 'mark' => 'albums')) : '',
			'S_LOGIN_ACTION'			=> append_sid($this->root_path . 'ucp.' . $this->php_ext, 'mode=login&amp;redirect=' . urlencode($this->helper->route($base_route))),

			'U_GALLERY_SEARCH'				=> $this->helper->route('phpbbgallery_core_search'),
			'U_G_SEARCH_COMMENTED'			=> $this->config['phpbb_gallery_allow_comments'] && $show_comments ? $this->helper->route('phpbbgallery_core_search_commented') : false,
			//'U_G_SEARCH_CONTESTS'			=> $this->config['phpbb_gallery_allow_rates'] && $this->config['phpbb_gallery_contests_ended'] ? $this->helper->route('phpbbgallery_core_search_contests') : '',
			'U_G_SEARCH_RECENT'				=> $show_recent ? $this->helper->route('phpbbgallery_core_search_recent') : false,
			'U_G_SEARCH_RANDOM'				=> $show_random ? $this->helper->route('phpbbgallery_core_search_random') : false,
			'U_G_SEARCH_SELF'				=> $this->helper->route('phpbbgallery_core_search_egosearch'),
			'U_G_SEARCH_TOPRATED'			=> $this->config['phpbb_gallery_allow_rates'] ? $this->helper->route('phpbbgallery_core_search_toprated') : '',
		));
	}

	protected function display_legend()
	{
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';

		// Grab group details for legend display
		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend . ' ASC';
		}
		else
		{
			$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
				FROM ' . GROUPS_TABLE . ' g
				LEFT JOIN ' . USER_GROUP_TABLE . ' ug
					ON (
						g.group_id = ug.group_id
						AND ug.user_id = ' . $this->user->data['user_id'] . '
						AND ug.user_pending = 0
					)
				WHERE g.group_legend > 0
					AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . (int) $this->user->data['user_id'] . ')
				ORDER BY g.' . $order_legend . ' ASC';
		}
		$result = $this->db->sql_query($sql);

		$legend = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $this->language->lang('G_' . $row['group_name']) : $row['group_name'];

			if ($row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile')))
			{
				$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
			}
			else
			{
				$legend[] = '<a' . $colour_text . ' href="' . append_sid($this->root_path . 'memberlist.' . $this->php_ext, 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
			}
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'LEGEND'	=> implode($this->language->lang('COMMA_SEPARATOR'), $legend),
		));
	}

	protected function display_birthdays()
	{
		// Generate birthday list if required ...
		if ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->config['phpbb_gallery_disp_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			$this->template->assign_vars(array(
				'S_DISPLAY_BIRTHDAY_LIST'	=> true,
			));

			$time = $this->user->create_datetime();
			$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

			// Display birthdays of 29th February on 28th February in non-leap-years
			$leap_year_birthdays = '';
			if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
			{
				$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
			}

			$sql = 'SELECT u.user_id, u.username, u.user_colour, u.user_birthday
				FROM ' . USERS_TABLE . ' u
				LEFT JOIN ' . BANLIST_TABLE . " b ON (u.user_id = b.ban_userid)
				WHERE (b.ban_id IS NULL
					OR b.ban_exclude = 1)
					AND (u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
					AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$birthday_username	= get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				$birthday_year		= (int) substr($row['user_birthday'], -4);
				$birthday_age		= ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

				$this->template->assign_block_vars('birthdays', array(
					'USERNAME'	=> $birthday_username,
					'AGE'		=> $birthday_age,
				));
			}
			$this->db->sql_freeresult($result);
		}
	}

}
