<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core;

class moderate
{
	/**
	 * moderate constructor.
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\template\template $template
	 * @param \phpbb\controller\helper $helper
	 * @param \phpbb\user $user
	 * @param \phpbb\user_loader $user_loader
	 * @param album\album $album
	 * @param auth\auth $gallery_auth
	 * @param \phpbb\pagination $pagination
	 * @param comment $comment
	 * @param report $report
	 * @param image\image $image
	 * @param config $gallery_config
	 * @param notification $gallery_notification
	 * @param rating $gallery_rating
	 * @param $images_table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\controller\helper $helper, \phpbb\user $user,
								\phpbb\user_loader $user_loader, \phpbbgallery\core\album\album $album, \phpbbgallery\core\auth\auth $gallery_auth, \phpbb\pagination $pagination,
								\phpbbgallery\core\comment $comment, \phpbbgallery\core\report $report, \phpbbgallery\core\image\image $image,
								\phpbbgallery\core\config $gallery_config, \phpbbgallery\core\notification $gallery_notification, \phpbbgallery\core\rating $gallery_rating,
								$images_table)
	{
		$this->db = $db;
		$this->template = $template;
		$this->helper = $helper;
		$this->user = $user;
		$this->user_loader = $user_loader;
		$this->album = $album;
		$this->gallery_auth = $gallery_auth;
		$this->pagination = $pagination;
		$this->comment = $comment;
		$this->report = $report;
		$this->image = $image;
		$this->gallery_config = $gallery_config;
		$this->gallery_notification = $gallery_notification;
		$this->gallery_rating = $gallery_rating;
		$this->images_table = $images_table;
	}

	/**
	* Helper function building queues
	* @param	int		$album	album we build queue for
	* @param	int		$page	This queue builder should return objects for MCP queues, so page?
	* @param	int		$per_page	We need how many elements per page
	*/
	public function build_list($album, $page = 1, $per_page = 0)
	{
		// So if we are not forcing par page get it from config
		if ($per_page == 0)
		{
			$per_page = $this->gallery_config->get('items_per_page');
		}
		// Let's get albums that user can moderate
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);

		// Get albums we can approve in
		$mod_array = array();
		if ($album === 0)
		{
			$mod_array = $this->gallery_auth->acl_album_ids('m_status');
			if (empty($mod_array))
			{
				$mod_array[] = 0;
			}
		}
		else
		{
			$mod_array = array($album);
		}
		// Let's get count of unapproved
		$sql = 'SELECT COUNT(DISTINCT image_id) as count 
			FROM ' . $this->images_table . ' 
			WHERE image_status = ' . \phpbbgallery\core\block::STATUS_UNAPPROVED . ' AND ' . $this->db->sql_in_set('image_album_id', $mod_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		// If user has no albums to have e return him
		$sql = 'SELECT * 
			FROM ' . $this->images_table . ' 
			WHERE image_status = ' . \phpbbgallery\core\block::STATUS_UNAPPROVED . ' AND ' . $this->db->sql_in_set('image_album_id', $mod_array) . '
			ORDER BY image_id DESC';
		$page = $page - 1;
		$result = $this->db->sql_query_limit($sql, $per_page, $page * $per_page);

