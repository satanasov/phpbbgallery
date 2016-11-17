<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

class album
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\db\driver\driver */
	protected $db;

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
	protected $auth;

	/* @var \phpbbgallery\core\auth\level */
	protected $auth_level;

	/* @var string */
	protected $table_images;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config Config object
	 * @param \phpbb\controller\helper $helper Controller helper object
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db Database object
	 * @param \phpbb\pagination $pagination Pagination object
	 * @param \phpbb\template\template $template Template object
	 * @param \phpbb\user $user User object
	 * @param \phpbbgallery\core\album\display $display Albums display object
	 * @param \phpbbgallery\core\album\loader $loader Albums display object
	 * @param \phpbbgallery\core\auth\auth $auth Gallery auth object
	 * @param \phpbbgallery\core\auth\level $auth_level Gallery auth level object
	 * @param \phpbbgallery\core\config $gallery_config
	 * @param \phpbbgallery\core\notification\helper $notifications_helper
	 * @param \phpbbgallery\core\url $url
	 * @param \phpbbgallery\core\image\image $image
	 * @param \phpbb\request\request $request
	 * @param string $images_table Gallery image table
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\db\driver\driver_interface $db,
	\phpbb\pagination $pagination, \phpbb\template\template $template, \phpbb\user $user, \phpbbgallery\core\album\display $display,
	\phpbbgallery\core\album\loader $loader, \phpbbgallery\core\auth\auth $auth, \phpbbgallery\core\auth\level $auth_level,  \phpbbgallery\core\config $gallery_config,
	\phpbbgallery\core\notification\helper $notifications_helper, \phpbbgallery\core\url $url, \phpbbgallery\core\image\image $image, \phpbb\request\request $request,
	$images_table)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->db = $db;
		$this->pagination = $pagination;
		$this->template = $template;
		$this->user = $user;
		$this->display = $display;
		$this->loader = $loader;
		$this->auth = $auth;
		$this->auth_level = $auth_level;
		$this->notifications_helper = $notifications_helper;
		$this->url = $url;
		$this->image = $image;
		$this->gallery_config = $gallery_config;
		$this->request = $request;
		$this->table_images = $images_table;
	}

	/**
	 * Album Controller
	 *    Route: gallery/album/{album_id}
	 *
	 * @param int $album_id Root Album ID
	 * @param int $page
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function base($album_id, $page = 0)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));

		try
		{
			$this->loader->load($album_id);
		}
		catch (\Exception $e)
		{
			throw new \phpbb\exception\http_exception(404, 'ALBUM_NOT_EXIST');
		}

		$album_data = $this->loader->get($album_id);

		/*if ($album_data['album_contest'] == 1)
		{
			$phpbb_gallery_contest = new \phpbbgallery\core\contest();
			$album_contest_data = $phpbb_gallery_contest->get_contest($album_id);
			if ($album_contest_data['contest_id'] && $album_contest_data['contest_marked'] && (($album_contest_data['contest_start'] + $album_contest_data['contest_end']) < time()))
			{
				$contest_end_time = $album_contest_data['contest_start'] + $album_contest_data['contest_end'];
				$phpbb_gallery_contest->end($album_id, $album_contest_data['contest_id'], $contest_end_time);

				$album_contest_data['contest_marked'] = phpbb_ext_gallery_core_image::NO_CONTEST;
			}
		}*/
		$this->check_permissions($album_id, $album_data['album_user_id'], $album_data['album_auth_access']);
		$this->auth_level->display($album_id, $album_data['album_status'], $album_data['album_user_id']);

		$this->display->generate_navigation($album_data);
		$this->display->display_albums($album_data, $this->config['load_moderators']);

		$page_title = $album_data['album_name'];
		if ($page > 1)
		{
			$page_title .= ' - ' . $this->user->lang('PAGE_TITLE_NUMBER', $page);
		}

		if ($this->config['load_moderators'])
		{
			$moderators = $this->display->get_moderators($album_id);
			if (!empty($moderators[$album_id]))
			{
				$moderators = $moderators[$album_id];
				$l_moderator = (sizeof($moderators) == 1) ? $this->user->lang('MODERATOR') : $this->user->lang('MODERATORS');
				$this->template->assign_vars(array(
					'L_MODERATORS'	=> $l_moderator,
					'MODERATORS'	=> implode($this->user->lang('COMMA_SEPARATOR'), $moderators),
				));
			}
		}

		if ($this->auth->acl_check('m_', $album_id, $album_data['album_user_id']))
		{
			$this->template->assign_var('U_MCP', $this->helper->route(
				'phpbbgallery_core_moderate_album',
				array('album_id' => $album_id)
			));
		}

		if ((!$album_data['album_user_id'] || $album_data['album_user_id'] == $this->user->data['user_id'])
			&& ($this->user->data['user_id'] == ANONYMOUS || $this->auth->acl_check('i_upload', $album_id, $album_data['album_user_id'])))
		{
			$this->template->assign_var('U_UPLOAD_IMAGE', $this->helper->route(
				'phpbbgallery_core_album_upload',
				array('album_id' => $album_id)
			));
		}

		$this->template->assign_vars(array(
			'S_IS_POSTABLE'		=> $album_data['album_type'] != \phpbbgallery\core\block::TYPE_CAT,
			'S_IS_LOCKED'		=> $album_data['album_status'] == \phpbbgallery\core\block::ALBUM_LOCKED,

			'U_RETURN_LINK'		=> $this->helper->route('phpbbgallery_core_index'),
			'L_RETURN_LINK'		=> $this->user->lang('RETURN_TO_GALLERY'),
			'S_ALBUM_ACTION'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id)),
			'S_IS_WATCHED'		=> $this->notifications_helper->get_watched_album($album_id) ? true : false,
			'U_WATCH_TOGLE'		=> $this->helper->route('phpbbgallery_core_album_watch', array('album_id' => $album_id)),
		));

		if ($album_data['album_type'] != \phpbbgallery\core\block::TYPE_CAT
			&& $album_data['album_images_real'] > 0)
		{
			$this->display_images($album_id, $album_data, ($page - 1) * $this->config['phpbb_gallery_items_per_page'], $this->config['phpbb_gallery_items_per_page']);
		}

