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
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\controller\helper $helper, \phpbb\user $user,
	\phpbb\user_loader $user_loader, \phpbbgallery\core\album\album $album, \phpbbgallery\core\auth\auth $gallery_auth, \phpbb\pagination $pagination, \phpbbgallery\core\config $gallery_config,
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
		$this->gallery_config = $gallery_config;
		$this->images_table = $images_table;
	}

	/**
	* Helper function building queues
	* @param	(string)	type	What type of queue are we building (short or full)
	* @param	(string)	target	For what are we building queue
	* @param	(int)		$page	This queue builder should return objects for MCP queues, so page?
	* @param	(int)		$count	We need how many elements per page
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
		}
		else
		{
			$mod_array = array($album);
		}
		// Let's get count of unaproved
		$sql = 'SELECT COUNT(image_id) as count 
			FROM ' . $this->images_table . ' 
			WHERE image_status = ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ' and ' . $this->db->sql_in_set('image_album_id', $mod_array) . '
			ORDER BY image_id DESC';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		// If user has no albums to have e return him
		$sql = 'SELECT * 
			FROM ' . $this->images_table . ' 
			WHERE image_status = ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ' and ' . $this->db->sql_in_set('image_album_id', $mod_array) . '
			ORDER BY image_id DESC';
		$page = $page - 1;
		$result = $this->db->sql_query_limit($sql, $per_page, $page * $per_page);

		$waiting_images = $users_array = array();
		while($row = $this->db->sql_fetchrow($result))
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
				'U_IMAGE'	=> $this->helper->route('phpbbgallery_image_file_mini', array('image_id' => $VAR['image_id'])),
				'U_IMAGE_URL'	=> $this->helper->route('phpbbgallery_image', array('image_id' => $VAR['image_id'])),
				'U_IMAGE_MODERATE_URL'	=> $this->helper->route('phpbbgallery_moderate_image', array('image_id'	=> $VAR['image_id'])),
				'U_IMAGE_NAME'	=> $VAR['image_name'],
				'IMAGE_AUTHOR'	=> $this->user_loader->get_username($VAR['image_author'], 'full'),
				'IMAGE_TIME'	=> $this->user->format_date($VAR['image_time']),
				'IMAGE_ALBUM'	=> $album_tmp['album_name'],
				'IMAGE_ALBUM_URL'	=> $this->helper->route('phpbbgallery_album', array('album_id' => $VAR['image_album_id'])),
				'IMAGE_ALBUM_ID'	=> $VAR['image_album_id'],
			));
			unset($album_tmp);
			$waiting_images ++;
		}
		$this->template->assign_vars(array(
			'TOTAL_IMAGES_WAITING' => $this->user->lang('WAITING_UNAPPROVED_IMAGE', (int) $count),
			'S_GALLERY_APPROVE_ACTION'	=> $album > 0 ? $this->helper->route('phpbbgallery_moderate_queue_approve_album', array('album_id' => $album)) : $this->helper->route('phpbbgallery_moderate_queue_approve'),
		));
		if ($album === 0)
		{
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_moderate_queue_approve',
					'phpbbgallery_moderate_queue_approve_page',
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
					'phpbbgallery_moderate_queue_approve_album',
					'phpbbgallery_moderate_queue_approve_album_page',
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
}
