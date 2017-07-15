<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

class image
{
	protected $data;

	/**
	 * Constructor
	 *
	 * @param \phpbb\request\request $request
	 * @param \phpbb\auth\auth $auth Gallery auth object
	 * @param \phpbb\config\config $config Config object
	 * @param \phpbb\controller\helper $helper Controller helper object
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db Database object
	 * @param \phpbb\event\dispatcher $dispatcher Event dispatcher object
	 * @param \phpbb\pagination $pagination Pagination object
	 * @param \phpbb\template\template $template Template object
	 * @param \phpbb\user $user User object
	 * @param \phpbbgallery\core\album\display $display Albums display object
	 * @param \phpbbgallery\core\album\loader $loader Albums display object
	 * @param \phpbbgallery\core\album\album $album
	 * @param \phpbbgallery\core\image\image $image
	 * @param \phpbbgallery\core\auth\auth $gallery_auth
	 * @param \phpbbgallery\core\user $gallery_user
	 * @param \phpbbgallery\core\config $gallery_config
	 * @param \phpbbgallery\core\auth\level $auth_level Gallery auth level object
	 * @param \phpbbgallery\core\url $url
	 * @param \phpbbgallery\core\misc $misc
	 * @param \phpbbgallery\core\comment $comment
	 * @param \phpbbgallery\core\report $report
	 * @param \phpbbgallery\core\notification\helper $notification_helper
	 * @param \phpbbgallery\core\log $gallery_log
	 * @param \phpbbgallery\core\moderate $moderate
	 * @param \phpbbgallery\core\rating $gallery_rating
	 * @param \phpbbgallery\core\block $block
	 * @param ContainerInterface $phpbb_container
	 * @param string $albums_table Gallery albums table
	 * @param string $images_table Gallery images table
	 * @param string $users_table Gallery users table
	 * @param                                                           $table_comments
	 * @param                                                           $phpbb_root_path
	 * @param                                                           $php_ext
	 * @internal param \phpbbgallery\core\comment $gallery_comment Gallery comment class
	 */
	public function __construct(\phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper,
	\phpbb\db\driver\driver_interface $db, \phpbb\event\dispatcher $dispatcher, \phpbb\pagination $pagination,
	\phpbb\template\template $template, \phpbb\user $user, \phpbbgallery\core\album\display $display, \phpbbgallery\core\album\loader $loader, \phpbbgallery\core\album\album $album,
	\phpbbgallery\core\image\image $image, \phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\user $gallery_user, \phpbbgallery\core\config $gallery_config,
	\phpbbgallery\core\auth\level $auth_level, \phpbbgallery\core\url $url, \phpbbgallery\core\misc $misc, \phpbbgallery\core\comment $comment, \phpbbgallery\core\report $report,
	\phpbbgallery\core\notification\helper $notification_helper, \phpbbgallery\core\log $gallery_log, \phpbbgallery\core\moderate $moderate, \phpbbgallery\core\rating $gallery_rating,
	\phpbbgallery\core\block $block, ContainerInterface $phpbb_container,
	$albums_table, $images_table, $users_table, $table_comments, $phpbb_root_path, $php_ext)
	{
		$this->request = $request;
		$this->auth = $auth;
		$this->config = $config;
		$this->helper = $helper;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->pagination = $pagination;
		$this->template = $template;
		$this->user = $user;
		$this->display = $display;
		$this->loader = $loader;
		$this->album = $album;
		$this->image = $image;
		$this->gallery_auth = $gallery_auth;
		$this->gallery_user = $gallery_user;
		$this->gallery_config = $gallery_config;
		$this->auth_level = $auth_level;
		$this->url = $url;
		$this->misc = $misc;
		$this->comment = $comment;
		$this->report = $report;
		$this->notification_helper = $notification_helper;
		$this->gallery_log = $gallery_log;
		$this->moderate = $moderate;
		$this->gallery_rating = $gallery_rating;
		$this->block = $block;
		$this->phpbb_container = $phpbb_container;
		$this->table_albums = $albums_table;
		$this->table_images = $images_table;
		$this->table_users = $users_table;
		$this->table_comments = $table_comments;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Image Controller
	 *    Route: gallery/image_id/{image_id}
	 *
	 * @param int $image_id Image ID
	 * @param int $page
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function base($image_id, $page = 0)
	{

		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		try
		{
			$sql = 'SELECT *
			FROM ' . $this->table_images . '
			WHERE image_id = ' . (int) $image_id;
			$result = $this->db->sql_query($sql);
			$this->data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$this->data)
			{
				// Image does not exist
				throw new \OutOfBoundsException('INVALID_IMAGE');
			}

			$this->loader->load($this->data['image_album_id']);
		}
		catch (\Exception $e)
		{
			throw new \phpbb\exception\http_exception(404, 'INVALID_IMAGE');
		}

		$album_id = (int) $this->data['image_album_id'];
		$album_data = $this->loader->get($album_id);
		$this->check_permissions($album_id, $album_data['album_user_id'], $this->data['image_status'], $album_data['album_auth_access']);
		$this->display->generate_navigation($album_data);
		if (!$this->user->data['is_bot'] && isset($this->user->data['session_page']) && (strpos($this->user->data['session_page'], '&image_id=' . $image_id) === false || isset($this->user->data['session_created'])))
		{
			$sql = 'UPDATE ' . $this->table_images . '
				SET image_view_count = image_view_count + 1
				WHERE image_id = ' . $image_id;
			$this->db->sql_query($sql);
		}

		// Do stuff here

		$page_title = $this->data['image_name'];
		if ($page > 1)
		{
			$page_title .= ' - ' . $this->user->lang('PAGE_TITLE_NUMBER', $page);
		}

		$s_allowed_delete = $s_allowed_edit = $s_allowed_status = false;
		if (($this->gallery_auth->acl_check('m_', $album_id, $album_data['album_user_id']) || ($this->data['image_user_id'] == $this->user->data['user_id'])) && ($this->user->data['user_id'] != ANONYMOUS))
		{
			//$s_user_allowed = (($this->data['image_user_id'] == $this->user->data['user_id']) && ($album_data['album_status'] != phpbb_ext_gallery_core_album::STATUS_LOCKED));
			$s_user_allowed = (($this->data['image_user_id'] == $this->user->data['user_id']) && ($album_data['album_status'] != 1));

			$s_allowed_delete = (($this->gallery_auth->acl_check('i_delete', $album_id, $album_data['album_user_id']) && $s_user_allowed) || $this->gallery_auth->acl_check('m_delete', $album_id, $album_data['album_user_id']));
			$s_allowed_edit = (($this->gallery_auth->acl_check('i_edit', $album_id, $album_data['album_user_id']) && $s_user_allowed) || $this->gallery_auth->acl_check('m_edit', $album_id, $album_data['album_user_id']));
			$s_quick_mod = ($s_allowed_delete || $s_allowed_edit || $this->gallery_auth->acl_check('m_status', $album_id, $album_data['album_user_id']) || $this->gallery_auth->acl_check('m_move', $album_id, $album_data['album_user_id']));

			$this->user->add_lang_ext('phpbbgallery/core', 'gallery_mcp');
			$this->template->assign_vars(array(
				'S_MOD_ACTION'		=> $this->helper->route('phpbbgallery_core_moderate_image', array('image_id' => (int) $image_id)),
				'S_QUICK_MOD'		=> $s_quick_mod,
				'S_QM_MOVE'			=> $this->gallery_auth->acl_check('m_move', $album_id, $album_data['album_user_id']),
				'S_QM_EDIT'			=> $s_allowed_edit,
				'S_QM_DELETE'		=> $s_allowed_delete,
				'S_QM_REPORT'		=> $this->gallery_auth->acl_check('m_report', $album_id, $album_data['album_user_id']),
				'S_QM_STATUS'		=> $this->gallery_auth->acl_check('m_status', $album_id, $album_data['album_user_id']),

				'S_IMAGE_REPORTED'		=> $this->data['image_reported'] ? true : false,
				'U_IMAGE_REPORTED'		=> ($this->data['image_reported']) ? $this->helper->route('phpbbgallery_core_moderate_image', array('image_id' => (int) $image_id)) : '',
				'S_STATUS_APPROVED'		=> ($this->data['image_status'] == \phpbbgallery\core\block::STATUS_APPROVED),
				'S_STATUS_UNAPPROVED'	=> ($this->data['image_status'] == \phpbbgallery\core\block::STATUS_UNAPPROVED),
				'S_STATUS_LOCKED'		=> ($this->data['image_status'] == \phpbbgallery\core\block::STATUS_LOCKED),
			));
		}
		$image_desc = generate_text_for_display($this->data['image_desc'], $this->data['image_desc_uid'], $this->data['image_desc_bitfield'], 7);

		// Let's see if we can get next end prev
		$sort_key	= $this->request->variable('sk', ($album_data['album_sort_key']) ? $album_data['album_sort_key'] : $this->config['phpbb_gallery_default_sort_key']);
		$sort_dir	= $this->request->variable('sd', ($album_data['album_sort_dir']) ? $album_data['album_sort_dir'] : $this->config['phpbb_gallery_default_sort_dir']);

		if (in_array($sort_key, array('r', 'ra')))
		{
			$sql_help_sort = ', image_id ' . (($sort_dir == 'd') ? 'ASC' : 'DESC');
		}
		else
		{
			$sql_help_sort = ', image_id ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
		}

		$limit_days = array();
		$sort_by_text = array(
			't'		=> $this->user->lang['TIME'],
			'n'		=> $this->user->lang['IMAGE_NAME'],
			'vc'	=> $this->user->lang['GALLERY_VIEWS'],
			'u'		=> $this->user->lang['SORT_USERNAME'],
		);
		$sort_by_sql = array(
			't'		=> 'image_time',
			'n'		=> 'image_name_clean',
			'vc'	=> 'image_view_count',
			'u'		=> 'image_username_clean',
		);

		if ($this->config['phpbb_gallery_allow_rates'])
		{
			$sort_by_text['ra'] = $this->user->lang['RATING'];
			$sort_by_sql['ra'] = 'image_rate_points';
			$sort_by_text['r'] = $this->user->lang['RATES_COUNT'];
			$sort_by_sql['r'] = 'image_rates';
		}
		if ($this->config['phpbb_gallery_allow_comments'])
		{
			$sort_by_text['c'] = $this->user->lang['COMMENTS'];
			$sort_by_sql['c'] = 'image_comments';
			$sort_by_text['lc'] = $this->user->lang['NEW_COMMENT'];
			$sort_by_sql['lc'] = 'image_last_comment';
		}
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);
		$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
		$sql_sort_order .= $sql_help_sort;

