<?php
/**
*
* @package Gallery - ACP CleanUp Extension
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\acpcleanup\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path, $phpbb_ext_gallery;


		$phpbb_ext_gallery = new \phpbbgallery\core\core($auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path);
		$phpbb_ext_gallery->init();

		$user->add_lang_ext('phpbbgallery/core', array('gallery_acp', 'gallery'));
		//$user->add_lang_ext('phpbbgallery/acpcleanup', 'cleanup');
		$this->tpl_name = 'gallery_cleanup';
		add_form_key('acp_gallery');

		$this->page_title = $user->lang['ACP_GALLERY_CLEANUP'];
		$this->cleanup();
	}

	function cleanup()
	{
		global $auth, $cache, $db, $template, $user, $phpbb_ext_gallery, $table_prefix, $phpbb_container, $request;

		$delete = (isset($_POST['delete'])) ? true : false;
		$prune = (isset($_POST['prune'])) ? true : false;
		$submit = (isset($_POST['submit'])) ? true : false;

		$missing_sources = $request->variable('source', array(0));
		$missing_entries = $request->variable('entry', array(''), true);
		$missing_authors = $request->variable('author', array(0), true);
		$missing_comments = $request->variable('comment', array(0), true);
		$missing_personals = $request->variable('personal', array(0), true);
		$personals_bad = $request->variable('personal_bad', array(0), true);
		$prune_pattern = $request->variable('prune_pattern', array('' => ''), true);

		$move_to_import = $request->variable('move_to_import', 0);
		$new_author = $request->variable('new_author', '');

		$gallery_album = $phpbb_container->get('phpbbgallery.core.album');
		$core_cleanup = $phpbb_container->get('phpbbgallery.acpcleanup.cleanup');
		$gallery_auth = $phpbb_container->get('phpbbgallery.core.auth');
		$gallery_config = $phpbb_container->get('phpbbgallery.core.config');

		// Lets detect if ACP Import exists (find if directory is with RW access)
		$acp_import_installed = false;
		$acp_import_dir = $phpbb_ext_gallery->url->path('import');
		if (file_exists($acp_import_dir) && is_writable($acp_import_dir))
		{
			$acp_import_installed = true;
		}
		if ($prune && empty($prune_pattern))
		{
			$prune_pattern['image_album_id'] = implode(',', $request->variable('prune_album_ids', array(0)));
			if (isset($_POST['prune_username_check']))
			{
				$usernames = $request->variable('prune_usernames', '', true);
				$usernames = explode("\n", $usernames);
				$prune_pattern['image_user_id'] = array();
				if (!empty($usernames))
				{
					if (!function_exists('user_get_id_name'))
					{
						$phpbb_ext_gallery->url->_include('functions_user', 'phpbb');
					}
					user_get_id_name($user_ids, $usernames);
					$prune_pattern['image_user_id'] = $user_ids;
				}
				if (isset($_POST['prune_anonymous']))
				{
					$prune_pattern['image_user_id'][] = ANONYMOUS;
				}
				$prune_pattern['image_user_id'] = implode(',', $prune_pattern['image_user_id']);
			}
			if (isset($_POST['prune_time_check']))
			{
				$prune_time = explode('-', $request->variable('prune_time', ''));

				if (sizeof($prune_time) == 3)
				{
					$prune_pattern['image_time'] = @gmmktime(0, 0, 0, (int) $prune_time[1], (int) $prune_time[2], (int) $prune_time[0]);
				}
			}
			if (isset($_POST['prune_comments_check']))
			{
				$prune_pattern['image_comments'] = $request->variable('prune_comments', 0);
			}
			if (isset($_POST['prune_ratings_check']))
			{
				$prune_pattern['image_rates'] = $request->variable('prune_ratings', 0);
			}
			if (isset($_POST['prune_rating_avg_check']))
			{
				$prune_pattern['image_rate_avg'] = (int) ($request->variable('prune_rating_avg', 0.0) * 100);
			}
		}

		$s_hidden_fields = build_hidden_fields(array(
			'source'		=> $missing_sources,
			'entry'			=> $missing_entries,
			'author'		=> $missing_authors,
			'comment'		=> $missing_comments,
			'personal'		=> $missing_personals,
			'personal_bad'	=> $personals_bad,
			'prune_pattern'	=> $prune_pattern,
			'move_to_import'	=> $move_to_import,
		));

		if ($submit)
		{
			$user_id = 1;
			if ($new_author)
			{
				$user_id = 0;
				if (!function_exists('user_get_id_name'))
				{
					$phpbb_ext_gallery->url->_include('functions_user', 'phpbb');
				}
				user_get_id_name($user_id, $new_author);
				if (is_array($user_id) && !empty($user_id))
				{
					$user_id = $user_id[0];
				}
				if (!$user_id)
				{
					trigger_error($user->lang('CLEAN_USER_NOT_FOUND', $new_author) . adm_back_link($this->u_action), E_USER_WARNING);
				}
			}
			if ($missing_authors)
			{
				$sql = 'UPDATE ' . $table_prefix . 'gallery_images
					SET image_user_id = ' . $user_id . ",
						image_user_colour = ''
					WHERE " . $db->sql_in_set('image_id', $missing_authors);
				$db->sql_query($sql);
			}
			if ($missing_comments)
			{
				$sql = 'UPDATE ' . $table_prefix . 'gallery_comments
					SET comment_user_id = ' . $user_id . ",
						comment_user_colour = ''
					WHERE " . $db->sql_in_set('comment_id', $missing_comments);
				$db->sql_query($sql);
			}
			trigger_error($user->lang['CLEAN_CHANGED'] . adm_back_link($this->u_action));
		}

		if (confirm_box(true))
		{
			$message = array();
			if ($missing_entries)
			{
				if ($acp_import_installed && $move_to_import)
				{
					foreach($missing_entries as $entrie)
					{
						copy($phpbb_ext_gallery->url->path('upload') . '/' . $entrie, $phpbb_ext_gallery->url->path('import') . '/' . $entrie);
					}
				}
				$message[] = $core_cleanup->delete_files($missing_entries);
			}
			if ($missing_sources)
			{
				$message[] = $core_cleanup->delete_images($missing_sources);
			}
			if ($missing_authors)
			{
				$message[] = $core_cleanup->delete_author_images($missing_entries);
			}
			if ($missing_comments)
			{
				$message[] = $core_cleanup->delete_author_comments($missing_comments);
			}
			if ($missing_personals || $personals_bad)
			{
				$message = array_merge($message, $core_cleanup->delete_pegas($personals_bad, $missing_personals));

				// Only do this, when we changed something about the albums
				$cache->destroy('_albums');
				$gallery_auth->set_user_permissions('all', '');
			}
			if ($prune_pattern)
			{
				$message[] = $core_cleanup->prune($prune_pattern);
			}

			if (empty($message))
			{
				trigger_error($user->lang['CLEAN_NO_ACTION'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Make sure the overall image & comment count is correct...
			$sql = 'SELECT COUNT(image_id) AS num_images, SUM(image_comments) AS num_comments
				FROM ' . $table_prefix . 'gallery_images
				WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$gallery_config->set('num_images', $row['num_images']);
			$gallery_config->set('num_comments', $row['num_comments']);

			$cache->destroy('sql', $table_prefix . 'gallery_albums');
			$cache->destroy('sql', $table_prefix . 'gallery_comments');
			$cache->destroy('sql', $table_prefix . 'gallery_images');
			$cache->destroy('sql', $table_prefix . 'gallery_rates');
			$cache->destroy('sql', $table_prefix . 'gallery_reports');
			$cache->destroy('sql', $table_prefix . 'gallery_watch');

			$message_string = '';
			foreach ($message as $lang_key)
			{
				$message_string .= (($message_string) ? '<br />' : '') . $user->lang[$lang_key];
			}

			trigger_error($message_string . adm_back_link($this->u_action));
		}
		else if ($delete || $prune || (isset($_POST['cancel'])))
		{
			if (isset($_POST['cancel']))
			{
				trigger_error($user->lang['CLEAN_GALLERY_ABORT'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
			else
			{
				$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang['CONFIRM_CLEAN'];
				if ($missing_sources)
				{
					$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang['CONFIRM_CLEAN_SOURCES'] . '<br />' . $user->lang['CLEAN_GALLERY_CONFIRM'];
				}
				if ($missing_entries)
				{
					$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang['CONFIRM_CLEAN_ENTRIES'] . '<br />' . $user->lang['CLEAN_GALLERY_CONFIRM'];
				}
				if ($missing_authors)
				{
					$core_cleanup->delete_author_images($missing_authors);
					$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang['CONFIRM_CLEAN_AUTHORS'] . '<br />' . $user->lang['CLEAN_GALLERY_CONFIRM'];
				}
				if ($missing_comments)
				{
					$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang['CONFIRM_CLEAN_COMMENTS'] . '<br />' . $user->lang['CLEAN_GALLERY_CONFIRM'];
				}
				if ($personals_bad || $missing_personals)
				{
					$sql = 'SELECT album_name, album_user_id
						FROM ' . $table_prefix . 'gallery_albums
						WHERE ' . $db->sql_in_set('album_user_id', array_merge($missing_personals, $personals_bad));
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						if (in_array($row['album_user_id'], $personals_bad))
						{
							$personals_bad_names[] = $row['album_name'];
						}
						else
						{
							$missing_personals_names[] = $row['album_name'];
						}
					}
					$db->sql_freeresult($result);
				}
				if ($missing_personals)
				{
					$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang('CONFIRM_CLEAN_PERSONALS', implode(', ', $missing_personals_names)) . '<br />' . $user->lang['CLEAN_GALLERY_CONFIRM'];
				}
				if ($personals_bad)
				{
					$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang('CONFIRM_CLEAN_PERSONALS_BAD', implode(', ', $personals_bad_names)) . '<br />' . $user->lang['CLEAN_GALLERY_CONFIRM'];
				}
				if ($prune && empty($prune_pattern))
				{
					trigger_error($user->lang['CLEAN_PRUNE_NO_PATTERN'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				elseif ($prune && $prune_pattern)
				{
					$user->lang['CLEAN_GALLERY_CONFIRM'] = $user->lang('CONFIRM_PRUNE', $core_cleanup->lang_prune_pattern($prune_pattern)) . '<br />' . $user->lang['CLEAN_GALLERY_CONFIRM'];
				}
				confirm_box(false, 'CLEAN_GALLERY', $s_hidden_fields);
			}
		}

		$requested_source = array();
		$sql_array = array(
			'SELECT'		=> 'i.image_id, i.image_name, i.image_filemissing, i.image_filename, i.image_username, u.user_id',
			'FROM'			=> array($table_prefix . 'gallery_images' => 'i'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(USERS_TABLE => 'u'),
					'ON'		=> 'u.user_id = i.image_user_id',
				),
			),
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['image_filemissing'])
			{
				$template->assign_block_vars('sourcerow', array(
					'IMAGE_ID'		=> $row['image_id'],
					'IMAGE_NAME'	=> $row['image_name'],
				));
			}
			if (!$row['user_id'])
			{
				$template->assign_block_vars('authorrow', array(
					'IMAGE_ID'		=> $row['image_id'],
					'AUTHOR_NAME'	=> $row['image_username'],
				));
			}
			$requested_source[] = $row['image_filename'];
		}
		$db->sql_freeresult($result);

		$check_mode = $request->variable('check_mode', '');
		if ($check_mode == 'source')
		{
			$source_missing = array();

			// Reset the status: a image might have been viewed without file but the file is back
			$sql = 'UPDATE ' . $table_prefix . 'gallery_images
				SET image_filemissing = 0';
			$db->sql_query($sql);

			$sql = 'SELECT image_id, image_filename, image_filemissing
				FROM ' . $table_prefix . 'gallery_images';
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				if (!file_exists($phpbb_ext_gallery->url->path('upload') . $row['image_filename']))
				{
					$source_missing[] = $row['image_id'];
				}
			}
			$db->sql_freeresult($result);

			if ($source_missing)
			{
				$sql = 'UPDATE ' . $table_prefix . "gallery_images
					SET image_filemissing = 1
					WHERE " . $db->sql_in_set('image_id', $source_missing);
				$db->sql_query($sql);
			}
		}

		if ($check_mode == 'entry')
		{
			$directory = $phpbb_ext_gallery->url->path('upload');
			$handle = opendir($directory);
			while ($file = readdir($handle))
			{
				if (!is_dir($directory . $file) &&
				 ((substr(strtolower($file), '-4') == '.png') || (substr(strtolower($file), '-4') == '.gif') || (substr(strtolower($file), '-4') == '.jpg'))
				 && !in_array($file, $requested_source)
				)
				{
					if ((strpos($file, 'image_not_exist') !== false) || (strpos($file, 'not_authorised') !== false) || (strpos($file, 'no_hotlinking') !== false))
					{
						continue;
					}

					$template->assign_block_vars('entryrow', array(
						'FILE_NAME'				=> utf8_encode($file),
					));
				}
			}
			closedir($handle);
		}


		$sql_array = array(
			'SELECT'		=> 'c.comment_id, c.comment_image_id, c.comment_username, u.user_id',
			'FROM'			=> array($table_prefix . 'gallery_comments' => 'c'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(USERS_TABLE => 'u'),
					'ON'		=> 'u.user_id = c.comment_user_id',
				),
			),
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (!$row['user_id'])
			{
				$template->assign_block_vars('commentrow', array(
					'COMMENT_ID'	=> $row['comment_id'],
					'IMAGE_ID'		=> $row['comment_image_id'],
					'AUTHOR_NAME'	=> $row['comment_username'],
				));
			}
		}
		$db->sql_freeresult($result);

		$sql_array = array(
			'SELECT'		=> 'a.album_id, a.album_user_id, a.album_name, u.user_id, a.album_images_real',
			'FROM'			=> array($table_prefix . 'gallery_albums' => 'a'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(USERS_TABLE => 'u'),
					'ON'		=> 'u.user_id = a.album_user_id',
				),
			),

			'WHERE'			=> 'a.album_user_id <> ' . $gallery_album->get_public() . ' AND a.parent_id = 0',
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$personalrow = $personal_bad_row = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$album = array(
				'user_id'		=> $row['album_user_id'],
				'album_id'		=> $row['album_id'],
				'album_name'	=> $row['album_name'],
				'images'		=> $row['album_images_real'],
			);
			if (!$row['user_id'])
			{
				$personalrow[$row['album_user_id']] = $album;
			}
			$personal_bad_row[$row['album_user_id']] = $album;
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT ga.album_user_id, ga.album_images_real
			FROM ' . $table_prefix . 'gallery_albums ga
			WHERE ga.album_user_id <> ' . $gallery_album->get_public() . '
				AND ga.parent_id <> 0';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (isset($personalrow[$row['album_user_id']]))
			{
				$personalrow[$row['album_user_id']]['images'] = $personalrow[$row['album_user_id']]['images'] + $row['album_images_real'];
			}
			$personal_bad_row[$row['album_user_id']]['images'] = $personal_bad_row[$row['album_user_id']]['images'] + $row['album_images_real'];
		}
		$db->sql_freeresult($result);

		foreach ($personalrow as $key => $row)
		{
			$template->assign_block_vars('personalrow', array(
				'USER_ID'		=> $row['user_id'],
				'ALBUM_ID'		=> $row['album_id'],
				'AUTHOR_NAME'	=> $row['album_name'],
			));
		}
		foreach ($personal_bad_row as $key => $row)
		{
			$template->assign_block_vars('personal_bad_row', array(
				'USER_ID'		=> $row['user_id'],
				'ALBUM_ID'		=> $row['album_id'],
				'AUTHOR_NAME'	=> $row['album_name'],
				'IMAGES'		=> $row['images'],
			));
		}

		$template->assign_vars(array(
			'S_GALLERY_MANAGE_RESTS'		=> true,
			'ACP_GALLERY_TITLE'				=> $user->lang['ACP_GALLERY_CLEANUP'],
			'ACP_GALLERY_TITLE_EXPLAIN'		=> $user->lang['ACP_GALLERY_CLEANUP_EXPLAIN'],
			'ACP_IMPORT_INSTALLED'	=> $acp_import_installed,
			'CHECK_SOURCE'			=> $this->u_action . '&amp;check_mode=source',
			'CHECK_ENTRY'			=> $this->u_action . '&amp;check_mode=entry',

			'U_FIND_USERNAME'		=> $phpbb_ext_gallery->url->append_sid('phpbb', 'memberlist', 'mode=searchuser&amp;form=acp_gallery&amp;field=prune_usernames'),
			'S_SELECT_ALBUM'		=> $gallery_album->get_albumbox(false, '', false, false, false, $gallery_album->get_public(), $gallery_album->get_type_upload()),

			'S_FOUNDER'				=> ($user->data['user_type'] == USER_FOUNDER) ? true : false,
		));
	}
}
