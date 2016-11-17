<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/


namespace phpbbgallery\core;

class report
{
	const UNREPORTED = 0;
	const OPEN = 1;
	const LOCKED = 2;

	public function __construct(\phpbbgallery\core\log $gallery_log, \phpbbgallery\core\auth\auth $gallery_auth, \phpbb\user $user, \phpbb\db\driver\driver_interface $db,
	\phpbb\user_loader $user_loader, \phpbbgallery\core\album\album $album, \phpbb\template\template $template, \phpbb\controller\helper $helper,
	\phpbbgallery\core\config $gallery_config, \phpbb\pagination $pagination, \phpbbgallery\core\notification\helper $notification_helper,
	$images_table, $reports_table)
	{
		$this->gallery_log = $gallery_log;
		$this->gallery_auth = $gallery_auth;
		$this->user = $user;
		$this->db = $db;
		$this->user_loader = $user_loader;
		$this->album = $album;
		$this->template = $template;
		$this->helper = $helper;
		$this->gallery_config = $gallery_config;
		$this->pagination = $pagination;
		$this->notification_helper = $notification_helper;
		$this->images_table = $images_table;
		$this->reports_table = $reports_table;
	}

	/**
	 * Report an image
	 *
	 * @param $data
	 */
	public function add($data)
	{
		if (!isset($data['report_album_id']) || !isset($data['report_image_id']) || !isset($data['report_note']))
		{
			return;
		}
		$data = $data + array(
			'reporter_id'				=> $this->user->data['user_id'],
			'report_time'				=> time(),
			'report_status'				=> self::OPEN,
		);
		$sql = 'INSERT INTO ' . $this->reports_table . ' ' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);

		$report_id = (int) $this->db->sql_nextid();

		$sql = 'UPDATE ' . $this->images_table . ' 
			SET image_reported = ' . $report_id . '
			WHERE image_id = ' . (int) $data['report_image_id'];
		$this->db->sql_query($sql);

