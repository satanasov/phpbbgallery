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
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\controller\helper $helper, \phpbb\user $user, \phpbb\user_loader $user_loader, \phpbbgallery\core\album\album $album, $images_table, $reports_table)
	{
		$this->db = $db;
		$this->template = $template;
		$this->helper = $helper;
		$this->user = $user;
		$this->user_loader = $user_loader;
		$this->album = $album;
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

		switch($target)
		{
			case 'report_image_open':
				// Let's get reports and images
				$sql_array = array(
					'SELECT'	=> 'i.image_id, i.image_name, i.image_user_id, i.image_username, i.image_user_colour, i.image_time, i.image_album_id, r.report_id, r.reporter_id, r.report_time',
					'FROM'	=> array(
						$this->images_table => 'i',
						$this->reports_table	=> 'r',
					),
					'WHERE'	=> 'i.image_id = r.report_image_id and i.image_reported and r.report_status = 1',
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
		}
	}
}