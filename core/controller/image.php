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
	public function __construct(\phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\db\driver\driver_interface $db, \phpbb\event\dispatcher $dispatcher, \phpbb\pagination $pagination, \phpbb\template\template $template, \phpbb\user $user, \phpbbgallery\core\album\display $display, \phpbbgallery\core\album\loader $loader, \phpbbgallery\core\album\album $album, \phpbbgallery\core\image\image $image, \phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\config $gallery_config, \phpbbgallery\core\auth\level $auth_level, \phpbbgallery\core\url $url, \phpbbgallery\core\misc $misc, $albums_table, $images_table, $users_table)
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
		$this->gallery_config = $gallery_config;
		$this->auth_level = $auth_level;
		$this->url = $url;
		$this->misc = $misc;
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
			//$s_user_allowed = (($this->data['image_user_id'] == $this->user->data['user_id']) && ($album_data['album_status'] != phpbb_ext_gallery_core_album::STATUS_LOCKED));
			$s_user_allowed = (($this->data['image_user_id'] == $this->user->data['user_id']) && ($album_data['album_status'] != 1));

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

	
	public function edit($image_id)
	{
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
		if (!$this->gallery_auth->acl_check('i_edit', $album_id, $owner_id) || ($image_status == \phpbbgallery\core\image\image::STATUS_ORPHAN) || !$this->gallery_auth->acl_check('m_edit', $album_id, $owner_id))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		if ($submit)
		{
			if (!check_form_key('gallery'))
			{
				trigger_error('FORM_INVALID');
			}

			$image_desc = request_var('message', array(''), true);
			$image_desc = $image_desc[0];
			$image_name = request_var('image_name', array(''), true);
			$image_name = $image_name[0];

			$message_parser				= new \phpbbgallery\core\parser\parse_message();
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
				'image_allow_comments'		=> request_var('allow_comments', 0),
			);

			$errors = array();
			if (empty($sql_ary['image_name_clean']))
			{
				$errors[] = $user->lang['MISSING_IMAGE_NAME'];
			}

			if (!$this->gallery_config->get('allow_comments') || !$this->gallery_config->get('comment_user_control'))
			{
				unset($sql_ary['image_allow_comments']);
			}

			$change_image_count = false;
			if ($this->gallery_auth->acl_check('m_edit', $album_id, $album_data['album_user_id']))
			{
				$user_data = $this->image->get_new_author_info(request_var('change_author', '', true));
				if ($user_data)
				{
					$sql_ary = array_merge($sql_ary, array(
						'image_user_id'			=> $user_data['user_id'],
						'image_username'		=> $user_data['username'],
						'image_username_clean'	=> utf8_clean_string($user_data['username']),
						'image_user_colour'		=> $user_data['user_colour'],
					));

					if ($image_data['image_status'] != $this->image->get_status_unaproved())
					{
						$change_image_count = true;
					}
				}
				else if (request_var('change_author', '', true))
				{
					$errors[] = $user->lang['INVALID_USERNAME'];
				}
			}

			$move_to_personal = request_var('move_to_personal', 0);
			if ($move_to_personal)
			{
				$personal_album_id = 0;
				if ($user->data['user_id'] != $image_data['image_user_id'])
				{
					$image_user = new \phpbbgallery\core\user($db, $image_data['image_user_id']);
					$personal_album_id = $image_user->get_data('personal_album_id');

					// The User has no personal album, moderators can created that without the need of permissions
					if (!$personal_album_id)
					{
						$personal_album_id = $this->album->generate_personal_album($image_data['image_username'], $image_data['image_user_id'], $image_data['image_user_colour'], $image_user);
					}
				}
				else
				{
					$personal_album_id = $this->user->get_data('personal_album_id');
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

			$rotate = request_var('rotate', array(0));
			$rotate = (isset($rotate[0])) ? $rotate[0] : 0;
			if ($this->gallery_config->get('allow_rotate') && ($rotate > 0) && (($rotate % 90) == 0))
			{
				$image_tools = new \phpbbgallery\core\file\file();
				$image_tools->set_image_options($this->gallery_config->get('max_filesize'), $this->gallery_config->get('max_height'), $this->gallery_config->get('max_width'));
				$image_tools->set_image_data($this->url->path('upload') . $image_data['image_filename']);

				/*if (($image_data['image_has_exif'] != phpbb_gallery_exif::UNAVAILABLE) && ($image_data['image_has_exif'] != phpbb_gallery_exif::DBSAVED))
				{
					// Store exif-data to database if there are any and we didn't already do that.
					$exif = new phpbb_gallery_exif($image_tools->image_source);
					$exif->read();
					$sql_ary['image_has_exif'] = $exif->status;
					$sql_ary['image_exif_data'] = $exif->serialized;
				}*/

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
					WHERE image_id = ' . $image_id;
				$this->db->sql_query($sql);

				$this->album->update_info($album_data['album_id']);
				if ($move_to_personal && $personal_album_id)
				{
					$this->album->update_info($personal_album_id);
				}

				if ($change_image_count)
				{
					$new_user = new phpbb_gallery_user($db, $user_data['user_id'], false);
					$new_user->update_images(1);
					$old_user = new phpbb_gallery_user($db, $image_data['image_user_id'], false);
					$old_user->update_images(-1);
				}

				if ($this->user->data['user_id'] != $image_data['image_user_id'])
				{
					add_log('gallery', $image_data['image_album_id'], $image_id, 'LOG_GALLERY_EDITED', $image_name);
				}

				$message = $this->user->lang['IMAGES_UPDATED_SUCCESSFULLY'];
				$message .= '<br /><br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
				$message .= '<br /><br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');
				meta_refresh(3, $image_backlink);
				trigger_error($message);
			}
			$disp_image_data = array_merge($disp_image_data, $sql_ary);
		}

		$message_parser				= new \phpbbgallery\core\parser\parse_message();
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
			'S_ALBUM_ACTION'		=> append_sid('./gallery/image/'. $image_id .'/edit'),
			'ERROR'				=> (isset($error)) ? $error : '',

			'U_VIEW_IMAGE'		=> $this->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id"),
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
	public function delete($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$album_data = $this->album->get_info($album_id);
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$album_loginlink = './ucp.php?mode=login';
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('i_delete', $album_id, $owner_id) || ($image_status == \phpbbgallery\core\image\image::STATUS_ORPHAN))
		{
			if (!$this->gallery_auth->acl_check('m_dele', $album_id, $owner_id))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		$s_hidden_fields = build_hidden_fields(array(
			'album_id'		=> $album_id,
			'image_id'		=> $image_id,
			'mode'			=> 'delete',
		));
		
		$image_backlink = append_sid('./gallery/image/'. $image_id);
		$album_backlink = append_sid('./gallery/album/'. $image_data['image_album_id']);
		
		if (confirm_box(true))
		{
			$this->image->handle_counter($image_id, false);
			$this->image->delete_images(array($image_id), array($image_id => $image_data['image_filename']));
			$this->album->update_info($album_id);

			$message = $this->user->lang['DELETED_IMAGE'] . '<br />';
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

			if ($this->user->data['user_id'] != $image_data['image_user_id'])
			{
				add_log('gallery', $image_data['image_album_id'], $image_id, 'LOG_GALLERY_DELETED', $image_data['image_name']);
			}

			meta_refresh(3, $album_backlink);
			trigger_error($message);
		}
		else
		{
			if (isset($_POST['cancel']))
			{
				$message = $this->user->lang['DELETED_IMAGE_NOT'] . '<br />';
				$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
				meta_refresh(3, $image_backlink);
				trigger_error($message);
			}
			else
			{
				confirm_box(false, 'DELETE_IMAGE2', $s_hidden_fields);
			}
		}
	}
	
	/**
	 * @param	int		$album_id
	 * @param	array	$album_data
	 */
	protected function check_permissions($album_id, $owner_id, $image_status)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
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
				//return $this->error('NOT_AUTHORISED', 403);
				redirect('/gallery/album/' . $album_id);
			}
		}
		if (!$this->gallery_auth->acl_check('m_status', $album_id, $owner_id) && ($image_status == \phpbbgallery\core\image\image::STATUS_UNAPPROVED))
		{
			//return $this->error('NOT_AUTHORISED', 403);
			redirect('/gallery/album/' . $album_id);
		}
	}
}
