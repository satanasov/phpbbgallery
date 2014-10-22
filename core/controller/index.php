<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbbgallery\core\album\display */
	protected $display;

	/* @var string */
	protected $root_path;

	/* @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth		Auth object
	* @param \phpbb\config\config		$config		Config object
	* @param \phpbb\db\driver\driver	$db			Database object
	* @param \phpbb\request\request		$request	Request object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbbgallery\core\album\display	$display	Albums display object
	* @param string						$root_path	Root path
	* @param string						$php_ext	php file extension
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbbgallery\core\album\display $display, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->display = $display;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Index Controller
	*	Route: gallery
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function index()
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->display->display_albums(false, $this->config['load_moderators']);

		$this->display_legend();
		$this->display_brithdays();
		$this->assign_dropdown_links('phpbbgallery_index');

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang('GALLERY'),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_index'),
		));

		return $this->helper->render('gallery/index_body.html', $this->user->lang('GALLERY'));
	}

	/**
	* Personal Index Controller
	*	Route: gallery/users
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function personal()
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->display->display_albums('personal', $this->config['load_moderators']);

		$this->assign_dropdown_links('phpbbgallery_personal');

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
			'FORUM_NAME'	=> $this->user->lang('GALLERY'),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang('PERSONAL_ALBUMS'),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_personal'),
		));

		return $this->helper->render('gallery/index_body.html', $this->user->lang('PERSONAL_ALBUMS'));
	}

	protected function assign_dropdown_links($base_route)
	{
//		$this->template->assign_vars(array(
//			'TOTAL_IMAGES'		=> ($phpbb_ext_gallery->config->get('disp_statistic')) ? $user->lang('TOTAL_IMAGES_SPRINTF', $phpbb_ext_gallery->config->get('num_images')) : '',
//			'TOTAL_COMMENTS'	=> ($phpbb_ext_gallery->config->get('allow_comments')) ? $user->lang('TOTAL_COMMENTS_SPRINTF', $phpbb_ext_gallery->config->get('num_comments')) : '',
//			'TOTAL_PGALLERIES'	=> ($phpbb_ext_gallery->auth->acl_check('a_list', phpbb_ext_gallery_core_auth::PERSONAL_ALBUM)) ? $user->lang('TOTAL_PEGAS_SPRINTF', $phpbb_ext_gallery->config->get('num_pegas')) : '',
//			'NEWEST_PGALLERIES'	=> ($phpbb_ext_gallery->config->get('num_pegas')) ? sprintf($user->lang['NEWEST_PGALLERY'], get_username_string('full', $phpbb_ext_gallery->config->get('newest_pega_user_id'), $phpbb_ext_gallery->config->get('newest_pega_username'), $phpbb_ext_gallery->config->get('newest_pega_user_colour'), '', $phpbb_ext_gallery->url->append_sid('album', 'album_id=' . $phpbb_ext_gallery->config->get('newest_pega_album_id')))) : '',
//		));

		$this->template->assign_vars(array(
			//'U_MCP'		=> ($this->gallery_auth->acl_check_global('m_')) ? $this->helper->route('phpbbgallery_mcp', array('mode' => 'overview')) : '',
			'U_MARK_ALBUMS'					=> ($this->user->data['is_registered']) ? $this->helper->route($base_route, array('hash' => generate_link_hash('global'), 'mark' => 'albums')) : '',
			'S_LOGIN_ACTION'			=> append_sid($this->root_path . 'ucp.' . $this->php_ext, 'mode=login&amp;redirect=' . urlencode($this->helper->route($base_route))),

			'U_GALLERY_SEARCH'				=> $this->helper->route('phpbbgallery_search'),
			'U_G_SEARCH_COMMENTED'			=> $this->config['phpbb_gallery_allow_comments'] ? $this->helper->route('phpbbgallery_search_commented') : '',
			'U_G_SEARCH_CONTESTS'			=> $this->config['phpbb_gallery_allow_rates'] && $this->config['phpbb_gallery_contests_ended'] ? $this->helper->route('phpbbgallery_search_contests') : '',
			'U_G_SEARCH_RECENT'				=> $this->helper->route('phpbbgallery_search_recent'),
			'U_G_SEARCH_SELF'				=> $this->helper->route('phpbbgallery_search_egosearch'),
			'U_G_SEARCH_TOPRATED'			=> $this->config['phpbb_gallery_allow_rates'] ? $this->helper->route('phpbbgallery_search_toprated') : '',
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
					AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $this->user->data['user_id'] . ')
				ORDER BY g.' . $order_legend . ' ASC';
		}
		$result = $this->db->sql_query($sql);

		$legend = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $this->user->lang['G_' . $row['group_name']] : $row['group_name'];

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
			'LEGEND'	=> implode($this->user->lang['COMMA_SEPARATOR'], $legend),
		));
	}

	protected function display_brithdays()
	{
		// Generate birthday list if required ...
		if ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->config['phpbb_gallery_disp_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			$this->template->assign_vars(array(
				'S_DISPLAY_BIRTHDAY_LIST'	=> true,
			));

			$time = $this->user->create_datetime();
			$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

			// Display birthdays of 29th february on 28th february in non-leap-years
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