		// Let's see if there is prieveus image
		$sql = 'SELECT *
			FROM ' . $this->table_images . '
			WHERE image_album_id = ' . (int) $album_id . "
				AND image_status <> 3
			ORDER BY $sql_sort_order" . $sql_help_sort;
		$result = $this->db->sql_query($sql);
		$images_array = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$images_array[] = $row;
		}
		$cur = 0;
		foreach ($images_array as $id => $var)
		{
			if ($var['image_id'] == $image_id)
			{
				$cur = $id;
			}
		}
		$next = $prev = false;
		if (count($images_array) > $cur + 1)
		{
			$next = array(
				'image_id'	=> $images_array[$cur + 1]['image_id'],
				'image_name'	=> $images_array[$cur + 1]['image_name'],
			);
		}
		if ($cur > 0)
		{
			$prev = array(
				'image_id'	=> $images_array[$cur - 1]['image_id'],
				'image_name'	=> $images_array[$cur - 1]['image_name'],
			);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'UC_NEXT_IMAGE'		=> ($next ? ($this->gallery_config->get('disp_nextprev_thumbnail') ? '<a href="' . $this->helper->route('phpbbgallery_core_image', array('image_id' => $next['image_id'])) . '"><img style="max-width: 70px; max-height: 70px;" src="' . $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $next['image_id'])) . '" alt="' . $next['image_name'] . '"></a>' : '<a href="' . $this->helper->route('phpbbgallery_core_image', array('image_id' => $next['image_id'])) . '">' . $next['image_name'] . ' &raquo;&raquo; </a>') : ''),
			'UC_PREV_IMAGE'		=> ($prev ? ($this->gallery_config->get('disp_nextprev_thumbnail') ? '<a href="' . $this->helper->route('phpbbgallery_core_image', array('image_id' => $prev['image_id'])) . '"><img style="max-width: 70px; max-height: 70px;" src="' . $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $prev['image_id'])) . '" alt="' . $prev['image_name'] . '"></a>' : '<a href="' . $this->helper->route('phpbbgallery_core_image', array('image_id' => $prev['image_id'])) . '">&laquo;&laquo; ' . $prev['image_name'] . '</a>') : ''),
			'U_VIEW_ALBUM'		=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id)),
			'UC_IMAGE'			=> $this->helper->route('phpbbgallery_core_image_file_medium', array('image_id' => (int) $image_id)),
			//'UC_IMAGE_ACTION'	=> $this->gallery_config->get('link_imagepage') == 'none' ? '' : $this->gallery_config->get('link_imagepage') == 'image' ? $this->helper->route('phpbbgallery_core_image_file_source', array('image_id' => $image_id)) : $next && $this->gallery_config->get('link_imagepage') == 'next' ? $this->helper->route('phpbbgallery_core_image', array('image_id' => $next['image_id'])) : '',

			'U_DELETE'			=> ($s_allowed_delete) ? $this->helper->route('phpbbgallery_core_image_delete', array('image_id' => $image_id)) : '',
			'U_EDIT'			=> ($s_allowed_edit) ? $this->helper->route('phpbbgallery_core_image_edit', array('image_id' => $image_id)) : '',
			'U_REPORT'			=> ($this->gallery_auth->acl_check('i_report', $album_id, $album_data['album_user_id']) && ($this->data['image_user_id'] != $this->user->data['user_id'])) ? $this->helper->route('phpbbgallery_core_image_report', array('image_id' => $image_id)) : '',
			'U_STATUS'			=> ($s_allowed_status) ? $this->helper->route('phpbbgallery_core_moderate_image', array('image_id' => $image_id)) : '',

			'CONTEST_RANK'		=> ($this->data['image_contest_rank']) ? $this->user->lang('CONTEST_RESULT_' . $this->data['image_contest_rank']) : '',
			'IMAGE_NAME'		=> $this->data['image_name'],
			'IMAGE_DESC'		=> $image_desc,
			'IMAGE_BBCODE'		=> ($this->config['allow_bbcode']) ? '[image]' . (int) $image_id . '[/image]' : '',
			'IMAGE_IMGURL_BBCODE'	=> ($this->config['phpbb_gallery_disp_image_url']) ? '[url=' . $this->url->get_uri($this->helper->route('phpbbgallery_core_image', array('image_id'	=> $image_id))) . '][img]' . $this->url->get_uri($this->helper->route('phpbbgallery_core_image_file_mini', array('image_id'	=> $image_id))) . '[/img][/url]' : '',
			'IMAGE_URL'			=> ($this->config['phpbb_gallery_disp_image_url']) ? $this->url->get_uri($this->helper->route('phpbbgallery_core_image_file_medium', array('image_id'	=> $image_id))) : '',
			'IMAGE_TIME'		=> $this->user->format_date($this->data['image_time']),
			'IMAGE_VIEW'		=> $this->data['image_view_count'],
			'POSTER_IP'			=> ($this->auth->acl_get('a_')) ? $this->data['image_user_ip'] : '',
			'U_POSTER_WHOIS'	=> ($this->auth->acl_get('a_')) ? append_sid('mcp', 'mode=whois&amp;ip=' . $this->data['image_user_ip']) : '',

			'S_ALBUM_ACTION'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id)),

			'U_RETURN_LINK'		=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id)),
			'S_RETURN_LINK'		=> $this->user->lang('RETURN_TO', $album_data['album_name']),
		));

		switch ($this->gallery_config->get('link_imagepage'))
		{
			case 'image':
				$this->template->assign_vars(array(
					'UC_IMAGE_ACTION'	=> $this->helper->route('phpbbgallery_core_image_file_source', array('image_id' => $image_id))
				));
			break;
			case 'next':
				if ($next)
				{
					$this->template->assign_vars(array(
						'UC_IMAGE_ACTION'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $next['image_id']))
					));
				}
			break;
		}
		$image_data = $this->data;

		/**
		* Event view image
		*
		* @event phpbbgallery.core.viewimage
		* @var	int		image_id		id of the image we are viewing
		* @var	array	image_data		All the data related to the image
		* @var	array	album_data		All the data related to the album image is part of
		* @var	string	page_title		Page title
		* @since 1.2.0
		*/
		$vars = array('image_id', 'image_data', 'album_data', 'page_title');
		extract($this->dispatcher->trigger_event('phpbbgallery.core.viewimage', compact($vars)));

		$this->data = $image_data;

		$user_id = $this->data['image_user_id'];
		$sql = $this->db->sql_build_query('SELECT', array(
			'SELECT'	=> 'u.*, gu.personal_album_id, gu.user_images',
			'FROM'		=> array(USERS_TABLE => 'u'),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->table_users => 'gu'),
					'ON'	=> 'gu.user_id = u.user_id'
				),
			),

			'WHERE'		=> 'u.user_id = ' . $this->data['image_user_id'],
		));
		$result = $this->db->sql_query($sql);

		$user_cache = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->gallery_user->add_user_to_cache($user_cache, $row);
		}
		$this->db->sql_freeresult($result);

		$user_cache[$user_id]['username'] = ($this->data['image_username']) ? $this->data['image_username'] : $this->user->lang['GUEST'];
		$this->template->assign_vars(array(
			'POSTER_FULL'		=> get_username_string('full', $user_id, $user_cache[$user_id]['username'], $user_cache[$user_id]['user_colour']),
			'POSTER_COLOUR'		=> get_username_string('colour', $user_id, $user_cache[$user_id]['username'], $user_cache[$user_id]['user_colour']),
			'POSTER_USERNAME'	=> get_username_string('username', $user_id, $user_cache[$user_id]['username'], $user_cache[$user_id]['user_colour']),
			'U_POSTER'			=> get_username_string('profile', $user_id, $user_cache[$user_id]['username'], $user_cache[$user_id]['user_colour']),

			'POSTER_SIGNATURE'		=> $user_cache[$user_id]['sig'],
			'POSTER_RANK_TITLE'		=> $user_cache[$user_id]['rank_title'],
			'POSTER_RANK_IMG'		=> $user_cache[$user_id]['rank_image'],
			'POSTER_RANK_IMG_SRC'	=> $user_cache[$user_id]['rank_image_src'],
			'POSTER_JOINED'		=> $user_cache[$user_id]['joined'],
			'POSTER_POSTS'		=> $user_cache[$user_id]['posts'],
			'POSTER_AVATAR'		=> $user_cache[$user_id]['avatar'],
			'POSTER_WARNINGS'	=> $user_cache[$user_id]['warnings'],
			'POSTER_AGE'		=> $user_cache[$user_id]['age'],

			'POSTER_ONLINE_IMG'			=> ($user_id == ANONYMOUS || !$this->config['load_onlinetrack']) ? '' : (($user_cache[$user_id]['online']) ? $this->user->img('icon_user_online', 'ONLINE') : $this->user->img('icon_user_offline', 'OFFLINE')),
			'S_POSTER_ONLINE'			=> ($user_id == ANONYMOUS || !$this->config['load_onlinetrack']) ? false : (($user_cache[$user_id]['online']) ? true : false),

			'U_POSTER_PROFILE'		=> $user_cache[$user_id]['profile'],
			'U_POSTER_SEARCH'		=> $user_cache[$user_id]['search'],
			'U_POSTER_PM'			=> ($user_id != ANONYMOUS && $this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && ($user_cache[$user_id]['allow_pm'] || $this->auth->acl_gets('a_', 'm_'))) ? append_sid('./ucp.php', 'i=pm&amp;mode=compose&amp;u=' . $user_id) : '',
			'U_POSTER_EMAIL'		=> ($this->auth->acl_gets('a_') || !$this->config['board_hide_emails']) ? $user_cache[$user_id]['email'] : false,
			'U_POSTER_JABBER'		=> $user_cache[$user_id]['jabber'],

			'U_POSTER_GALLERY'			=> $user_cache[$user_id]['gallery_album'],
			'POSTER_GALLERY_IMAGES'		=> $user_cache[$user_id]['gallery_images'],
			'U_POSTER_GALLERY_SEARCH'	=> $user_cache[$user_id]['gallery_search'],
		));

		// Add ratings
		if ($this->gallery_config->get('allow_rates'))
		{
			$rating = $this->gallery_rating;
			$rating->loader($image_id, $image_data, $album_data);

			$user_rating = $rating->get_user_rating($this->user->data['user_id']);

			// Check: User didn't rate yet, has permissions, it's not the users own image and the user is logged in
			if (!$user_rating && $rating->is_allowed())
			{
				$rating->display_box();
			}
			$this->template->assign_vars(array(
				'IMAGE_RATING'			=> $rating->get_image_rating($user_rating),
				'S_ALLOWED_TO_RATE'		=> (!$user_rating && $rating->is_allowed()),
				'S_VIEW_RATE'			=> ($this->gallery_auth->acl_check('i_rate', $album_id, $album_data['album_user_id'])) ? true : false,
				'S_RATE_ACTION'		=> $this->helper->route('phpbbgallery_core_image_rate', array('image_id'	=> $image_id)),
			));
			unset($rating);
		}
		/**
		* Posting comment
		*/
		$comments_disabled = (!$this->gallery_config->get('allow_comments') || ($this->gallery_config->get('comment_user_control') && !$image_data['image_allow_comments']));
		if (!$comments_disabled && $this->gallery_auth->acl_check('c_post', $album_id, $album_data['album_user_id']) && ($album_data['album_status'] != $this->block->get_album_status_locked()) && (($image_data['image_status'] != $this->block->get_image_status_locked()) || $this->gallery_auth->acl_check('m_status', $album_id, $album_data['album_user_id'])))
		{
			add_form_key('gallery');
			$this->user->add_lang('posting');
			$this->url->_include('functions_posting', 'phpbb');

			$bbcode_status	= ($this->config['allow_bbcode']) ? true : false;
			$smilies_status	= ($this->config['allow_smilies']) ? true : false;
			$img_status		= ($bbcode_status) ? true : false;
			$url_status		= ($this->config['allow_post_links']) ? true : false;
			$flash_status	= false;
			$quote_status	= true;

			if (!function_exists('generate_smilies'))
			{
				include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
			}
			if (!function_exists('display_custom_bbcodes'))
			{
				include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
			}
			// Build custom bbcodes array
			display_custom_bbcodes();

			// Build smilies array
			generate_smilies('inline', 0);

			//$s_hide_comment_input = (time() < ($album_data['contest_start'] + $album_data['contest_end'])) ? true : false;
			$s_hide_comment_input = false;

			$this->template->assign_vars(array(
				'S_ALLOWED_TO_COMMENT'	=> true,
				'S_HIDE_COMMENT_INPUT'	=> $s_hide_comment_input,
			//	'CONTEST_COMMENTS'		=> sprintf($user->lang['CONTEST_COMMENTS_STARTS'], $user->format_date(($album_data['contest_start'] + $album_data['contest_end']), false, true)),

				'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($this->user->lang['BBCODE_IS_ON'], '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>') : sprintf($this->user->lang['BBCODE_IS_OFF'], '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>'),
				'IMG_STATUS'			=> ($img_status) ? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
				'FLASH_STATUS'			=> ($flash_status) ? $this->user->lang['FLASH_IS_ON'] : $this->user->lang['FLASH_IS_OFF'],
				'SMILIES_STATUS'		=> ($smilies_status) ? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
				'URL_STATUS'			=> ($bbcode_status && $url_status) ? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],
				'S_SIGNATURE_CHECKED'	=> ($this->user->optionget('attachsig')) ? ' checked="checked"' : '',

				'S_BBCODE_ALLOWED'		=> $bbcode_status,
				'S_SMILIES_ALLOWED'		=> $smilies_status,
				'S_LINKS_ALLOWED'		=> $url_status,
				'S_BBCODE_IMG'			=> $img_status,
				'S_BBCODE_URL'			=> $url_status,
				'S_BBCODE_FLASH'		=> $flash_status,
				'S_BBCODE_QUOTE'		=> $quote_status,
				'L_COMMENT_LENGTH'		=> sprintf($this->user->lang['COMMENT_LENGTH'], $this->gallery_config->get('comment_length')),
			));

			if ($this->misc->display_captcha('comment'))
			{
				$captcha = $this->phpbb_container->get('captcha.factory')->get_instance($this->config['captcha_plugin']);
				$captcha->init(CONFIRM_POST);
				$s_captcha_hidden_fields = '';
				$this->template->assign_vars(array(
					'S_CONFIRM_CODE'		=> true,
					'CAPTCHA_TEMPLATE'		=> $captcha->get_template(),
				));

			}

			// Different link, when we rate and dont comment
			if (!$s_hide_comment_input)
			{
				//$this->template->assign_var('S_COMMENT_ACTION', append_sid($this->url->path('full') . 'comment/' . $image_id . '/add/0'));
				$this->template->assign_var('S_COMMENT_ACTION', $this->helper->route('phpbbgallery_core_comment_add', array('image_id' => $image_id, 'comment_id' => 0)));
			}
		}
		else if ($this->gallery_config->get('comment_user_control') && !$image_data['image_allow_comments'])
		{
			$this->template->assign_var('S_COMMENTS_DISABLED', true);
		}

		/**
		* Listing comment
		*/
		if (($this->gallery_config->get('allow_comments') && $this->gallery_auth->acl_check('c_read', $album_id, $album_data['album_user_id'])) /*&& (time() > ($album_data['contest_start'] + $album_data['contest_end']))*/)
		{
			$this->display_comments($image_id, $this->data, $album_id, $album_data, ($page - 1) * $this->gallery_config->get('items_per_page'), $this->gallery_config->get('items_per_page'));
		}

		return $this->helper->render('gallery/viewimage_body.html', $page_title);
	}
	protected function display_comments($image_id, $image_data, $album_id, $album_data, $start, $limit)
	{
		$sort_order = ($this->request->variable('sort_order', 'ASC') == 'ASC') ? 'ASC' : 'DESC';
		$this->template->assign_vars(array(
			'S_ALLOWED_READ_COMMENTS'	=> true,
			'IMAGE_COMMENTS'			=> $image_data['image_comments'],
			'SORT_ASC'					=> ($sort_order == 'ASC') ? true : false,
		));

		if ($image_data['image_comments'] > 0)
		{
			if (!class_exists('bbcode'))
			{
				$this->url->_include('bbcode', 'phpbb');
			}

			$bbcode = new \bbcode();

			$comments = $users = $user_cache = array();
			$users[] = $image_data['image_user_id'];
			$sql = 'SELECT *
				FROM ' . $this->table_comments . '
				WHERE comment_image_id = ' . (int) $image_id . '
				ORDER BY comment_id ' . $sort_order;
			$result = $this->db->sql_query_limit($sql, $limit, $start);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$comments[] = $row;
				$users[] = $row['comment_user_id'];
				if ($row['comment_edit_count'] > 0)
				{
					$users[] = $row['comment_edit_user_id'];
				}
			}
			$this->db->sql_freeresult($result);

			$users = array_unique($users);
			$sql = $this->db->sql_build_query('SELECT', array(
				'SELECT'	=> 'u.*, gu.personal_album_id, gu.user_images',
				'FROM'		=> array(USERS_TABLE => 'u'),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array($this->table_users => 'gu'),
						'ON'	=> 'gu.user_id = u.user_id'
					),
				),

				'WHERE'		=> $this->db->sql_in_set('u.user_id', $users),
			));
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->gallery_user->add_user_to_cache($user_cache, $row);
			}
			$this->db->sql_freeresult($result);

			if ($this->config['load_onlinetrack'] && sizeof($users))
			{
				// Load online-information
				$sql = 'SELECT session_user_id, MAX(session_time) as online_time, MIN(session_viewonline) AS viewonline
					FROM ' . SESSIONS_TABLE . '
					WHERE ' . $this->db->sql_in_set('session_user_id', $users) . '
					GROUP BY session_user_id';
				$result = $this->db->sql_query($sql);

				$update_time = $this->config['load_online_time'] * 60;
				while ($row = $this->db->sql_fetchrow($result))
				{
					$user_cache[$row['session_user_id']]['online'] = (time() - $update_time < $row['online_time'] && (($row['viewonline']) || $this->auth->acl_get('u_viewonline'))) ? true : false;
				}
				//$this->db->sql_freeresult($result);
			}

			foreach ($comments as $row)
			{
				$edit_info = '';
				if ($row['comment_edit_count'] > 0)
				{
					$edit_info = ($row['comment_edit_count'] == 1) ? $this->user->lang['EDITED_TIME_TOTAL'] : $this->user->lang['EDITED_TIMES_TOTAL'];
					$edit_info = sprintf($edit_info, get_username_string('full', $user_cache[$row['comment_edit_user_id']]['user_id'], $user_cache[$row['comment_edit_user_id']]['username'], $user_cache[$row['comment_edit_user_id']]['user_colour']), $this->user->format_date($row['comment_edit_time'], false, true), $row['comment_edit_count']);
				}

				$user_id = $row['comment_user_id'];
				$user_deleted = (isset($user_cache[$user_id]) ? false : true);
				if (!$user_deleted)
				{
					if ($user_cache[$user_id]['sig'] && empty($user_cache[$user_id]['sig_parsed']))
					{
						$user_cache[$user_id]['sig'] = censor_text($user_cache[$user_id]['sig']);

						if ($user_cache[$user_id]['sig_bbcode_bitfield'])
						{
							$bbcode->bbcode_second_pass($user_cache[$user_id]['sig'], $user_cache[$user_id]['sig_bbcode_uid'], $user_cache[$user_id]['sig_bbcode_bitfield']);
						}

						$user_cache[$user_id]['sig'] = bbcode_nl2br($user_cache[$user_id]['sig']);
						$user_cache[$user_id]['sig'] = smiley_text($user_cache[$user_id]['sig']);
						$user_cache[$user_id]['sig_parsed'] = true;
					}
				}

				$this->template->assign_block_vars('commentrow', array(
					'U_COMMENT'		=> $this->helper->route('phpbbgallery_core_image', array('image_id'	=> $image_id)) . '#comment_' . $row['comment_id'],
					'COMMENT_ID'	=> $row['comment_id'],
					'TIME'			=> $this->user->format_date($row['comment_time']),
					'TEXT'			=> generate_text_for_display($row['comment'], $row['comment_uid'], $row['comment_bitfield'], 7),
					'EDIT_INFO'		=> $edit_info,
					'U_DELETE'		=> ($this->gallery_auth->acl_check('m_comments', $album_id, $album_data['album_user_id']) || ($this->gallery_auth->acl_check('c_delete', $album_id, $album_data['album_user_id']) && ($row['comment_user_id'] == $this->user->data['user_id']) && $this->user->data['is_registered'])) ? $this->helper->route('phpbbgallery_core_comment_delete', array('image_id'	=> $image_id, 'comment_id'	=> $row['comment_id'])) : '',
					'U_QUOTE'		=> ($this->gallery_auth->acl_check('c_post', $album_id, $album_data['album_user_id'])) ? $this->helper->route('phpbbgallery_core_comment_add', array('image_id'	=> $image_id, 'comment_id'	=> $row['comment_id'])) : '',
					'U_EDIT'		=> ($this->gallery_auth->acl_check('m_comments', $album_id, $album_data['album_user_id']) || ($this->gallery_auth->acl_check('c_edit', $album_id, $album_data['album_user_id']) && ($row['comment_user_id'] == $this->user->data['user_id']) && $this->user->data['is_registered'])) ? $this->helper->route('phpbbgallery_core_comment_edit', array('image_id'	=> $image_id, 'comment_id'	=> $row['comment_id'])) : '',
					'U_INFO'		=> ($this->auth->acl_get('a_')) ? $this->url->append_sid('mcp', 'mode=whois&amp;ip=' . $row['comment_user_ip']) : '',

					'POST_AUTHOR_FULL'		=> get_username_string('full', $user_id, $row['comment_username'], ($user_deleted ? '' : $user_cache[$user_id]['user_colour'])),
					'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $user_id, $row['comment_username'], ($user_deleted ? '' : $user_cache[$user_id]['user_colour'])),
					'POST_AUTHOR'			=> get_username_string('username', $user_id, $row['comment_username'], ($user_deleted ? '' : $user_cache[$user_id]['user_colour'])),
					'U_POST_AUTHOR'			=> get_username_string('profile', $user_id, $row['comment_username'], ($user_deleted ? '' : $user_cache[$user_id]['user_colour'])),

					'SIGNATURE'			=> ($row['comment_signature'] && !$user_deleted) ? $user_cache[$user_id]['sig'] : '',
					'RANK_TITLE'		=> $user_deleted ? '' : $user_cache[$user_id]['rank_title'],
					'RANK_IMG'			=> $user_deleted ? '' : $user_cache[$user_id]['rank_image'],
					'RANK_IMG_SRC'		=> $user_deleted ? '' : $user_cache[$user_id]['rank_image_src'],
					'POSTER_JOINED'		=> $user_deleted ? '' : $user_cache[$user_id]['joined'],
					'POSTER_POSTS'		=> $user_deleted ? '' : $user_cache[$user_id]['posts'],
					'POSTER_FROM'		=> isset($user_cache[$user_id]['from']) ? $user_cache[$user_id]['from'] : '',
					'POSTER_AVATAR'		=> $user_deleted ? '' : $user_cache[$user_id]['avatar'],
					'POSTER_WARNINGS'	=> $user_deleted ? '' : $user_cache[$user_id]['warnings'],
					'POSTER_AGE'		=> $user_deleted ? '' : $user_cache[$user_id]['age'],

					'MINI_POST_IMG'		=> $this->user->img('icon_post_target', 'POST'),
					'ICQ_STATUS_IMG'	=> isset($user_cache[$user_id]['icq_status_img']) ? $user_cache[$user_id]['icq_status_img'] : '',
					'ONLINE_IMG'		=> ($user_id == ANONYMOUS || !$this->config['load_onlinetrack']) ? '' : ($user_deleted ? '' : (($user_cache[$user_id]['online']) ? $this->user->img('icon_user_online', 'ONLINE') : $this->user->img('icon_user_offline', 'OFFLINE'))),
					'S_ONLINE'			=> ($user_id == ANONYMOUS || !$this->config['load_onlinetrack']) ? false : ($user_deleted ? '' : (($user_cache[$user_id]['online']) ? true : false)),

					'U_PROFILE'		=> $user_deleted ? '' : $user_cache[$user_id]['profile'],
					'U_SEARCH'		=> $user_deleted ? '' : $user_cache[$user_id]['search'],
					'U_PM'			=> (!$user_deleted && $user_id != ANONYMOUS && $this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && ($user_cache[$user_id]['allow_pm'] || $this->auth->acl_gets('a_', 'm_'))) ? $this->url->append_sid('phpbb', 'ucp', 'i=pm&amp;mode=compose&amp;u=' . $user_id) : '',
					'U_EMAIL'		=> $user_deleted ? '' : $user_cache[$user_id]['email'],
					'U_WWW'			=> isset($user_cache[$user_id]['www']) ? $user_cache[$user_id]['www'] : '',
					'U_ICQ'			=> isset($user_cache[$user_id]['icq']) ? $user_cache[$user_id]['icq'] : '',
					'U_AIM'			=> isset($user_cache[$user_id]['aim']) ? $user_cache[$user_id]['aim'] : '',
					'U_MSN'			=> isset($user_cache[$user_id]['msn']) ? $user_cache[$user_id]['msn'] : '',
					'U_YIM'			=> isset($user_cache[$user_id]['yim']) ? $user_cache[$user_id]['yim'] : '',
					'U_JABBER'		=> isset($user_cache[$user_id]['jabber']) ? $user_cache[$user_id]['jabber'] : '',

					'U_GALLERY'			=> $user_deleted ? '' : $user_cache[$user_id]['gallery_album'],
					'GALLERY_IMAGES'	=> $user_deleted ? '' : $user_cache[$user_id]['gallery_images'],
					'U_GALLERY_SEARCH'	=> $user_deleted ? '' : $user_cache[$user_id]['gallery_search'],
				));
			}
			$this->db->sql_freeresult($result);

			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_image',
					'phpbbgallery_core_image_page',
				),
				'params' => array(
					'image_id' => (int) $image_id,
				),
			), 'pagination', 'page', $image_data['image_comments'], $limit, $start);

			$this->template->assign_vars(array(
				'TOTAL_COMMENTS'				=> $this->user->lang('VIEW_IMAGE_COMMENTS', $image_data['image_comments']),
				//'S_SELECT_SORT_DIR'			=> $s_sort_dir,
				//'S_SELECT_SORT_KEY'			=> $s_sort_key,
			));
		}
	}

	// Edit image
	public function edit($image_id)
	{
		//we cheat a little but we will make good later
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$album_data = $this->album->get_info($album_id);
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->display->generate_navigation($album_data);
		add_form_key('gallery');
		$submit = $this->request->variable('submit', false);
		$image_backlink = append_sid('./gallery/image/'. $image_id);
		$album_backlink = append_sid('./gallery/album/'. $image_data['image_album_id']);
		$disp_image_data = $image_data;
		$owner_id = $image_data['image_user_id'];
		$album_loginlink = './ucp.php?mode=login';
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('i_edit', $album_id, $album_data['album_user_id']) || ($image_data['image_status'] == \phpbbgallery\core\block::STATUS_ORPHAN))
		{
			if (!$this->gallery_auth->acl_check('m_edit', $album_id, $album_data['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		if ($submit)
		{
			if (!check_form_key('gallery'))
			{
				trigger_error('FORM_INVALID');
			}

			$image_desc = $this->request->variable('message', array(''), true);
			$image_desc = $image_desc[0];
			$image_name = $this->request->variable('image_name', array(''), true);
			$image_name = $image_name[0];
			if (strlen($image_desc) > $this->gallery_config->get('description_length'))
			{
				trigger_error($this->user->lang('DESC_TOO_LONG'));
			}
			// Create message parser instance
			if (!class_exists('parse_message'))
			{
				include_once($this->phpbb_root_path . 'includes/message_parser.' . $this->php_ext);
			}
			$message_parser = new \parse_message();
			$message_parser->message	= utf8_normalize_nfc($image_desc);
			if ($message_parser->message)
			{
				$message_parser->parse(true, true, true, true, false, true, true, true);
			}

			$sql_ary = array(
				'image_name'				=> $image_name,
				'image_name_clean'			=> utf8_clean_string($image_name),
				'image_desc'				=> $message_parser->message,
				'image_desc_uid'			=> $message_parser->bbcode_uid,
				'image_desc_bitfield'		=> $message_parser->bbcode_bitfield,
				'image_allow_comments'		=> $this->request->variable('allow_comments', 0),
			);

			$errors = array();
			if (empty($sql_ary['image_name_clean']))
			{
				$errors[] = $this->user->lang['MISSING_IMAGE_NAME'];
			}

			if (!$this->gallery_config->get('allow_comments') || !$this->gallery_config->get('comment_user_control'))
			{
				unset($sql_ary['image_allow_comments']);
			}

			$change_image_count = false;
			if ($this->gallery_auth->acl_check('m_edit', $album_id, $album_data['album_user_id']))
			{
				$user_data = $this->image->get_new_author_info($this->request->variable('change_author', '', true));
				if ($user_data)
				{
					$sql_ary = array_merge($sql_ary, array(
						'image_user_id'			=> $user_data['user_id'],
						'image_username'		=> $user_data['username'],
						'image_username_clean'	=> utf8_clean_string($user_data['username']),
						'image_user_colour'		=> $user_data['user_colour'],
					));

					if ($image_data['image_status'] != $this->block->get_image_status_unapproved())
					{
						$change_image_count = true;
					}
				}
				else if ($this->request->variable('change_author', '', true))
				{
					$errors[] = $this->user->lang['INVALID_USERNAME'];
				}
			}

			$move_to_personal = $this->request->variable('move_to_personal', 0);
			if ($move_to_personal)
			{
				$personal_album_id = 0;
				if ($this->user->data['user_id'] != $image_data['image_user_id'])
				{
					$image_user = $this->gallery_user;
					$image_user->set_user_id($image_data['image_user_id']);
					$personal_album_id = $image_user->get_data('personal_album_id');

					// The User has no personal album, moderators can created that without the need of permissions
					if (!$personal_album_id)
					{
						$personal_album_id = $this->album->generate_personal_album($image_data['image_username'], $image_data['image_user_id'], $image_data['image_user_colour'], $image_user);
					}
				}
				else
				{
					$personal_album_id = $this->gallery_user->get_data('personal_album_id');
					if (!$personal_album_id && $this->gallery_auth->acl_check('i_upload', $this->gallery_auth->get_own_album()))
					{
						$personal_album_id = $this->album->generate_personal_album($image_data['image_username'], $image_data['image_user_id'], $image_data['image_user_colour'], phpbb_gallery::$user);
					}
				}

				if ($personal_album_id)
				{
					$sql_ary['image_album_id'] = $personal_album_id;
				}
			}

			$rotate = $this->request->variable('rotate', array(0));
			$rotate = (isset($rotate[0])) ? $rotate[0] : 0;
			if ($this->gallery_config->get('allow_rotate') && ($rotate > 0) && (($rotate % 90) == 0))
			{
				$image_tools = new \phpbbgallery\core\file\file($this->request, $this->url, $this->gallery_config, 2);
				$image_tools->set_image_options($this->gallery_config->get('max_filesize'), $this->gallery_config->get('max_height'), $this->gallery_config->get('max_width'));
				$image_tools->set_image_data($this->url->path('upload') . $image_data['image_filename']);

				// Rotate the image
				$image_tools->rotate_image($rotate, $this->gallery_config->get('allow_rotate'));
				if ($image_tools->rotated)
				{
					$image_tools->write_image($image_tools->image_source, $this->gallery_config->get('jpg_quality'), true);
				}
				@unlink($this->url->path('thumbnail') . $image_data['image_filename']);
				@unlink($this->url->path('medium') . $image_data['image_filename']);
			}

			$error = implode('<br />', $errors);

			if (!$error)
			{
				$sql = 'UPDATE ' . $this->table_images . '
					SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE image_id = ' . (int) $image_id;
				$this->db->sql_query($sql);

				$this->album->update_info($album_data['album_id']);
				if ($move_to_personal && $personal_album_id)
				{
					$this->album->update_info($personal_album_id);
				}

				if ($change_image_count)
				{
					$new_user = new \phpbbgallery\core\user($this->db, $this->dispatcher, $this->user, $this->config, $this->auth, $this->table_users, $this->phpbb_root_path, $this->php_ext);
					$new_user->set_user_id($user_data['user_id']);
					$new_user->update_images(1);
					$old_user = new \phpbbgallery\core\user($this->db, $this->dispatcher, $this->user, $this->config, $this->auth, $this->table_users, $this->phpbb_root_path, $this->php_ext);
					$old_user->set_user_id($image_data['image_user_id']);
					$old_user->update_images(-1);
				}

				if ($this->user->data['user_id'] != $image_data['image_user_id'])
				{
					$this->gallery_log->add_log('moderator', 'edit', $image_data['image_album_id'], $image_id, array('LOG_GALLERY_EDITED', $image_name));
				}

				$message = $this->user->lang['IMAGES_UPDATED_SUCCESSFULLY'];
				$message .= '<br /><br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
				$message .= '<br /><br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');
				$this->url->meta_refresh(3, $image_backlink);
				trigger_error($message);
			}
			$disp_image_data = array_merge($disp_image_data, $sql_ary);
		}

		if (!class_exists('bbcode'))
		{
			include($this->phpbb_root_path . 'includes/bbcode.' . $this->php_ext);
		}
		if (!class_exists('parse_message'))
		{
			include_once($this->phpbb_root_path . 'includes/message_parser.' . $this->php_ext);
		}
		$message_parser				= new \parse_message();
		$message_parser->message	= $disp_image_data['image_desc'];
		$message_parser->decode_message($disp_image_data['image_desc_uid']);

		$page_title = $disp_image_data['image_name'];

		$this->template->assign_block_vars('image', array(
			'U_IMAGE'		=> $this->image->generate_link('thumbnail', 'plugin', $image_id, $image_data['image_name'], $album_id),
			'IMAGE_NAME'	=> $disp_image_data['image_name'],
			'IMAGE_DESC'	=> $message_parser->message,
		));

		$this->template->assign_vars(array(
			'L_DESCRIPTION_LENGTH'	=> $this->user->lang('DESCRIPTION_LENGTH', $this->gallery_config->get('description_length')),
			'S_EDIT'			=> true,
			'S_ALBUM_ACTION'		=> $this->helper->route('phpbbgallery_core_image_edit', array('image_id'	=> $image_id)),
			'ERROR'				=> (isset($error)) ? $error : '',

			'U_VIEW_IMAGE'		=> $this->helper->route('phpbbgallery_core_image', array('image_id'	=> $image_id)),
			'IMAGE_NAME'		=> $image_data['image_name'],

			'S_CHANGE_AUTHOR'	=> $this->gallery_auth->acl_check('m_edit', $album_id, $album_data['album_user_id']),
			'U_FIND_USERNAME'	=> $this->url->append_sid('phpbb', 'memberlist', 'mode=searchuser&amp;form=postform&amp;field=change_author&amp;select_single=true'),
			'S_COMMENTS_ENABLED'=> $this->gallery_config->get('allow_comments') && $this->gallery_config->get('comment_user_control'),
			'S_ALLOW_COMMENTS'	=> $image_data['image_allow_comments'],

			'NUM_IMAGES'		=> 1,
			'S_ALLOW_ROTATE'	=> ($this->gallery_config->get('allow_rotate') && function_exists('imagerotate')),
			//'S_MOVE_PERSONAL'	=> (($this->galley_auth->acl_check('i_upload', $this->galley_auth::OWN_ALBUM) || phpbb_gallery::$user->get_data('personal_album_id')) || ($user->data['user_id'] != $image_data['image_user_id'])) ? true : false,
			'S_MOVE_MODERATOR'	=> ($this->user->data['user_id'] != $image_data['image_user_id']) ? true : false,
		));

		return $this->helper->render('gallery/posting_body.html', $page_title);
	}

	// Delete image
	public function delete($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$album_data = $this->album->get_info($album_id);
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$album_loginlink = './ucp.php?mode=login';
		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id'	=> $image_id));
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id'	=> $image_data['image_album_id']));
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('i_delete', $album_id, $album_data['album_user_id']) || ($image_data['image_status'] == \phpbbgallery\core\block::STATUS_ORPHAN))
		{
			if (!$this->gallery_auth->acl_check('m_delete', $album_id, $album_data['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		$s_hidden_fields = build_hidden_fields(array(
			'album_id'		=> $album_id,
			'image_id'		=> $image_id,
			'mode'			=> 'delete',
		));

		if (confirm_box(true))
		{
			$this->image->handle_counter($image_id, false);
			$this->moderate->delete_images(array($image_id), array($image_id => $image_data['image_filename']));
			$this->album->update_info($album_id);

			$message = $this->user->lang['DELETED_IMAGE'] . '<br />';
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

			if ($this->user->data['user_id'] != $image_data['image_user_id'])
			{
				$this->gallery_log->add_log('moderator', 'delete', $image_data['image_album_id'], $image_id, array('LOG_GALLERY_DELETED', $image_data['image_name']));
			}
			// So we need to see if there are still unapproved images in the album
			$this->notification_helper->read('approval', $album_id);
			$this->url->meta_refresh(3, $album_backlink);
			trigger_error($message);
		}
		else
		{
			if (isset($_POST['cancel']))
			{
				$message = $this->user->lang['DELETED_IMAGE_NOT'] . '<br />';
				$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
				$this->url->meta_refresh(3, $image_backlink);
				trigger_error($message);
			}
			else
			{
				confirm_box(false, 'DELETE_IMAGE2', $s_hidden_fields);
			}
		}
	}
	// Report image
	public function report($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$album_data = $this->album->get_info($album_id);
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$album_loginlink = './ucp.php?mode=login';
		$image_backlink = append_sid('./gallery/image/'. $image_id);
		$album_backlink = append_sid('./gallery/album/'. $image_data['image_album_id']);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('i_report', $album_id, $album_data['album_user_id']) || ($image_data['image_user_id'] == $this->user->data['user_id']))
		{
			$this->misc->not_authorised($image_backlink, $image_loginlink);
		}
		add_form_key('gallery');
		$submit = $this->request->variable('submit', false);
		$error = '';
		if ($submit)
		{
			if (!check_form_key('gallery'))
			{
				trigger_error('FORM_INVALID');
			}

			$report_message = $this->request->variable('message', '', true);
			$error = '';
			if ($report_message == '')
			{
				$error = $this->user->lang['MISSING_REPORT_REASON'];
				$submit = false;
			}

			if (!$error && $image_data['image_reported'])
			{
				$error = $this->user->lang['IMAGE_ALREADY_REPORTED'];
			}

			if (!$error)
			{
				$data = array(
					'report_album_id'			=> (int) $album_id,
					'report_image_id'			=> (int) $image_id,
					'report_note'				=> $report_message,
				);

				$this->report->add($data);

				$message = $this->user->lang['IMAGES_REPORTED_SUCCESSFULLY'];
				$message .= '<br /><br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
				$message .= '<br /><br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

				$this->url->meta_refresh(3, $image_backlink);
				trigger_error($message);
			}

		}

		$this->template->assign_vars(array(
			'ERROR'				=> $error,
			'U_IMAGE'			=> ($image_id) ? $this->helper->route('phpbbgallery_core_image_file_medium', array('image_id' => $image_id)) : '',
			'U_VIEW_IMAGE'		=> ($image_id) ? $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id)) : '',
			'IMAGE_RSZ_WIDTH'	=> $this->gallery_config->get('medium_width'),
			'IMAGE_RSZ_HEIGHT'	=> $this->gallery_config->get('medium_height'),

			'S_REPORT'			=> true,
			'S_ALBUM_ACTION'	=> $this->helper->route('phpbbgallery_core_image_report', array('image_id' => $image_id)),
		));

		$page_title = $this->user->lang['REPORT_IMAGE'];

		return $this->helper->render('gallery/posting_body.html', $page_title);
	}

	/**
	 * @param    int $album_id
	 * @param $owner_id
	 * @param $image_status
	 * @param $album_auth_level
	 * @internal param array $album_data
	 */
	protected function check_permissions($album_id, $owner_id, $image_status, $album_auth_level)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$zebra_array = $this->gallery_auth->get_user_zebra($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('i_view', $album_id, $owner_id) || ($image_status == \phpbbgallery\core\block::STATUS_ORPHAN) || $this->gallery_auth->get_zebra_state($zebra_array, (int) $owner_id, (int) $album_id) < (int) $album_auth_level)
		{
			if ($this->user->data['is_bot'])
			{
				// Redirect bots back to the index
				redirect($this->helper->route('phpbbgallery_core_index'));
			}

			// Display login box for guests and an error for users
			if (!$this->user->data['is_registered'])
			{
				// @todo Add "redirect after login" url
				login_box();
			}
			else
			{
				//return $this->error('NOT_AUTHORISED', 403);
				redirect('/gallery/album/' . $album_id);
			}
		}
		if (!$this->gallery_auth->acl_check('m_status', $album_id, $owner_id) && ($image_status == \phpbbgallery\core\block::STATUS_UNAPPROVED))
		{
			//return $this->error('NOT_AUTHORISED', 403);
			redirect('/gallery/album/' . $album_id);
		}
	}
}
