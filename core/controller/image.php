<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

class image
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\db\driver\driver */
	protected $db;

	/* @var \phpbb\event\dispatcher */
	protected $dispatcher;

	/* @var \phpbb\pagination */
	protected $pagination;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbbgallery\core\album\display */
	protected $display;

	/* @var \phpbbgallery\core\album\loader */
	protected $loader;

	/* @var \phpbbgallery\core\auth\auth */
	protected $gallery_auth;

	/* @var \phpbbgallery\core\auth\level */
	protected $auth_level;

	/* @var array */
	protected $data;

	/* @var string */
	protected $table_albums;

	/* @var string */
	protected $table_images;

	/* @var string */
	protected $table_users;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth		Auth object
	* @param \phpbb\config\config		$config		Config object
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbb\db\driver\driver	$db			Database object
	* @param \phpbb\event\dispatcher	$dispatcher	Event dispatcher object
	* @param \phpbb\pagination			$pagination	Pagination object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	* @param \phpbbgallery\core\album\display	$display	Albums display object
	* @param \phpbbgallery\core\album\loader	$loader	Albums display object
	* @param \phpbbgallery\core\auth\auth	$auth	Gallery auth object
	* @param \phpbbgallery\core\auth\level	$auth_level	Gallery auth level object
	* @param string						$images_table	Gallery images table
	* @param string						$albums_table	Gallery albums table
	* @param string						$users_table	Gallery users table
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\db\driver\driver $db, \phpbb\event\dispatcher $dispatcher, \phpbb\pagination $pagination, \phpbb\template\template $template, \phpbb\user $user, \phpbbgallery\core\album\display $display, \phpbbgallery\core\album\loader $loader, \phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\auth\level $auth_level, $albums_table, $images_table, $users_table)
	{
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
		$this->gallery_auth = $gallery_auth;
		$this->auth_level = $auth_level;
		$this->table_albums = $albums_table;
		$this->table_images = $images_table;
		$this->table_users = $users_table;
	}

	/**
	* Image Controller
	*	Route: gallery/image_id/{image_id}
	*
	* @param int	$image_id	Image ID
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
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
			return $this->error($e->getMessage(), 404);
		}

		$album_id = (int) $this->data['image_album_id'];
		$album_data = $this->loader->get($album_id);
		$this->check_permissions($album_id, $album_data['album_user_id'], $this->data['image_status']);
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
			$s_user_allowed = (($this->data['image_user_id'] == $this->user->data['user_id']) && ($album_data['album_status'] != phpbb_ext_gallery_core_album::STATUS_LOCKED));

			$s_allowed_delete = (($this->gallery_auth->acl_check('i_delete', $album_id, $album_data['album_user_id']) && $s_user_allowed) || $this->gallery_auth->acl_check('m_delete', $album_id, $album_data['album_user_id']));
			$s_allowed_edit = (($this->gallery_auth->acl_check('i_edit', $album_id, $album_data['album_user_id']) && $s_user_allowed) || $this->gallery_auth->acl_check('m_edit', $album_id, $album_data['album_user_id']));
			$s_quick_mod = ($s_allowed_delete || $s_allowed_edit || $this->gallery_auth->acl_check('m_status', $album_id, $album_data['album_user_id']) || $this->gallery_auth->acl_check('m_move', $album_id, $album_data['album_user_id']));

			$this->user->add_lang_ext('phpbbgallery/core', 'gallery_mcp');
			$this->template->assign_vars(array(
				'S_MOD_ACTION'		=> $this->helper->route('phpbbgallery_moderate_image', array('image_id' => $image_id)),
				'S_QUICK_MOD'		=> $s_quick_mod,
				'S_QM_MOVE'			=> $this->gallery_auth->acl_check('m_move', $album_id, $album_data['album_user_id']),
				'S_QM_EDIT'			=> $s_allowed_edit,
				'S_QM_DELETE'		=> $s_allowed_delete,
				'S_QM_REPORT'		=> $this->gallery_auth->acl_check('m_report', $album_id, $album_data['album_user_id']),
				'S_QM_STATUS'		=> $this->gallery_auth->acl_check('m_status', $album_id, $album_data['album_user_id']),

				'S_IMAGE_REPORTED'		=> $this->data['image_reported'],
				'U_IMAGE_REPORTED'		=> ($this->data['image_reported']) ? $this->helper->route('phpbbgallery_moderate_image', array('image_id' => $image_id)) : '',
				'S_STATUS_APPROVED'		=> ($this->data['image_status'] == \phpbbgallery\core\image\image::STATUS_APPROVED),
				'S_STATUS_UNAPPROVED'	=> ($this->data['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED),
				'S_STATUS_LOCKED'		=> ($this->data['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED),
			));
		}

		$image_desc = generate_text_for_display($this->data['image_desc'], $this->data['image_desc_uid'], $this->data['image_desc_bitfield'], 7);

		$this->template->assign_vars(array(
			'U_VIEW_ALBUM'		=> $this->helper->route('phpbbgallery_album', array('album_id' => $album_id)),
			'UC_IMAGE'			=> $this->helper->route('phpbbgallery_image_file_medium', array('image_id' => $image_id)),//\phpbbgallery\core\image\image::generate_link('medium', $this->config['phpbb_gallery_link_imagepage'], $image_id, $this->data['image_name'], $album_id, ((substr($this->data['image_filename'], 0 -3) == 'gif') ? true : false), false, ''),

			'EDIT_IMG'			=> $this->user->img('icon_post_edit', 'EDIT_IMAGE'),
			'DELETE_IMG'		=> $this->user->img('icon_post_delete', 'DELETE_IMAGE'),
			'REPORT_IMG'		=> $this->user->img('icon_post_report', 'REPORT_IMAGE'),
			'STATUS_IMG'		=> $this->user->img('icon_post_info', 'STATUS_IMAGE'),
			'U_DELETE'			=> ($s_allowed_delete) ? $this->helper->route('phpbbgallery_image_delete', array('image_id' => $image_id)) : '',
			'U_EDIT'			=> ($s_allowed_edit) ? $this->helper->route('phpbbgallery_image_edit', array('image_id' => $image_id)) : '',
			'U_REPORT'			=> ($this->gallery_auth->acl_check('i_report', $album_id, $album_data['album_user_id']) && ($this->data['image_user_id'] != $this->user->data['user_id'])) ? $this->helper->route('phpbbgallery_image_report', array('image_id' => $image_id)) : '',
			'U_STATUS'			=> ($s_allowed_status) ? $this->helper->route('phpbbgallery_moderate_image', array('image_id' => $image_id)) : '',

			'CONTEST_RANK'		=> ($this->data['image_contest_rank']) ? $this->user->lang('CONTEST_RESULT_' . $this->data['image_contest_rank']) : '',
			'IMAGE_NAME'		=> $this->data['image_name'],
			'IMAGE_DESC'		=> $image_desc,
			'IMAGE_BBCODE'		=> ($this->config['allow_bbcode']) ? '[album]' . $image_id . '[/album]' : '',
//			'IMAGE_IMGURL_BBCODE'	=> ($this->config['phpbb_gallery_disp_image_url']) ? '[url=' . $phpbb_ext_gallery->url->append_sid('full', 'image', "album_id=$album_id&amp;image_id=$image_id", true, '') . '][img]' . $phpbb_ext_gallery->url->append_sid('full', 'image', "album_id=$album_id&amp;image_id=$image_id&amp;mode=thumbnail", true, '') . '[/img][/url]' : '',
//			'IMAGE_URL'			=> ($this->config['phpbb_gallery_disp_image_url']) ? $phpbb_ext_gallery->url->append_sid('full', 'image', "album_id=$album_id&amp;image_id=$image_id", true, '') : '',
			'IMAGE_TIME'		=> $this->user->format_date($this->data['image_time']),
			'IMAGE_VIEW'		=> $this->data['image_view_count'],
			'POSTER_IP'			=> ($this->auth->acl_get('a_')) ? $this->data['image_user_ip'] : '',
			'U_POSTER_WHOIS'	=> ($this->auth->acl_get('a_')) ? append_sid('mcp', 'mode=whois&amp;ip=' . $this->data['image_user_ip']) : '',

			'S_ALBUM_ACTION'	=> $this->helper->route('phpbbgallery_image', array('image_id' => $image_id)),

			'U_RETURN_LINK'		=> $this->helper->route('phpbbgallery_album', array('album_id' => $album_id)),
			'S_RETURN_LINK'		=> $this->user->lang('RETURN_TO', $album_data['album_name']),
		));

		$image_data = $this->data;
		$vars = array('image_id', 'image_data', 'album_data', 'page_title');
		extract($this->dispatcher->trigger_event('gallery.core.viewimage', compact($vars)));
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
			\phpbbgallery\core\user::add_user_to_cache($user_cache, $row);
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
			'U_POSTER_PM'			=> ($user_id != ANONYMOUS && $this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && ($user_cache[$user_id]['allow_pm'] || $this->auth->acl_gets('a_', 'm_'))) ? append_sid('phpbb', 'ucp', 'i=pm&amp;mode=compose&amp;u=' . $user_id) : '',
			'U_POSTER_EMAIL'		=> $user_cache[$user_id]['email'],
			'U_POSTER_JABBER'		=> $user_cache[$user_id]['jabber'],

			'U_POSTER_GALLERY'			=> $user_cache[$user_id]['gallery_album'],
			'POSTER_GALLERY_IMAGES'		=> $user_cache[$user_id]['gallery_images'],
			'U_POSTER_GALLERY_SEARCH'	=> $user_cache[$user_id]['gallery_search'],
		));

		$this->template->assign_vars(array(
			'PROFILE_IMG'		=> $this->user->img('icon_user_profile', 'READ_PROFILE'),
			'SEARCH_IMG' 		=> $this->user->img('icon_user_search', 'SEARCH_USER_POSTS'),
			'PM_IMG' 			=> $this->user->img('icon_contact_pm', 'SEND_PRIVATE_MESSAGE'),
			'EMAIL_IMG' 		=> $this->user->img('icon_contact_email', 'SEND_EMAIL'),
			'JABBER_IMG'		=> $this->user->img('icon_contact_jabber', 'JABBER') ,
			'GALLERY_IMG'		=> $this->user->img('icon_contact_gallery', 'PERSONAL_ALBUM'),
		));

		return $this->helper->render('gallery/viewimage_body.html', $page_title);
	}

	/**
	 * @param	int		$album_id
	 * @param	array	$album_data
	 */
	protected function check_permissions($album_id, $owner_id, $image_status)
	{
		if (!$this->gallery_auth->acl_check('i_view', $album_id, $owner_id) || ($image_status == \phpbbgallery\core\image\image::STATUS_ORPHAN))
		{
			if ($this->user->data['is_bot'])
			{
				// Redirect bots back to the index
				redirect($this->helper->route('phpbbgallery_index'));
			}

			// Display login box for guests and an error for users
			if (!$this->user->data['is_registered'])
			{
				// @todo Add "redirect after login" url
				login_box();
			}
			else
			{
				return $this->error('NOT_AUTHORISED', 403);
			}
		}
		if (!$this->gallery_auth->acl_check('m_status', $album_id, $owner_id) && ($image_status == \phpbbgallery\core\image\image::STATUS_UNAPPROVED))
		{
			return $this->error('NOT_AUTHORISED', 403);
		}
	}

	protected function error($message, $status = 200, $title = '')
	{
		$title = $title ?: 'INFORMATION';

		$this->template->assign_vars(array(
			'MESSAGE_TITLE'		=> $this->user->lang($title),
			'MESSAGE_TEXT'		=> $message,
		));

		return $this->helper->render('message_body.html', $this->user->lang($title), $status);
	}
}