		$waiting_images = $users_array = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$waiting_images[] = array(
				'image_id' => $row['image_id'],
				'image_name'	=> $row['image_name'],
				'image_author'	=> (int) $row['image_user_id'],
				'image_time'	=> $row['image_time'],
				'image_album_id'	=> $row['image_album_id'],
			);
			$users_array[$row['image_user_id']] = array('');
		}
		$this->db->sql_freeresult($result);

		if (empty($users_array))
		{
			return;
		}

		// Load users
		$this->user_loader->load_users(array_keys($users_array));

		foreach ($waiting_images as $VAR)
		{
			$album_tmp = $this->album->get_info($VAR['image_album_id']);
			$this->template->assign_block_vars('unaproved', array(
				'U_IMAGE_ID'	=> $VAR['image_id'],
				'U_IMAGE'	=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $VAR['image_id'])),
				'U_IMAGE_URL'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $VAR['image_id'])),
				'U_IMAGE_MODERATE_URL'	=> $this->helper->route('phpbbgallery_core_moderate_image', array('image_id'	=> $VAR['image_id'])),
				'U_IMAGE_NAME'	=> $VAR['image_name'],
				'IMAGE_AUTHOR'	=> $this->user_loader->get_username($VAR['image_author'], 'full'),
				'IMAGE_TIME'	=> $this->user->format_date($VAR['image_time']),
				'IMAGE_ALBUM'	=> $album_tmp['album_name'],
				'IMAGE_ALBUM_URL'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $VAR['image_album_id'])),
				'IMAGE_ALBUM_ID'	=> $VAR['image_album_id'],
			));
			unset($album_tmp);
			$waiting_images ++;
		}
		$this->template->assign_vars(array(
			'TOTAL_IMAGES_WAITING' => $this->user->lang('WAITING_UNAPPROVED_IMAGE', (int) $count),
			'S_GALLERY_APPROVE_ACTION'	=> $album > 0 ? $this->helper->route('phpbbgallery_core_moderate_queue_approve_album', array('album_id' => $album)) : $this->helper->route('phpbbgallery_core_moderate_queue_approve'),
		));
		if ($album === 0)
		{
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_moderate_queue_approve',
					'phpbbgallery_core_moderate_queue_approve_page',
				),
				'params' => array(
				),
			), 'pagination', 'page', $count, $per_page, $page * $per_page);
			$this->template->assign_vars(array(
				'TOTAL_PAGES'				=> $this->user->lang('PAGE_TITLE_NUMBER', $page + 1),
			));
		}
		else
		{
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_moderate_queue_approve_album',
					'phpbbgallery_core_moderate_queue_approve_album_page',
				),
				'params' => array(
					'album_id'	=> $album,
				),
			), 'pagination', 'page', $count, $per_page, $page * $per_page);
			$this->template->assign_vars(array(
				'TOTAL_PAGES'				=> $this->user->lang('PAGE_TITLE_NUMBER', $page + 1),
			));
		}
	}

	/**
	 * Build album overview
	 *
	 * @param    int $album_id
	 * @param    int $page     This queue builder should return objects for MCP queues, so page?
	 * @param    int $per_page We need how many elements per page
	 * @internal param int $album album we build queue for
	 */
	public function album_overview($album_id, $page = 1, $per_page = 0)
	{
		// So if we are not forcing par page get it from config
		if ($per_page == 0)
		{
			$per_page = $this->gallery_config->get('items_per_page');
		}
		// Let's get albums that user can moderate
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);

		// we have security in the controller, so no need to be paranoid ...
		// and we will build queue with only items user can review
		if (!isset($album_id))
		{
			return;
		}
		// Let's see what the user can do?
		$status[] = 1;
		$actions = array();
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album = $this->album->get_info($album_id);
		if ($this->gallery_auth->acl_check('m_status', $album['album_id'], $album['album_user_id']))
		{
			$status[] = 0;
			$status[] = 2;
			$actions['approve']	= 'QUEUES_A_APPROVE';
			$actions['unapprove']	= 'QUEUES_A_UNAPPROVE';
			$actions['lock']	= 'QUEUES_A_LOCK';
		}
		if ($this->gallery_auth->acl_check('m_delete', $album['album_id'], $album['album_user_id']))
		{
			$actions['delete']	= 'QUEUES_A_DELETE';
		}
		if ($this->gallery_auth->acl_check('m_move', $album['album_id'], $album['album_user_id']))
		{
			$actions['move']	= 'QUEUES_A_MOVE';
		}
		if ($this->gallery_auth->acl_check('m_report', $album['album_id'], $album['album_user_id']))
		{
			$actions['report']	= 'REPORT_A_CLOSE';
		}
		$sql = 'SELECT COUNT(DISTINCT image_id) AS count FROM ' . $this->images_table . ' WHERE ' . $this->db->sql_in_set('image_status', $status) . ' AND image_album_id = ' . (int) $album_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		$sql = 'SELECT * FROM ' . $this->images_table . ' WHERE ' . $this->db->sql_in_set('image_status', $status) . ' AND image_album_id = ' . (int) $album_id . ' ORDER BY image_id DESC';

		$result = $this->db->sql_query_limit($sql, $per_page, ($page - 1) * $per_page);
		$users_array = array();
		$images = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$images[] = array(
				'image_id'				=> $row['image_id'],
				'image_filename'		=> $row['image_filename'],
				'image_name'			=> $row['image_name'],
				'image_name_clean'		=> $row['image_name_clean'],
				'image_desc'			=> $row['image_desc'],
				'image_desc_uid'		=> $row['image_desc_uid'],
				'image_desc_bitfield'	=> $row['image_desc_bitfield'],
				'image_user_id'			=> $row['image_user_id'],
				'image_username'		=> $row['image_username'],
				'image_username_clean'	=> $row['image_username_clean'],
				'image_user_colour'		=> $row['image_user_colour'],
				'image_user_ip'			=> $row['image_user_ip'],
				'image_time'			=> $row['image_time'],
				'image_album_id'		=> $row['image_album_id'],
				'image_view_count'		=> $row['image_view_count'],
				'image_status'			=> $row['image_status'],
				'image_filemissing'		=> $row['image_filemissing'],
				'image_rates'			=> $row['image_rates'],
				'image_rate_points'		=> $row['image_rate_points'],
				'image_rate_avg'		=> $row['image_rate_avg'],
				'image_comments'		=> $row['image_comments'],
				'image_last_comment'	=> $row['image_last_comment'],
				'image_allow_comments'	=> $row['image_allow_comments'],
				'image_favorited'		=> $row['image_favorited'],
				'image_reported'		=> $row['image_reported'],
				'filesize_upload'		=> $row['filesize_upload'],
				'filesize_medium'		=> $row['filesize_medium'],
				'filesize_cache'		=> $row['filesize_cache'],
			);
			$users_array[$row['image_user_id']] = array('');
		}
		$this->db->sql_freeresult($result);

		if (empty($users_array))
		{
			return;
		}

		// Load users
		$this->user_loader->load_users(array_keys($users_array));
		foreach ($images as $var)
		{
			$this->template->assign_block_vars('overview', array(
				'U_IMAGE_ID'	=> $var['image_id'],
				'U_IMAGE'	=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $var['image_id'])),
				'U_IMAGE_URL'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $var['image_id'])),
				'U_IMAGE_MODERATE_URL'	=> $this->helper->route('phpbbgallery_core_moderate_image', array('image_id'	=> $var['image_id'])),
				'U_IMAGE_NAME'	=> $var['image_name'],
				'IMAGE_AUTHOR'	=> $this->user_loader->get_username($var['image_user_id'], 'full'),
				'IMAGE_TIME'	=> $this->user->format_date($var['image_time']),
				'IMAGE_ALBUM'	=> $album['album_name'],
				'IMAGE_ALBUM_URL'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $var['image_album_id'])),
				'IMAGE_ALBUM_ID'	=> $var['image_album_id'],
				'U_IS_REPORTED'		=> $this->gallery_auth->acl_check('m_report', $album['album_id'], $album['album_user_id']) && $var['image_reported'] > 0 ? true : false,
				'U_IS_UNAPPROVED'		=> $var['image_status'] == 0 ? true : false,
				'U_IS_LOCKED'		=> $var['image_status'] == 2 ? true : false,
			));
		}

		$this->pagination->generate_template_pagination(array(
			'routes' => array(
				'phpbbgallery_core_moderate_view',
				'phpbbgallery_core_moderate_view_page',
			),
			'params' => array(
				'album_id'	=> $album_id
			),
		), 'pagination', 'page', $count, $per_page, ($page - 1) * $per_page);

		$select = '<select name="select_action">';
		foreach ($actions as $id => $var)
		{
			$select .= '<option value="' . $id . '">' . $this->user->lang($var) . '</option>';
		}
		$select .= '</select>';
		$this->template->assign_vars(array(
			'TOTAL_PAGES'				=> $this->user->lang('PAGE_TITLE_NUMBER', $page),
			'S_GALLERY_MODERATE_OVERVIEW_ACTION'	=> $this->helper->route('phpbbgallery_core_moderate_view', array('album_id' => $album_id)),
			'U_ACTION_SELECT' => $select,
		));
	}
	public function delete_images($images, $files = array())
	{
		// We are going to do some cleanup
		$this->gallery_rating->loader(0);
		$this->gallery_rating->delete_ratings($images);
		$this->comment->delete_images($images);
		$this->gallery_notification->delete_images($images);
		$this->report->delete_images($images);
		$this->image->delete_images($images, $files);
	}
}