		$this->gallery_log->add_log('moderator', 'reportopen', $data['report_album_id'], $data['report_image_id'], array('LOG_GALLERY_REPORT_OPENED', $data['report_note']));
		$data = array(
			'report_id'	=> $report_id,
			'reporter_id'	=> $this->user->data['user_id'],
			'reported_image_id'	=> $data['report_image_id'],
			'reported_album_id'	=> $data['report_album_id']
		);
		if (!isset($data['report_album_id']))
		{
			$this->notification_helper->notify('new_report', $data);
		}
	}

	/**
	 * Close report
	 * @param    array $report_ids array of report_ids to closedir
	 * @param bool|int $user_id User Id, if not set - use current user idate
	 */
	public function close_reports_by_image($report_ids, $user_id = false)
	{
		$sql_ary = array(
			'report_manager'		=> (int) (($user_id) ? $user_id : $this->user->data['user_id']),
			'report_status'			=> 0,
		);
		$sql = 'UPDATE ' . $this->reports_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE ' . $this->db->sql_in_set('report_image_id', $report_ids);
		$this->db->sql_query($sql);
		// We will have to request some images so we can log closing reports
		$sql = 'SELECT * FROM ' . $this->images_table . ' WHERE image_reported <> 0 and ' . $this->db->sql_in_set('image_id', $report_ids);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->gallery_log->add_log('moderator', 'reportclosed', (int) $row['image_album_id'], (int) $row['image_id'], array('LOG_GALLERY_REPORT_CLOSED', 'Closed'));
		}
		$this->db->sql_freeresult($result);
		$sql = 'UPDATE ' . $this->images_table . ' SET image_reported = 0 WHERE ' . $this->db->sql_in_set('image_id', $report_ids);
		$this->db->sql_query($sql);
	}

	/**
	 * Move an image from one album to another
	 *
	 * @param    mixed $image_ids Array or integer with image_id.
	 * @param $move_to
	 */
	public function move_images($image_ids, $move_to)
	{
		$image_ids = self::cast_mixed_int2array($image_ids);

		$sql = 'UPDATE ' . $this->reports_table . '
			SET report_album_id = ' . (int) $move_to . '
			WHERE ' . $this->db->sql_in_set('report_image_id', $image_ids);
		$this->db->sql_query($sql);
	}

	/**
	 * Move the content from one album to another
	 *
	 * @param $move_from
	 * @param $move_to
	 * @internal param mixed $image_ids Array or integer with image_id.
	 */
	public function move_album_content($move_from, $move_to)
	{
		$sql = 'UPDATE ' . $this->reports_table . '
			SET report_album_id = ' . (int) $move_to . '
			WHERE report_album_id = ' . (int) $move_from;
		$this->db->sql_query($sql);
	}

	/**
	* Delete reports for given report_ids
	*
	* @param	mixed	$report_ids		Array or integer with report_id.
	*/
	public function delete($report_ids)
	{
		$report_ids = self::cast_mixed_int2array($report_ids);

		$sql = 'DELETE FROM ' . $this->reports_table . '
			WHERE ' . $this->db->sql_in_set('report_id', $report_ids);
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->images_table . '
			SET image_reported = ' . self::UNREPORTED . '
			WHERE ' . $this->db->sql_in_set('image_reported', $report_ids);
		$this->db->sql_query($sql);

		// Let's delete notifications
		$this->notification_helper->delete_notifications('report', $report_ids);
	}


	/**
	* Delete reports for given image_ids
	*
	* @param	mixed	$image_ids		Array or integer with image_id.
	*/
	public function delete_images($image_ids)
	{
		$image_ids = self::cast_mixed_int2array($image_ids);

		// Let's build array for report notifications for images we are deleting
		$sql = 'SELECT report_id FROM ' . $this->reports_table . '
			WHERE ' . $this->db->sql_in_set('report_image_id', $image_ids);
		$result = $this->db->sql_query($sql);
		$reports = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$reports[] = $row['report_id'];
		}
		$this->db->sql_freeresult($result);
		if (!empty($reports))
		{
			$reports = self::cast_mixed_int2array($reports);
			$this->notification_helper->delete_notifications('report', $reports);
		}

		$sql = 'DELETE FROM ' . $this->reports_table . '
			WHERE ' . $this->db->sql_in_set('report_image_id', $image_ids);
		$this->db->sql_query($sql);
	}


	/**
	* Delete reports for given album_ids
	*
	* @param	mixed	$album_ids		Array or integer with album_id.
	*/
	public function delete_albums($album_ids)
	{
		$album_ids = self::cast_mixed_int2array($album_ids);

		// Let's build array for report notifications for albums we are deleting
		$sql = 'SELECT report_id FROM ' . $this->reports_table . '
			WHERE ' . $this->db->sql_in_set('report_album_id', $album_ids);
		$result = $this->db->sql_query($sql);
		$reports = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$reports[] = $row['report_id'];
		}
		$this->db->sql_freeresult($result);
		if (!empty($reports))
		{
			$reports = self::cast_mixed_int2array($repors);
			$this->notification_helper->delete_notifications('report', $reports);
		}

		$sql = 'DELETE FROM ' . $this->reports_table . '
			WHERE ' . $this->db->sql_in_set('report_album_id', $album_ids);
		$this->db->sql_query($sql);
	}

	/**
	 * Helper function building queues
	 * @param    (string)    type    What type of queue are we building (short or full)
	 * @param int $page
	 * @param int $per_page
	 * @param int $status
	 */
	public function build_list($album, $page = 1, $per_page = 0, $status = 1)
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
			$mod_array = $this->gallery_auth->acl_album_ids('m_report');
			if (empty($mod_array))
			{
				$mod_array[] = 0;
			}
		}
		else
		{
			$mod_array = array($album);
		}

		$sql_array = array(
			'FROM'	=> array(
				$this->images_table => 'i',
				$this->reports_table	=> 'r',
			),
			'WHERE'	=> 'i.image_id = r.report_image_id and r.report_status = ' . (int) $status . ' and ' . $this->db->sql_in_set('i.image_album_id', $mod_array),
			'ORBER_BY'	=> 'r.report_id DESC'
		);
		// Get Count
		$sql_array['SELECT'] = 'COUNT(r.report_id) as count';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		// Request reports
		$sql_array['SELECT'] = 'i.image_id, i.image_name, i.image_user_id, i.image_username, i.image_user_colour, i.image_time, i.image_album_id, r.report_id, r.reporter_id, r.report_time';
		$page = $page - 1;
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $per_page, $page * $per_page);
		// Build few arrays
		while ($row = $this->db->sql_fetchrow($result))
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
		foreach ($reported_images as $VAR)
		{
			$album_tmp = $this->album->get_info($VAR['image_album_id']);
			$this->template->assign_block_vars('report_image_open', array(
				'U_IMAGE_ID'	=> $VAR['image_id'],
				'U_IMAGE'	=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $VAR['image_id'])),
				'U_IMAGE_URL'	=> $this->helper->route('phpbbgallery_core_image', array('image_id'	=> $VAR['image_id'])),
				'U_IMAGE_NAME'	=> $VAR['image_name'],
				'IMAGE_AUTHOR'	=> $this->user_loader->get_username($VAR['image_user_id'], 'full'),
				'IMAGE_TIME'	=> $this->user->format_date($VAR['image_time']),
				'IMAGE_ALBUM'	=> $album_tmp['album_name'],
				'IMAGE_ALBUM_URL'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $VAR['image_album_id'])),
				'REPORT_URL'	=> $this->helper->route('phpbbgallery_core_moderate_image', array('image_id' => $VAR['image_id'])),
				'REPORT_AUTHOR'	=> $this->user_loader->get_username($VAR['reporter_id'], 'full'),
				'REPORT_TIME'	=> $this->user->format_date($VAR['report_time']),
			));
			unset($album_tmp);
			$reported_images_count ++;
		}
		$this->template->assign_vars(array(
			'TOTAL_IMAGES_REPORTED' => $status == 1 ? $this->user->lang('WAITING_REPORTED_IMAGE', (int) $count) : $this->user->lang('WAITING_REPORTED_DONE', (int) $count),
			'S_GALLERY_REPORT_ACTION'	=> $status == 1 ? ($album > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_album', array('album_id' => $album)) : $this->helper->route('phpbbgallery_core_moderate_reports')) : false,
		));
		if ($album === 0)
		{
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					$status == 1 ? 'phpbbgallery_core_moderate_reports' : 'phpbbgallery_core_moderate_reports_closed',
					$status == 1 ? 'phpbbgallery_core_moderate_reports_page' : 'phpbbgallery_core_moderate_reports_closed_page',
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
					$status == 1 ? 'phpbbgallery_core_moderate_reports_album' : 'phpbbgallery_core_moderate_reports_closed_album',
					$status == 1 ? 'phpbbgallery_core_moderate_reports_album_page' : 'phpbbgallery_core_moderate_reports_closed_album_page',
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
	 * Get report data by image id
	 *
	 * @param    (int)    $image_id    Image id for which we will get info about
	 * return    array    $report_data    array with all report info\
	 * @return array|void
	 */
	public function get_data_by_image($image_id)
	{
		if (empty($image_id))
		{
			return;
		}

		$sql = 'SELECT * FROM ' . $this->reports_table . ' WHERE report_image_id = ' . $image_id;
		$result = $this->db->sql_query($sql);
		$report_data = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$report_data[$row['report_id']] = array(
				'report_id'			=> $row['report_id'],
				'report_album_id'	=> $row['report_album_id'],
				'reporter_id'		=> $row['reporter_id'],
				'report_manager'	=> $row['report_manager'],
				'report_note'		=> $row['report_note'],
				'report_time'		=> $row['report_time'],
				'report_status'		=> $row['report_status'],
			);
		}
		$this->db->sql_freeresult($result);

		return $report_data;
	}
	static public function cast_mixed_int2array($ids)
	{
		if (is_array($ids))
		{
			return array_map('intval', $ids);
		}
		else
		{
			return array((int) $ids);
		}
	}
}
