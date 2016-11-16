<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace phpbbgallery\core;

class misc
{
	/**
	 * misc constructor.
	 *
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\user                       $user
	 * @param \phpbb\config\config              $config
	 * @param \phpbbgallery\core\config         $gallery_config
	 * @param \phpbbgallery\core\user			$gallery_user
	 * @param \phpbbgallery\core\url			$url
	 * @param                                   $track_table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\config\config $config,
								   \phpbbgallery\core\config $gallery_config, \phpbbgallery\core\user $gallery_user, \phpbbgallery\core\url $url,
								   $track_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->config = $config;
		$this->gallery_config = $gallery_config;
		$this->gallery_user = $gallery_user;
		$this->url = $url;
		$this->track_table = $track_table;
	}

	/**
	 * Display captcha when needed
	 *
	 * @param $mode
	 * @return mixed
	 */
	public function display_captcha($mode)
	{
		static $gallery_display_captcha;

		if (isset($gallery_display_captcha[$mode]))
		{
			return $gallery_display_captcha[$mode];
		}

		$gallery_display_captcha[$mode] = ($this->user->data['user_id'] == ANONYMOUS) && $this->gallery_config->get('captcha_' . $mode);

		return $gallery_display_captcha[$mode];
	}

	/**
	 * Create not authorized dialog
	 *
	 * @param $backlink
	 * @param string $loginlink
	 * @param string $login_explain
	 */
	public function not_authorised($backlink, $loginlink = '', $login_explain = '')
	{
		if (!$this->user->data['is_registered'] && $loginlink)
		{
			if ($login_explain && isset($this->user->lang[$login_explain]))
			{
				$login_explain = $this->user->lang[$login_explain];
			}
			else
			{
				$login_explain = '';
			}
			login_box($loginlink, $login_explain);
		}
		else
		{
			$this->url->meta_refresh(3, $backlink);
			trigger_error('NOT_AUTHORISED');
		}
	}

	/**
	 * Marks a album as read
	 *
	 * borrowed from phpBB3
	 *
	 * @author  : phpBB Group
	 * @function: markread
	 * @param      $mode
	 * @param bool $album_id
	 */
	public function markread($mode, $album_id = false)
	{
		$this->gallery_user->set_user_id($this->user->data['user_id']);

		// Sorry, no guest support!
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			return;
		}

		if ($mode == 'all')
		{
			if ($album_id === false || !sizeof($album_id))
			{
				// Mark all albums read (index page)
				$sql = 'DELETE FROM ' . $this->track_table . '
					WHERE user_id = ' . (int) $this->user->data['user_id'];
				$this->db->sql_query($sql);

				$this->gallery_user->update_data(array(
						'user_lastmark'		=> time(),
				));
			}

			return;
		}
		else if ($mode == 'albums')
		{
			// Mark album read
			if (!is_array($album_id))
			{
				$album_id = array($album_id);
			}

			$sql = 'SELECT album_id
				FROM ' . $this->track_table . '
				WHERE user_id = ' . (int) $this->user->data['user_id'] .'
					AND ' . $this->db->sql_in_set('album_id', $album_id);
			$result = $this->db->sql_query($sql);

			$sql_update = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$sql_update[] = $row['album_id'];
			}
			$this->db->sql_freeresult($result);

			if (sizeof($sql_update))
			{
				$sql = 'UPDATE ' . $this->track_table . '
					SET mark_time = ' . time() . '
					WHERE user_id = '. (int) $this->user->data['user_id'] .'
						AND ' . $this->db->sql_in_set('album_id', $sql_update);
				$this->db->sql_query($sql);
			}

			if ($sql_insert = array_diff($album_id, $sql_update))
			{
				$sql_ary = array();
				foreach ($sql_insert as $a_id)
				{
					$sql_ary[] = array(
						'user_id'	=> (int) $this->user->data['user_id'],
						'album_id'	=> (int) $a_id,
						'mark_time'	=> time()
					);
				}

				$this->db->sql_multi_insert($this->track_table, $sql_ary);
			}

			return;
		}
		else if ($mode == 'album')
		{
			if ($album_id === false)
			{
				return;
			}

			$sql = 'UPDATE ' . $this->track_table . '
				SET mark_time = ' . time() . '
				WHERE user_id = ' . (int) $this->user->data['user_id'] .'
					AND album_id = ' . (int) $album_id;
			$this->db->sql_query($sql);

			if (!$this->db->sql_affectedrows())
			{
				$this->db->sql_return_on_error(true);

				$sql_ary = array(
					'user_id'		=> (int) $this->user->data['user_id'],
					'album_id'		=> (int) $album_id,
					'mark_time'		=> time(),
				);

				$this->db->sql_query('INSERT INTO ' . $this->track_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));

				$this->db->sql_return_on_error(false);
			}

			return;
		}
	}
}