//		phpbb_ext_gallery_core_misc::markread('album', $album_id);

		return $this->helper->render('gallery/album_body.html', $page_title);
	}

	protected function display_images($album_id, $album_data, $start, $limit)
	{
		$sort_days	= $this->request->variable('st', 0);
		$sort_key	= $this->request->variable('sk', ($album_data['album_sort_key']) ? $album_data['album_sort_key'] : $this->config['phpbb_gallery_default_sort_key']);
		$sort_dir	= $this->request->variable('sd', ($album_data['album_sort_dir']) ? $album_data['album_sort_dir'] : $this->config['phpbb_gallery_default_sort_dir']);

		$image_status_check = ' AND image_status <> ' . \phpbbgallery\core\block::STATUS_UNAPPROVED;
		$image_counter = $album_data['album_images'];
		if ($this->auth->acl_check('m_status', $album_id, $album_data['album_user_id']))
		{
			$image_status_check = '';
			$image_counter = $album_data['album_images_real'];
		}

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

		$this->template->assign_block_vars('imageblock', array(
			'BLOCK_NAME'	=> $album_data['album_name'],
		));

		$images = array();
		$sql = 'SELECT *
			FROM ' . $this->table_images . '
			WHERE image_album_id = ' . (int) $album_id . "
				$image_status_check
				AND image_status <> " . \phpbbgallery\core\block::STATUS_ORPHAN . "
			ORDER BY $sql_sort_order" . $sql_help_sort;
		$result = $this->db->sql_query_limit($sql, $limit, $start);

		// Now let's get display options
		$show_ip = $show_ratings = $show_username = $show_views = $show_time = $show_imagename = $show_comments = $show_album = false;
		$show_options = $this->gallery_config->get('album_display');
		if ($show_options >= 128)
		{
			$show_ip = true;
			$show_options = $show_options - 128;
		}
		if ($show_options >= 64)
		{
			$show_ratings = true;
			$show_options = $show_options - 64;
		}
		if ($show_options >= 32)
		{
			$show_username = true;
			$show_options = $show_options - 32;
		}
		if ($show_options >= 16)
		{
			$show_views = true;
			$show_options = $show_options - 16;
		}
		if ($show_options >= 8)
		{
			$show_time = true;
			$show_options = $show_options - 8;
		}
		if ($show_options >= 4)
		{
			$show_imagename = true;
			$show_options = $show_options - 4;
		}
		if ($show_options >= 2)
		{
			$show_comments = true;
			$show_options = $show_options - 2;
		}
		if ($show_options == 1)
		{
			$show_album = true;
		}

		while ($row = $this->db->sql_fetchrow($result))
		{
			// Assign the image to the template-block
			$image_data = array_merge($album_data, $row);
			$album_status = $image_data['album_status'];
			$album_user_id = $image_data['album_user_id'];

			//@todo: $rating = new phpbb_gallery_image_rating($image_data['image_id'], $image_data, $image_data);
			$image_data['rating'] = '0';//@todo: $rating->get_image_rating(false, false);
			//@todo: unset($rating);

			$s_user_allowed = (($image_data['image_user_id'] == $this->user->data['user_id']) && ($album_status != \phpbbgallery\core\block::ALBUM_LOCKED));

			switch ($this->gallery_config->get('link_thumbnail'))
			{
				case 'image_page':
					$action = $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id']));
				break;
				case 'image':
					$action = $this->helper->route('phpbbgallery_core_image_file_source', array('image_id' => $row['image_id']));
				break;
				default:
					$action = false;
				break;
			}
			switch ($this->gallery_config->get('link_image_name'))
			{
				case 'image_page':
					$action_image = $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id']));
				break;
				case 'image':
					$action_image = $this->helper->route('phpbbgallery_core_image_file_source', array('image_id' => $row['image_id']));
				break;
				default:
					$action_image = false;
				break;
			}
			$s_allowed_delete = (($this->auth->acl_check('i_delete', $image_data['image_album_id'], $album_user_id) && $s_user_allowed) || $this->auth->acl_check('m_delete', $image_data['image_album_id'], $album_user_id));
			$s_allowed_edit = (($this->auth->acl_check('i_edit', $image_data['image_album_id'], $album_user_id) && $s_user_allowed) || $this->auth->acl_check('m_edit', $image_data['image_album_id'], $album_user_id));
			$s_quick_mod = ($s_allowed_delete || $s_allowed_edit || $this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id) || $this->auth->acl_check('m_move', $image_data['image_album_id'], $album_user_id));

			$s_username_hidden = $image_data['image_contest'] && !$this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id) && ($this->user->data['user_id'] != $image_data['image_user_id'] || $image_data['image_user_id'] == ANONYMOUS);
			$this->template->assign_block_vars('imageblock.image', array(
				'IMAGE_ID'		=> $image_data['image_id'],
				'U_IMAGE'		=> $action_image,
				'UC_IMAGE_NAME'	=> $show_imagename ? $image_data['image_name'] : false,
				'U_ALBUM'	=> $show_album ? $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_data['album_id'])) : false,
				'ALBUM_NAME'	=> $show_album ? $album_data['album_name'] : false,
				'IMAGE_VIEWS'	=> $show_views ? $image_data['image_view_count'] : -1,
				//'UC_THUMBNAIL'	=> 'self::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
				'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $image_data['image_id'])),
				'UC_THUMBNAIL_ACTION'	=> $action,
				'S_UNAPPROVED'	=> ($this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id) && ($image_data['image_status'] == \phpbbgallery\core\block::STATUS_UNAPPROVED)) ? true : false,
				'S_LOCKED'		=> ($image_data['image_status'] == \phpbbgallery\core\block::STATUS_LOCKED) ? true : false,
				'S_REPORTED'	=> ($this->auth->acl_check('m_report', $image_data['image_album_id'], $album_user_id) && $image_data['image_reported']) ? true : false,
				'POSTER'		=> ($show_username) ? (($s_username_hidden) ? $this->user->lang['CONTEST_USERNAME'] : get_username_string('full', $image_data['image_user_id'], $image_data['image_username'], $image_data['image_user_colour'])) : false,
				'TIME'			=> $show_time ? $this->user->format_date($image_data['image_time']) : false,

				'S_RATINGS'		=> ($this->config['phpbb_gallery_allow_rates'] == 1 && $show_ratings) ? ($image_data['image_rates'] > 0 ? $image_data['image_rate_avg'] / 100 : $this->user->lang('NOT_RATED')) : false,
				'U_RATINGS'		=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_data['image_id'])) . '#rating',
				'L_COMMENTS'	=> ($image_data['image_comments'] == 1) ? $this->user->lang['COMMENT'] : $this->user->lang['COMMENTS'],
				'S_COMMENTS'	=> ($this->config['phpbb_gallery_allow_comments'] && $this->auth->acl_check('c_read', $image_data['image_album_id'], $album_user_id) && $show_comments) ? (($image_data['image_comments']) ? $image_data['image_comments'] : $this->user->lang['NO_COMMENTS']) : '',
				'U_COMMENTS'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_data['image_id'])) . '#comments',

				'U_USER_IP'		=> $show_ip && $this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id) ? $image_data['image_user_ip'] : false,
				'S_IMAGE_REPORTED'		=> $image_data['image_reported'],
				'U_IMAGE_REPORTED'		=> '',//($image_data['image_reported']) ? $phpbb_ext_gallery->url->append_sid('mcp', "mode=report_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_reported']) : '',
				'S_STATUS_APPROVED'		=> ($image_data['image_status'] == \phpbbgallery\core\block::STATUS_APPROVED),
				'S_STATUS_UNAPPROVED'	=> ($this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id) && $image_data['image_status'] == \phpbbgallery\core\block::STATUS_UNAPPROVED) ? true : false,
				'S_STATUS_UNAPPROVED_ACTION'	=> ($this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id) && $image_data['image_status'] == \phpbbgallery\core\block::STATUS_UNAPPROVED) ? $this->helper->route('phpbbgallery_core_moderate_image_approve', array('image_id' => $image_data['image_id'])) : '',
				'S_STATUS_LOCKED'		=> ($image_data['image_status'] == \phpbbgallery\core\block::STATUS_LOCKED),

				'U_REPORT'	=> ($this->auth->acl_check('m_report', $image_data['image_album_id'], $album_user_id) && $image_data['image_reported']) ? '123'/*$this->url->append_sid('mcp', "mode=report_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_reported'])*/ : '',
				'U_STATUS'	=> '',//($this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id)) ? $phpbb_ext_gallery->url->append_sid('mcp', "mode=queue_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_id']) : '',
				'L_STATUS'	=> ($image_data['image_status'] == \phpbbgallery\core\block::STATUS_UNAPPROVED) ? $this->user->lang['APPROVE_IMAGE'] : (($image_data['image_status'] == \phpbbgallery\core\block::STATUS_APPROVED) ? $this->user->lang['CHANGE_IMAGE_STATUS'] : $this->user->lang['UNLOCK_IMAGE']),
			));
		}
		$this->db->sql_freeresult($result);

		$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_album',
					'phpbbgallery_core_album_page',
				),
				'params' => array(
					'album_id' => $album_id,
					'sk' => $sort_key,
					'sd' => $sort_dir,
					'st' => $sort_days
				),
			), 'pagination', 'page', $image_counter, $limit, $start);

		$this->template->assign_vars(array(
			'TOTAL_IMAGES'				=> $this->user->lang('VIEW_ALBUM_IMAGES', $image_counter),
			'S_SELECT_SORT_DIR'			=> $s_sort_dir,
			'S_SELECT_SORT_KEY'			=> $s_sort_key,
		));
	}

	/**
	 * @param $album_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function watch($album_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));

		$album_data = $this->loader->get($album_id);

		$this->check_permissions($album_id, $album_data['album_user_id'], $album_data['album_auth_access']);
		if (confirm_box(true))
		{
			$back_link = $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id));
			if ($this->notifications_helper->get_watched_album($album_id) == 1)
			{
				$this->notifications_helper->remove_albums($album_id);
				$this->template->assign_vars(array(
					'INFORMATION'	=> $this->user->lang['UNWATCH_ALBUM']
				));
				$this->url->meta_refresh(3, $back_link);
				return $this->helper->render('gallery/message.html', $this->user->lang('GALLERY'));
			}
			else
			{
				$this->notifications_helper->add_albums($album_id);
				$this->template->assign_vars(array(
					'INFORMATION'	=> $this->user->lang['WATCH_ALBUM']
				));
				$this->url->meta_refresh(3, $back_link);
				return $this->helper->render('gallery/message.html', $this->user->lang('GALLERY'));
			}
		}
		else
		{
			if ($this->notifications_helper->get_watched_album($album_id) == 1)
			{
				$lang = $this->user->lang['UNWATCH_ALBUM'];
			}
			else
			{
				$lang = $this->user->lang['WATCH_ALBUM'];
			}
			$s_hidden_fields = '';
			confirm_box(false, $lang, $s_hidden_fields);
		}
		//return $this->helper->render('gallery/moderate_approve.html', $this->user->lang('GALLERY'));
	}

	/**
	 * @param    int $album_id
	 * @param $owner_id
	 * @param $album_auth_level
	 * @internal param array $album_data
	 */
	protected function check_permissions($album_id, $owner_id, $album_auth_level)
	{
		$this->auth->load_user_premissions($this->user->data['user_id']);
		$zebra_array = $this->auth->get_user_zebra($this->user->data['user_id']);
		if (!$this->auth->acl_check('i_view', $album_id, $owner_id) || $this->auth->get_zebra_state($zebra_array, (int) $owner_id, (int) $album_id) < (int) $album_auth_level)
		{
			if ($this->user->data['is_bot'])
			{
				// Redirect bots back to the index
				redirect($this->helper->route('phpbbgallery_core_index'));
			}

			// Display login box for guests and an error for users
			if (!$this->user->data['is_registered'])
			{
				login_box();
			}
			else
			{
				//return $this->error('NOT_AUTHORISED', 403);
				trigger_error($this->user->lang('NOT_AUTHORISED'));
			}
		}
	}
}
