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
	\phpbb\user_loader $user_loader, \phpbbgallery\core\album\album $album, \phpbbgallery\core\auth\auth $gallery_auth, $images_table, $reports_table)
	{
		$this->db = $db;
		$this->template = $template;
		$this->helper = $helper;
		$this->user = $user;
		$this->user_loader = $user_loader;
		$this->album = $album;
		$this->gallery_auth = $gallery_auth;
		$this->images_table = $images_table;
		$this->reports_table = $reports_table;
	}

	/**
	* Helper function building queues
	* @param	(string)	type	What type of queue are we building (short or full)
	* @param	(string)	target	For what are we building queue
	* @param	(int)		$page	This queue builder should return objects for MCP queues, so page?
	* @param	(int)		$count	We need how many elements per page
	*/
	public function build_queue($type, $target, $page = 0, $count = 0)
	{
		if (!$type || !$target)
		{
			return;
		}

		// Let's get albums that user can moderate
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		switch($target)
		{
			case 'report_image_open':
				// Get albums we can approve in
				$mod_array = $this->gallery_auth->acl_album_ids('m_report');

				// If no albums we can approve - quit building queue
				if (empty($mod_array))
				{
					return;
				}
				// Let's get reports and images
				$sql_array = array(
					'SELECT'	=> 'i.image_id, i.image_name, i.image_user_id, i.image_username, i.image_user_colour, i.image_time, i.image_album_id, r.report_id, r.reporter_id, r.report_time',
					'FROM'	=> array(
						$this->images_table => 'i',
						$this->reports_table	=> 'r',
					),
					'WHERE'	=> 'i.image_id = r.report_image_id and i.image_reported and r.report_status = 1 and ' . $this->db->sql_in_set('i.image_album_id', $mod_array),
					'ORBER_BY'	=> 'r.report_id DESC'
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);

				if ($type == 'short')
				{
					//We build last 5 for short
					$result = $this->db->sql_query_limit($sql, 5, 0);
				}

				$reported_images = $users_array = array();

				// Build few arrays
				while($row = $this->db->sql_fetchrow($result))
				{
					$reported_images[] = array(
						'image_id'	=> $row['image_id'],
						'image_name'	=> $row['image_name'],
						'image_username'	=> $row['image_username'],
						'image_user_id'	=> $row['image_user_id'],
						'image_user_colour'	=> $row['image_user_colour'],
						'image_time'	=> $row['image_time'],
						'image_album_id'	=> $row['image_album_id'],
						'report_id'	=> $row['report_id'],
						'reporter_id'	=> $row['reporter_id'],
						'report_time'	=> $row['report_time'],
					);
					$users_array[$row['reporter_id']] = array('');
					$users_array[$row['image_user_id']] = array('');
				}
				$this->db->sql_freeresult($result);

				if (empty($users_array))
				{
					return;
				}

				// Load users
				$this->user_loader->load_users(array_keys($users_array));

				$reported_images_count = 0;
				foreach($reported_images as $VAR)
				{
					$album = $this->album->get_info($VAR['image_album_id']);
					$this->template->assign_block_vars('report_image_open', array(
						'U_IMAGE_ID'	=> $VAR['image_id'],
						'U_IMAGE'	=> $this->helper->route('phpbbgallery_image_file_mini', array('image_id' => $VAR['image_id'])),
						'U_IMAGE_URL'	=> $this->helper->route('phpbbgallery_image', array('image_id' => $VAR['image_id'])),
						'U_IMAGE_NAME'	=> $VAR['image_name'],
						'IMAGE_AUTHOR'	=> $this->user_loader->get_username($VAR['image_user_id'], 'full'),
						'IMAGE_TIME'	=> $this->user->format_date($VAR['image_time']),
						'IMAGE_ALBUM'	=> $album['album_name'],
						'IMAGE_ALBUM_URL'	=> $this->helper->route('phpbbgallery_album', array('album_id' => $VAR['image_album_id'])),
						//'REPORT_URL'	=> $this->helper->route('phpbbgallery_moderate_report', array('image_id' => $row['r.report_id'])),
						'REPORT_AUTHOR'	=> $this->user_loader->get_username($VAR['reporter_id'], 'full'),
						'REPORT_TIME'	=> $this->user->format_date($VAR['report_time']),
					));
					unset($album);
					$reported_images_count ++;
				}
				if ($reported_images_count > 0)
				{
					$this->template->assign_vars(array(
						'TOTAL_IMAGES_REPORTED' => $reported_images_count,
					));
				}
			break;

			case 'image_waiting':
				// Get albums we can approve in
				$mod_array = $this->gallery_auth->acl_album_ids('m_status');

				// If no albums we can approve - quit building queue
				if (empty($mod_array))
				{
					return;
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
				if ($type == 'short')
				{
					//We build last 5 for short
					$result = $this->db->sql_query_limit($sql, 5, 0);
				}

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
					$album = $this->album->get_info($VAR['image_album_id']);
					$this->template->assign_block_vars('unaproved', array(
						'U_IMAGE_ID'	=> $VAR['image_id'],
						'U_IMAGE'	=> $this->helper->route('phpbbgallery_image_file_mini', array('image_id' => $VAR['image_id'])),
						'U_IMAGE_URL'	=> $this->helper->route('phpbbgallery_image', array('image_id' => $VAR['image_id'])),
						'U_IMAGE_MODERATE_URL'	=> $this->helper->route('phpbbgallery_moderate_image', array('image_id'	=> $VAR['image_id'])),
						'U_IMAGE_NAME'	=> $VAR['image_name'],
						'IMAGE_AUTHOR'	=> $this->user_loader->get_username($VAR['image_author'], 'full'),
						'IMAGE_TIME'	=> $this->user->format_date($VAR['image_time']),
						'IMAGE_ALBUM'	=> $album['album_name'],
						'IMAGE_ALBUM_URL'	=> $this->helper->route('phpbbgallery_album', array('album_id' => $VAR['image_album_id'])),
						'IMAGE_ALBUM_ID'	=> $VAR['image_album_id'],
					));
					unset($album);
					$waiting_images ++;
				}
				$this->template->assign_vars(array(
					'TOTAL_IMAGES_WAITING' => $this->user->lang('WAITING_UNAPPROVED_IMAGE', (int) $count),
					'S_HAS_UNAPPROVED_IMAGES'=> ($count != 0),
					'S_GALLERY_APPROVE_ACTION'	=> $this->helper->route('phpbbgallery_moderate_queue_approve'),
				));
			break;
		}
	}
}
