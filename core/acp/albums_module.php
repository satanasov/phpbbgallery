<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* mostly borrowed from phpBB3
* @author: phpBB Group
* @location: includes/acp/acp_forums.php
*
* Note: There are several code parts commented out, for example the album/forum_password.
*       I didn't remove them, to have it easier when I implement this feature one day. I hope it's okay.
*/

namespace phpbbgallery\core\acp;

/**
* @package acp
*/
class albums_module
{
	var $u_action;
	var $parent_id = 0;

	function main($id, $mode)
	{
		global $auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path, $phpbb_ext_gallery, $request;
		global $phpbb_dispatcher, $table_prefix, $table_name, $phpbb_container, $request, $moderators_table, $permissions_table, $roles_table, $users_table;
		$helper = $phpbb_container->get('controller.helper');
		$pagination = $phpbb_container->get('pagination');

		// Let us define some helpers;
		$albums_table = $table_prefix . 'gallery_albums';
		$contests_table = $table_prefix . 'gallery_contests';
		$tracking_table = $table_prefix . 'gallery_albums_tracking';
		// Init ext gallery
		$user->add_lang_ext('phpbbgallery/core', array('gallery_acp', 'gallery'));

		$gallery_user = $phpbb_container->get('phpbbgallery.core.user');
		$phpbb_ext_gallery_core_auth = $phpbb_container->get('phpbbgallery.core.auth');
		$phpbb_ext_gallery_core_url = $phpbb_container->get('phpbbgallery.core.url');

		// Init manage albums
		$manage_albums = $phpbb_container->get('phpbbgallery.core.album.manage');
		$manage_albums->set_user($request->variable('user_id', 0));
		$manage_albums->set_parent($request->variable('parent_id', 0));
		$manage_albums->set_u_action($this->u_action);

		// Init album
		$phpbb_ext_gallery_core_album = $phpbb_container->get('phpbbgallery.core.album');

		$phpbb_ext_gallery_core_album_display = $phpbb_container->get('phpbbgallery.core.album.display');

		// Init contest
		$phpbb_gallery_contest = $phpbb_container->get('phpbbgallery.core.contest');

		$this->tpl_name = 'gallery_albums';
		$this->page_title = 'ACP_GALLERY_MANAGE_ALBUMS';

		$form_key = 'acp_gallery_albums';
		add_form_key($form_key);

		$action		= $request->variable('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$album_id	= $request->variable('a', 0);

		$this->parent_id	= $request->variable('parent_id', 0);
		$album_data = $errors = array();
		if ($update && !check_form_key($form_key))
		{
			$update = false;
			$errors[] = $user->lang['FORM_INVALID'];
		}

		// Major routines
		if ($update)
		{
			switch ($action)
			{
				case 'delete':
					$action_subalbums	= $request->variable('action_subalbums', '');
					$subalbums_to_id	= $request->variable('subalbums_to_id', 0);
					$action_images		= $request->variable('action_images', '');
					$images_to_id		= $request->variable('images_to_id', 0);

					$errors = $manage_albums->delete_album($album_id, $action_images, $action_subalbums, $images_to_id, $subalbums_to_id);

					if (sizeof($errors))
					{
						break;
					}

					$cache->destroy('sql', $table_prefix . 'gallery_albums');

					trigger_error($user->lang['ALBUM_DELETED'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id));

				break;

				/** @noinspection PhpMissingBreakStatementInspection */
				case 'edit':
					$album_data = array(
						'album_id'		=>	$album_id
					);

				// No break; here

				case 'add':

					$album_data += array(
						'parent_id'				=> $request->variable('album_parent_id', $this->parent_id),
						'album_type'			=> $request->variable('album_type', \phpbbgallery\core\block::TYPE_UPLOAD),
						'type_action'			=> $request->variable('type_action', ''),
						'album_status'			=> $request->variable('album_status', \phpbbgallery\core\block::ALBUM_OPEN),
						'album_parents'			=> '',
						'album_name'			=> utf8_normalize_nfc($request->variable('album_name', '', true)),
						'album_desc'			=> utf8_normalize_nfc($request->variable('album_desc', '', true)),
						'album_desc_uid'		=> '',
						'album_desc_options'	=> 7,
						'album_desc_bitfield'	=> '',
						'album_image'			=> $request->variable('album_image', ''),
						'album_watermark'		=> $request->variable('album_watermark', false),
						'album_sort_key'		=> $request->variable('album_sort_key', ''),
						'album_sort_dir'		=> $request->variable('album_sort_dir', ''),
						'display_subalbum_list'	=> $request->variable('display_subalbum_list', false),
						'display_on_index'		=> $request->variable('display_on_index', false),
						'display_in_rrc'		=> $request->variable('display_in_rrc', false),
						/*
						'album_password'		=> $request->variable('album_password', '', true),
						'album_password_confirm'=> $request->variable('album_password_confirm', '', true),
						'album_password_unset'	=> $request->variable('album_password_unset', false),
						*/
					);

					/**
					* Event to send requested data
					* @event phpbbgallery.core.acp.albums.request_data
					* @var	string	action		Action we are taking
					* @var	int		album_id	Album we are doing it to
					* @var	array	album_data	Album data for the album
					* @since 1.2.0
					*/
					$vars = array('action', 'album_id', 'album_data');
					extract($phpbb_dispatcher->trigger_event('phpbbgallery.core.acp.albums.request_data', compact($vars)));

					// Categories are not able to be locked...
					if ($album_data['album_type'] == \phpbbgallery\core\block::TYPE_CAT)
					{
						$album_data['album_status'] = \phpbbgallery\core\block::ALBUM_OPEN;
					}

					// Contests need contest_data, freaky... :-O
					$contest_data = array(
						'contest_start'			=> $request->variable('contest_start', ''),
						'contest_rating'		=> $request->variable('contest_rating', ''),
						'contest_end'			=> $request->variable('contest_end', ''),
					);

					// Get data for album description if specified
					if ($album_data['album_desc'])
					{
						generate_text_for_storage($album_data['album_desc'], $album_data['album_desc_uid'], $album_data['album_desc_bitfield'], $album_data['album_desc_options'], $request->variable('desc_parse_bbcode', false), $request->variable('desc_parse_urls', false), $request->variable('desc_parse_smilies', false));
					}

					$errors = $manage_albums->update_album_data($album_data, $contest_data);

					if (!sizeof($errors))
					{
						$album_perm_from = $request->variable('album_perm_from', 0);

						// Copy permissions? You do not need permissions for that in the gallery
						if ($album_perm_from && $album_perm_from != $album_data['album_id'])
						{
							// If we edit a album delete current permissions first
							if ($action == 'edit')
							{
								$sql = 'DELETE FROM ' . $table_prefix . 'gallery_permissions
									WHERE perm_album_id = ' . (int) $album_data['album_id'];
								$db->sql_query($sql);

								$sql = 'DELETE FROM ' . $table_prefix . 'gallery_modscache
									WHERE album_id = ' . (int) $album_data['album_id'];
								$db->sql_query($sql);
							}

							$sql = 'SELECT *
								FROM ' . $table_prefix . 'gallery_permissions
								WHERE perm_album_id = ' . $album_perm_from;
							$result = $db->sql_query($sql);
							$perm_data = array();
							while ($row = $db->sql_fetchrow($result))
							{
								$perm_data[] = array(
									'perm_role_id'					=> $row['perm_role_id'],
									'perm_album_id'					=> $album_data['album_id'],
									'perm_user_id'					=> $row['perm_user_id'],
									'perm_group_id'					=> $row['perm_group_id'],
									'perm_system'					=> $row['perm_system'],
								);
							}
							$db->sql_freeresult($result);

							$modscache_ary = array();
							$sql = 'SELECT * FROM ' . $table_prefix . 'gallery_modscache
								WHERE album_id = ' . (int) $album_perm_from;
							$result = $db->sql_query($sql);
							while ($row = $db->sql_fetchrow($result))
							{
								$modscache_ary[] = array(
									'album_id'			=> $album_data['album_id'],
									'user_id'			=> $row['user_id'],
									'username'			=> $row['username'],
									'group_id'			=> $row['group_id'],
									'group_name'		=> $row['group_name'],
									'display_on_index'	=> $row['display_on_index'],
								);
							}
							$db->sql_freeresult($result);

							$db->sql_multi_insert($table_prefix . 'gallery_permissions', $perm_data);
							$db->sql_multi_insert($table_prefix . 'gallery_modscache', $modscache_ary);
						}

						$cache->destroy('sql', $table_prefix . 'gallery_albums');
						$cache->destroy('sql', $table_prefix . 'gallery_modscache');
						$cache->destroy('sql', $table_prefix . 'gallery_permissions');
						$cache->destroy('_albums');
						$phpbb_ext_gallery_core_auth->set_user_permissions('all', '');

						$acl_url = '&amp;mode=manage&amp;action=v_mask&amp;album_id[]=' . $album_data['album_id'];

						$message = ($action == 'add') ? $user->lang['ALBUM_CREATED'] : $user->lang['ALBUM_UPDATED'];
						$message .= '<br /><br />' . sprintf($user->lang['REDIRECT_ACL'], '<a href="' . $phpbb_ext_gallery_core_url->append_sid('admin' , 'index', 'i=-phpbbgallery-core-acp-permissions_module' . $acl_url) . '">', '</a>');

						// Redirect directly to permission settings screen
						if ($action == 'add' && !$album_perm_from)
						{
							meta_refresh(5, $phpbb_ext_gallery_core_url->append_sid('admin' , 'index', 'i=-phpbbgallery-core-acp-permissions_module' . $acl_url));
						}

						trigger_error($message . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id));
					}

				break;
			}
		}

		switch ($action)
		{
			case 'move_up':
			case 'move_down':

				if (!$album_id)
				{
					trigger_error($user->lang['NO_ALBUM'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $table_prefix . 'gallery_albums
					WHERE album_id = ' . (int) $album_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($user->lang['NO_ALBUM'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				$move_album_name = $manage_albums->move_album_by($row, $action, 1);

				if ($move_album_name !== false)
				{
					$log = $phpbb_container->get('phpbbgallery.core.log');
					$log->add_log('admin', 'move', $row['album_id'], 0, array('LOG_ALBUM_' . strtoupper($action), $row['album_name'], $move_album_name));
					$cache->destroy('sql', $table_prefix . 'gallery_albums');
				}

			break;

			case 'sync':
			case 'sync_album':
				if (!$album_id)
				{
					trigger_error($user->lang['NO_ALBUM'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				$sql = 'SELECT album_name, album_type
					FROM ' . $table_prefix . 'gallery_albums
					WHERE album_id = ' . (int) $album_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($user->lang['NO_ALBUM'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				$phpbb_ext_gallery_core_album->update_info($album_id);

				$log = $phpbb_container->get('phpbbgallery.core.log');
				$log->add_log('admin', 'resync', $album_id, 0, array('LOG_ALBUM_SYNC', $row['album_name']));

				$template->assign_var('L_ALBUM_RESYNCED', sprintf($user->lang['ALBUM_RESYNCED'], $row['album_name']));

			break;

			case 'add':
			case 'edit':

				// Show form to create/modify a album
				if ($action == 'edit')
				{
					$this->page_title = 'EDIT_ALBUM';
					$row = $phpbb_ext_gallery_core_album->get_info($album_id);
					$old_album_type = $row['album_type'];

					if (!$update)
					{
						$album_data = $row;
					}
					else
					{
						$album_data['left_id'] = $row['left_id'];
						$album_data['right_id'] = $row['right_id'];
					}
					if ($row['album_type'] == \phpbbgallery\core\block::TYPE_CONTEST)
					{
						$contest_data = $phpbb_gallery_contest->get_contest($album_id, 'album');
					}
					else
					{
						// Default values, 3 days later rate and 7 for the end of the contest
						$contest_data = array(
							'contest_start'			=> time(),
							'contest_rating'		=> 3 * 86400,
							'contest_end'			=> 7 * 86400,
						);
					}

					// Make sure no direct child albums are able to be selected as parents.
					$exclude_albums = array();
					foreach ($phpbb_ext_gallery_core_album_display->get_branch(\phpbbgallery\core\block::PUBLIC_ALBUM, $album_id, 'children') as $row)
					{
						$exclude_albums[] = $row['album_id'];
					}

					$parents_list = $phpbb_ext_gallery_core_album->get_albumbox(true, '', $album_data['parent_id'], false, $exclude_albums);

					/*
					$album_data['album_password_confirm'] = $album_data['album_password'];
					*/
				}
				else
				{
					$this->page_title = 'CREATE_ALBUM';

					$album_id = $this->parent_id;
					$parents_list = $phpbb_ext_gallery_core_album->get_albumbox(true, '', $this->parent_id);

					// Fill album data with default values
					if (!$update)
					{
						$album_data = array(
							'parent_id'				=> $this->parent_id,
							'album_type'			=> \phpbbgallery\core\block::TYPE_UPLOAD,
							'album_status'			=> \phpbbgallery\core\block::ALBUM_OPEN,
							'album_name'			=> utf8_normalize_nfc($request->variable('album_name', '', true)),
							'album_desc'			=> '',
							'album_image'			=> '',
							'album_watermark'		=> true,
							'album_sort_key'		=> '',
							'album_sort_dir'		=> '',
							'display_subalbum_list'	=> true,
							'display_on_index'		=> true,
							'display_in_rrc'		=> true,
							/*
							'album_password'		=> '',
							'album_password_confirm'=> '',
							*/
						);

						/**
						* Event to send default data
						*
						* @event phpbbgallery.core.acp.albums.default_data
						* @var	action	action		Action taken
						* @var	array	album_data	Album data array
						* @since 1.2.0
						*/
						$vars = array('action', 'album_data');
						extract($phpbb_dispatcher->trigger_event('phpbbgallery.core.acp.albums.default_data', compact($vars)));

						// Default values, 3 days later rate and 7 for the end of the contest
						$contest_data = array(
							'contest_start'			=> time(),
							'contest_rating'		=> 3 * 86400,
							'contest_end'			=> 7 * 86400,
						);
					}
				}

				$album_desc_data = array(
					'text'			=> $album_data['album_desc'],
					'allow_bbcode'	=> true,
					'allow_smilies'	=> true,
					'allow_urls'	=> true
				);

				// Parse desciption if specified
				if ($album_data['album_desc'])
				{
					if (!isset($album_data['album_desc_uid']))
					{
						// Before we are able to display the preview and plane text, we need to parse our request_var()'d value...
						$album_data['album_desc_uid'] = '';
						$album_data['album_desc_bitfield'] = '';
						$album_data['album_desc_options'] = 0;

						generate_text_for_storage($album_data['album_desc'], $album_data['album_desc_uid'], $album_data['album_desc_bitfield'], $album_data['album_desc_options'], $request->variable('desc_allow_bbcode', false), $request->variable('desc_allow_urls', false), $request->variable('desc_allow_smilies', false));
					}

					// decode...
					$album_desc_data = generate_text_for_edit($album_data['album_desc'], $album_data['album_desc_uid'], $album_data['album_desc_options']);
				}

				$album_type_options = '';
				$album_type_ary = array(\phpbbgallery\core\block::TYPE_CAT => 'CAT', \phpbbgallery\core\block::TYPE_UPLOAD => 'UPLOAD');//, $phpbb_ext_gallery_core_album::TYPE_CONTEST => 'CONTEST');

				foreach ($album_type_ary as $value => $lang)
				{
					$album_type_options .= '<option value="' . $value . '"' . (($value == $album_data['album_type']) ? ' selected="selected"' : '') . '>' . $user->lang['ALBUM_TYPE_' . $lang] . '</option>';
				}

				$album_sort_key_options = '';
				$album_sort_key_options .= '<option' . ((!in_array($album_data['album_sort_key'], array('t', 'n', 'vc', 'u', 'ra', 'r', 'c', 'lc'))) ? ' selected="selected"' : '') . " value=''>" . $user->lang['SORT_DEFAULT'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 't') ? ' selected="selected"' : '') . " value='t'>" . $user->lang['TIME'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 'n') ? ' selected="selected"' : '') . " value='n'>" . $user->lang['IMAGE_NAME'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 'vc') ? ' selected="selected"' : '') . " value='vc'>" . $user->lang['GALLERY_VIEWS'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 'u') ? ' selected="selected"' : '') . " value='u'>" . $user->lang['USERNAME'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 'ra') ? ' selected="selected"' : '') . " value='ra'>" . $user->lang['RATING'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 'r') ? ' selected="selected"' : '') . " value='r'>" . $user->lang['RATES_COUNT'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 'c') ? ' selected="selected"' : '') . " value='c'>" . $user->lang['COMMENTS'] . '</option>';
				$album_sort_key_options .= '<option' . (($album_data['album_sort_key'] == 'lc') ? ' selected="selected"' : '') . " value='lc'>" . $user->lang['NEW_COMMENT'] . '</option>';

				$album_sort_dir_options = '';
				$album_sort_dir_options .= '<option' . ((($album_data['album_sort_dir'] != 'd') && ($album_data['album_sort_dir'] != 'a')) ? ' selected="selected"' : '') . " value=''>" . $user->lang['SORT_DEFAULT'] . '</option>';
				$album_sort_dir_options .= '<option' . (($album_data['album_sort_dir'] == 'd') ? ' selected="selected"' : '') . " value='d'>" . $user->lang['SORT_DESCENDING'] . '</option>';
				$album_sort_dir_options .= '<option' . (($album_data['album_sort_dir'] == 'a') ? ' selected="selected"' : '') . " value='a'>" . $user->lang['SORT_ASCENDING'] . '</option>';

				$statuslist = '<option value="' . \phpbbgallery\core\block::ALBUM_OPEN . '"' . (($album_data['album_status'] == \phpbbgallery\core\block::ALBUM_OPEN) ? ' selected="selected"' : '') . '>' . $user->lang['UNLOCKED'] . '</option><option value="' . \phpbbgallery\core\block::ALBUM_LOCKED . '"' . (($album_data['album_status'] == \phpbbgallery\core\block::ALBUM_LOCKED) ? ' selected="selected"' : '') . '>' . $user->lang['LOCKED'] . '</option>';

				$sql = 'SELECT album_id
					FROM ' . $table_prefix . 'gallery_albums
					WHERE album_type = ' . \phpbbgallery\core\block::TYPE_UPLOAD . '
						AND album_user_id = ' . \phpbbgallery\core\block::PUBLIC_ALBUM . '
						AND album_id <> ' . (int) $album_id;
				$result = $db->sql_query_limit($sql, 1);

				$uploadable_album_exists = false;
				if ($db->sql_fetchrow($result))
				{
					$uploadable_album_exists = true;
				}
				$db->sql_freeresult($result);

				// Subalbum move options
				if ($action == 'edit' && in_array($album_data['album_type'], array(\phpbbgallery\core\block::TYPE_UPLOAD, \phpbbgallery\core\block::TYPE_CONTEST)))
				{
					$subalbums_id = array();
					$subalbums = $phpbb_ext_gallery_core_album_display->get_branch(\phpbbgallery\core\block::PUBLIC_ALBUM, $album_id, 'children');

					foreach ($subalbums as $row)
					{
						$subalbums_id[] = $row['album_id'];
					}

					$albums_list = $phpbb_ext_gallery_core_album->get_albumbox(true, '', $album_data['parent_id'], false, $subalbums_id);

					if ($uploadable_album_exists)
					{
						$template->assign_vars(array(
							'S_MOVE_ALBUM_OPTIONS'		=> $phpbb_ext_gallery_core_album->get_albumbox(true, '', $album_data['parent_id'], false, $subalbums_id, \phpbbgallery\core\block::PUBLIC_ALBUM, \phpbbgallery\core\block::TYPE_UPLOAD),
						));
					}

					$template->assign_vars(array(
						'S_HAS_SUBALBUMS'		=> ($album_data['right_id'] - $album_data['left_id'] > 1) ? true : false,
						'S_ALBUMS_LIST'			=> $albums_list,
					));
				}
				else if ($uploadable_album_exists)
				{
					$template->assign_vars(array(
						'S_MOVE_ALBUM_OPTIONS'		=> $phpbb_ext_gallery_core_album->get_albumbox(true, '', $album_data['parent_id'], false, $album_id, 0, \phpbbgallery\core\block::TYPE_UPLOAD),
					));
				}

				/*
				if (strlen($album_data['album_password']) == 32)
				{
					$errors[] = $user->lang['ALBUM_PASSWORD_OLD'];
				}
				*/

				$template->assign_vars(array(
					'S_EDIT_ALBUM'		=> true,
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'S_PARENT_ID'		=> $this->parent_id,
					'S_ALBUM_PARENT_ID'	=> $album_data['parent_id'],
					'S_ADD_ACTION'		=> ($action == 'add') ? true : false,

					'U_BACK'			=> $this->u_action . '&amp;parent_id=' . $this->parent_id,
					'U_EDIT_ACTION'		=> $this->u_action . "&amp;parent_id={$this->parent_id}&amp;action=$action&amp;a=$album_id",

					'L_COPY_PERMISSIONS_EXPLAIN'	=> $user->lang['COPY_PERMISSIONS_' . strtoupper($action) . '_EXPLAIN'],
					'L_TITLE'						=> $user->lang[$this->page_title],
					'ERROR_MSG'						=> (sizeof($errors)) ? implode('<br />', $errors) : '',

					'ALBUM_NAME'				=> $album_data['album_name'],
					'ALBUM_IMAGE'				=> $album_data['album_image'],
					'ALBUM_IMAGE_SRC'			=> ($album_data['album_image']) ? $phpbb_ext_gallery_core_url->path('phpbb') . $album_data['album_image'] : '',
					/*
					'S_ALBUM_PASSWORD_SET'		=> (empty($album_data['album_password'])) ? false : true,
					*/

					'ALBUM_DESC'				=> $album_desc_data['text'],
					'S_DESC_BBCODE_CHECKED'		=> ($album_desc_data['allow_bbcode']) ? true : false,
					'S_DESC_SMILIES_CHECKED'	=> ($album_desc_data['allow_smilies']) ? true : false,
					'S_DESC_URLS_CHECKED'		=> ($album_desc_data['allow_urls']) ? true : false,

					'S_ALBUM_TYPE_OPTIONS'		=> $album_type_options,
					'S_STATUS_OPTIONS'			=> $statuslist,
					'S_PARENT_OPTIONS'			=> $parents_list,
					'S_ALBUM_OPTIONS'			=> $phpbb_ext_gallery_core_album->get_albumbox(true, '', ($action == 'add') ? $album_data['parent_id'] : false, false, ($action == 'edit') ? $album_data['album_id'] : false),

					'S_ALBUM_ORIG_UPLOAD'		=> (isset($old_album_type) && $old_album_type == \phpbbgallery\core\block::TYPE_UPLOAD) ? true : false,
					'S_ALBUM_ORIG_CAT'			=> (isset($old_album_type) && $old_album_type == \phpbbgallery\core\block::TYPE_CAT) ? true : false,
					'S_ALBUM_ORIG_CONTEST'		=> (isset($old_album_type) && $old_album_type == \phpbbgallery\core\block::TYPE_CONTEST) ? true : false,
					'S_ALBUM_UPLOAD'			=> ($album_data['album_type'] == \phpbbgallery\core\block::TYPE_UPLOAD) ? true : false,
					'S_ALBUM_CAT'				=> ($album_data['album_type'] == \phpbbgallery\core\block::TYPE_CAT) ? true : false,
					'S_ALBUM_CONTEST'			=> ($album_data['album_type'] == \phpbbgallery\core\block::TYPE_CONTEST) ? true : false,
					'ALBUM_UPLOAD'				=> \phpbbgallery\core\block::TYPE_UPLOAD,
					'ALBUM_CAT'					=> \phpbbgallery\core\block::TYPE_CAT,
					'ALBUM_CONTEST'				=> \phpbbgallery\core\block::TYPE_CONTEST,
					'S_CAN_COPY_PERMISSIONS'	=> true,

					'S_ALBUM_WATERMARK'			=> ($album_data['album_watermark']) ? true : false,
					'ALBUM_SORT_KEY_OPTIONS'	=> $album_sort_key_options,
					'ALBUM_SORT_DIR_OPTIONS'	=> $album_sort_dir_options,
					'S_DISPLAY_SUBALBUM_LIST'	=> ($album_data['display_subalbum_list']) ? true : false,
					'S_DISPLAY_ON_INDEX'		=> ($album_data['display_on_index']) ? true : false,
					'S_DISPLAY_IN_RRC'			=> ($album_data['display_in_rrc']) ? true : false,

				//	'S_CONTEST_START'			=> $user->format_date($contest_data['contest_start'], 'Y-m-d H:i'),
				//	'CONTEST_RATING'			=> $user->format_date($contest_data['contest_start'] + $contest_data['contest_rating'], 'Y-m-d H:i'),
				//	'CONTEST_END'				=> $user->format_date($contest_data['contest_start'] + $contest_data['contest_end'], 'Y-m-d H:i'),
				));

				/**
				* Event after assigning data to templete
				*
				* @event phpbbgallery.core.acp.albums.send_to_template
				* @var	action	action		Action taken
				* @var	array	album_data	Album data array
				* @since 1.2.0
				*/
				$vars = array('action', 'album_data');
				extract($phpbb_dispatcher->trigger_event('phpbbgallery.core.acp.albums.send_to_template', compact($vars)));

				return;

			break;

			case 'delete':

				if (!$album_id)
				{
					trigger_error($user->lang['NO_ALBUM'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				$album_data = $phpbb_ext_gallery_core_album->get_info($album_id);

				$subalbums_id = array();
				$subalbums = $phpbb_ext_gallery_core_album_display->get_branch(\phpbbgallery\core\block::PUBLIC_ALBUM, $album_id, 'children');

				foreach ($subalbums as $row)
				{
					$subalbums_id[] = $row['album_id'];
				}

				$albums_list = $phpbb_ext_gallery_core_album->get_albumbox(true, '', $album_data['parent_id'], false, $subalbums_id);

				$sql = 'SELECT album_id
					FROM ' . $table_prefix . 'gallery_albums
					WHERE album_type = ' . \phpbbgallery\core\block::TYPE_UPLOAD . '
						AND album_id <> ' . (int) $album_id . '
						AND album_user_id = ' . \phpbbgallery\core\block::PUBLIC_ALBUM;
				$result = $db->sql_query_limit($sql, 1);

				if ($db->sql_fetchrow($result))
				{
					$template->assign_vars(array(
						'S_MOVE_ALBUM_OPTIONS'		=> $phpbb_ext_gallery_core_album->get_albumbox(true, '', $album_data['parent_id'], false, $subalbums_id, \phpbbgallery\core\block::PUBLIC_ALBUM, \phpbbgallery\core\block::TYPE_UPLOAD),
					));
				}
				$db->sql_freeresult($result);

				$parent_id = ($this->parent_id == $album_id) ? 0 : $this->parent_id;
				$template->assign_vars(array(
					'S_DELETE_ALBUM'		=> true,
					'U_ACTION'				=> $this->u_action . "&amp;parent_id={$parent_id}&amp;action=delete&amp;a=" . $album_id,
					'U_BACK'				=> $this->u_action . '&amp;parent_id=' . $this->parent_id,

					'ALBUM_NAME'			=> $album_data['album_name'],
					'S_ALBUM_POST'			=> (in_array($album_data['album_type'], array(\phpbbgallery\core\block::TYPE_UPLOAD, \phpbbgallery\core\block::TYPE_CONTEST))) ? true : false,
					'S_HAS_SUBALBUMS'		=> ($album_data['right_id'] - $album_data['left_id'] > 1) ? true : false,
					'S_ALBUMS_LIST'			=> $albums_list,

					'S_ERROR'				=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'				=> (sizeof($errors)) ? implode('<br />', $errors) : '',
				));

				return;
			break;
		}

		// Default management page
		if (!$this->parent_id)
		{
			$navigation = $user->lang['GALLERY_INDEX'];
		}
		else
		{
			$navigation = '<a href="' . $this->u_action . '">' . $user->lang['GALLERY_INDEX'] . '</a>';

			$albums_nav = $phpbb_ext_gallery_core_album_display->get_branch(\phpbbgallery\core\block::PUBLIC_ALBUM, $this->parent_id, 'parents', 'descending');
			foreach ($albums_nav as $row)
			{
				if ($row['album_id'] == $this->parent_id)
				{
					$navigation .= ' -&gt; ' . $row['album_name'];
				}
				else
				{
					$navigation .= ' -&gt; <a href="' . $this->u_action . '&amp;parent_id=' . $row['album_id'] . '">' . $row['album_name'] . '</a>';
				}
			}
		}

		// Jumpbox
		$album_box = $phpbb_ext_gallery_core_album->get_albumbox(true, '', $this->parent_id, false, false);

		if ($action == 'sync' || $action == 'sync_album')
		{
			$template->assign_var('S_RESYNCED', true);
		}

		$sql = 'SELECT *
			FROM ' . $table_prefix . "gallery_albums
			WHERE parent_id = {$this->parent_id}
				AND album_user_id = " . \phpbbgallery\core\block::PUBLIC_ALBUM . '
			ORDER BY left_id';
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				$album_type = $row['album_type'];

				if ($row['album_status'] == \phpbbgallery\core\block::ALBUM_LOCKED)
				{
					$folder_image = '<img src="images/icon_folder_lock.gif" alt="' . $user->lang['LOCKED'] . '" />';
				}
				else
				{
					$folder_image = ($row['left_id'] + 1 != $row['right_id']) ? '<img src="images/icon_subfolder.gif" alt="' . $user->lang['SUBALBUM'] . '" />' : '<img src="images/icon_folder.gif" alt="' . $user->lang['FOLDER'] . '" />';
				}

				$url = $this->u_action . "&amp;parent_id=$this->parent_id&amp;a={$row['album_id']}";

				$template->assign_block_vars('albums', array(
					'FOLDER_IMAGE'		=> $folder_image,
					'ALBUM_IMAGE'		=> ($row['album_image']) ? '<img src="' . $phpbb_ext_gallery_core_url->path('phpbb') . $row['album_image'] . '" alt="" />' : '',
					'ALBUM_IMAGE_SRC'	=> ($row['album_image']) ? $phpbb_ext_gallery_core_url->path('phpbb') . $row['album_image'] : '',
					'ALBUM_NAME'		=> $row['album_name'],
					'ALBUM_DESCRIPTION'	=> generate_text_for_display($row['album_desc'], $row['album_desc_uid'], $row['album_desc_bitfield'], $row['album_desc_options']),
					'ALBUM_IMAGES'		=> $row['album_images'],

					'S_ALBUM_POST'		=> ($album_type != \phpbbgallery\core\block::TYPE_CAT) ? true : false,

					'U_ALBUM'			=> $this->u_action . '&amp;parent_id=' . $row['album_id'],
					'U_MOVE_UP'			=> $url . '&amp;action=move_up',
					'U_MOVE_DOWN'		=> $url . '&amp;action=move_down',
					'U_EDIT'			=> $url . '&amp;action=edit',
					'U_DELETE'			=> $url . '&amp;action=delete',
					'U_SYNC'			=> $url . '&amp;action=sync')
				);
			}
			while ($row = $db->sql_fetchrow($result));
		}
		else if ($this->parent_id)
		{
			$row = $phpbb_ext_gallery_core_album->get_info($this->parent_id);

			$url = $this->u_action . '&amp;parent_id=' . $this->parent_id . '&amp;a=' . $row['album_id'];

			$template->assign_vars(array(
				'S_NO_ALBUMS'		=> true,

				'U_EDIT'			=> $url . '&amp;action=edit',
				'U_DELETE'			=> $url . '&amp;action=delete',
				'U_SYNC'			=> $url . '&amp;action=sync',
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
			'NAVIGATION'	=> $navigation,
			'ALBUM_BOX'		=> $album_box,
			'U_SEL_ACTION'	=> $this->u_action,
			'U_ACTION'		=> $this->u_action . '&amp;parent_id=' . $this->parent_id,

			'U_PROGRESS_BAR'	=> $this->u_action . '&amp;action=progress_bar',
		));
	}

	/**
	 * Display progress bar for syncinc albums
	 *
	 * borrowed from phpBB3
	 * @author: phpBB Group
	 * @function: display_progress_bar
	 * @param $start
	 * @param $total
	 */
	function display_progress_bar($start, $total)
	{
		global $template, $user;

		adm_page_header($user->lang['SYNC_IN_PROGRESS']);

		$template->set_filenames(array(
			'body'	=> 'progress_bar.html',
		));

		$template->assign_vars(array(
			'L_PROGRESS'			=> $user->lang['SYNC_IN_PROGRESS'],
			'L_PROGRESS_EXPLAIN'	=> ($start && $total) ? sprintf($user->lang['SYNC_IN_PROGRESS_EXPLAIN'], $start, $total) : $user->lang['SYNC_IN_PROGRESS'])
		);

		adm_page_footer();
	}
}
