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

if (!defined('IN_PHPBB'))
{
	exit;
}

class phpbb_ext_gallery_core_misc
{
	static public function display_captcha($mode)
	{
		static $gallery_display_captcha;

		if (isset($gallery_display_captcha[$mode]))
		{
			return $gallery_display_captcha[$mode];
		}

		global $config, $user;

		$gallery_display_captcha[$mode] = ($user->data['user_id'] == ANONYMOUS) && phpbb_gallery_config::get('captcha_' . $mode) && (version_compare($config['version'], '3.0.5', '>'));

		return $gallery_display_captcha[$mode];
	}

	static public function not_authorised($backlink, $loginlink = '', $login_explain = '')
	{
		global $user;

		if (!$user->data['is_registered'] && $loginlink)
		{
			if ($login_explain && isset($user->lang[$login_explain]))
			{
				$login_explain = $user->lang[$login_explain];
			}
			else
			{
				$login_explain = '';
			}
			login_box($loginlink, $login_explain);
		}
		else
		{
			meta_refresh(3, $backlink);
			trigger_error('NOT_AUTHORISED');
		}
	}

	/**
	* Marks a album as read
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: markread
	*/
	static public function markread($mode, $album_id = false)
	{
		global $db, $user;

		// Sorry, no guest support!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			return;
		}

		if ($mode == 'all')
		{
			if ($album_id === false || !sizeof($album_id))
			{
				// Mark all albums read (index page)
				$sql = 'DELETE FROM ' . GALLERY_ATRACK_TABLE . '
					WHERE user_id = ' . $user->data['user_id'];
				$db->sql_query($sql);

				phpbb_gallery::$user->update_data(array(
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
				FROM ' . GALLERY_ATRACK_TABLE . "
				WHERE user_id = {$user->data['user_id']}
					AND " . $db->sql_in_set('album_id', $album_id);
			$result = $db->sql_query($sql);

			$sql_update = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$sql_update[] = $row['album_id'];
			}
			$db->sql_freeresult($result);

			if (sizeof($sql_update))
			{
				$sql = 'UPDATE ' . GALLERY_ATRACK_TABLE . '
					SET mark_time = ' . time() . "
					WHERE user_id = {$user->data['user_id']}
						AND " . $db->sql_in_set('album_id', $sql_update);
				$db->sql_query($sql);
			}

			if ($sql_insert = array_diff($album_id, $sql_update))
			{
				$sql_ary = array();
				foreach ($sql_insert as $a_id)
				{
					$sql_ary[] = array(
						'user_id'	=> (int) $user->data['user_id'],
						'album_id'	=> (int) $a_id,
						'mark_time'	=> time()
					);
				}

				$db->sql_multi_insert(GALLERY_ATRACK_TABLE, $sql_ary);
			}

			return;
		}
		else if ($mode == 'album')
		{
			if ($album_id === false)
			{
				return;
			}

			$sql = 'UPDATE ' . GALLERY_ATRACK_TABLE . '
				SET mark_time = ' . time() . "
				WHERE user_id = {$user->data['user_id']}
					AND album_id = $album_id";
			$db->sql_query($sql);

			if (!$db->sql_affectedrows())
			{
				$db->sql_return_on_error(true);

				$sql_ary = array(
					'user_id'		=> (int) $user->data['user_id'],
					'album_id'		=> (int) $album_id,
					'mark_time'		=> time(),
				);

				$db->sql_query('INSERT INTO ' . GALLERY_ATRACK_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

				$db->sql_return_on_error(false);
			}

			return;
		}
	}
}
