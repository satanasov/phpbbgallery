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

/**
* @package acp
*/
class phpbb_ext_gallery_core_acp_permissions_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path, $permissions, $phpbb_ext_gallery;

		$phpbb_ext_gallery = new phpbb_ext_gallery_core($auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path);
		$phpbb_ext_gallery->init();

		$user->add_lang_ext('gallery/core', array('gallery_acp', 'gallery'));
		$this->tpl_name = 'gallery_permissions';
		$this->page_title = $user->lang['ALBUM_AUTH_TITLE'];
		add_form_key('acp_gallery');
		$submit = (isset($_POST['submit_edit_options'])) ? true : ((isset($_POST['submit_add_options'])) ? true : false);
		$action = request_var('action', '');

		/**
		* All our beautiful permissions
		*/
		$permissions->cats['full'] = array(
			'i'		=> array('i_view', 'i_watermark', 'i_upload', 'i_approve', 'i_edit', 'i_delete', 'i_report', 'i_rate'),
			'c'		=> array('c_read', 'c_post', 'c_edit', 'c_delete'),
			'm'		=> array('m_comments', 'm_delete', 'm_edit', 'm_move', 'm_report', 'm_status'),
			'misc'	=> array('a_list', 'i_count', 'i_unlimited', 'a_count', 'a_unlimited', 'a_restrict'),
		);
		$permissions->p_masks['full'] = array_merge($permissions->cats['full']['i'], $permissions->cats['full']['c'], $permissions->cats['full']['m'], $permissions->cats['full']['misc']);

		// Permissions for the normal albums
		$permissions->cats[phpbb_ext_gallery_core_auth::PUBLIC_ALBUM] = array(
			'i'		=> array('i_view', 'i_watermark', 'i_upload', 'i_approve', 'i_edit', 'i_delete', 'i_report', 'i_rate'),
			'c'		=> array('c_read', 'c_post', 'c_edit', 'c_delete'),
			'm'		=> array('m_comments', 'm_delete', 'm_edit', 'm_move', 'm_report', 'm_status'),
			'misc'	=> array('a_list', 'i_count', 'i_unlimited'/*, 'a_count', 'a_unlimited', 'a_restrict'*/),
		);
		$permissions->p_masks[phpbb_ext_gallery_core_auth::PUBLIC_ALBUM] = array_merge($permissions->cats[phpbb_ext_gallery_core_auth::PUBLIC_ALBUM]['i'], $permissions->cats[phpbb_ext_gallery_core_auth::PUBLIC_ALBUM]['c'], $permissions->cats[phpbb_ext_gallery_core_auth::PUBLIC_ALBUM]['m'], $permissions->cats[phpbb_ext_gallery_core_auth::PUBLIC_ALBUM]['misc']);
		$permissions->p_masks_anti[phpbb_ext_gallery_core_auth::PUBLIC_ALBUM] = array('a_count', 'a_unlimited', 'a_restrict');

		// Permissions for own personal albums
		// Note: we set i_view to 1 as default on storing the permissions
		$permissions->cats[phpbb_ext_gallery_core_auth::OWN_ALBUM] = array(
			'i'		=> array(/*'i_view', */'i_watermark', 'i_upload', 'i_approve', 'i_edit', 'i_delete', 'i_report', 'i_rate'),
			'c'		=> array('c_read', 'c_post', 'c_edit', 'c_delete'),
			'm'		=> array('m_comments', 'm_delete', 'm_edit', 'm_move', 'm_report', 'm_status'),
			'misc'	=> array('a_list', 'i_count', 'i_unlimited', 'a_count', 'a_unlimited', 'a_restrict'),
		);
		$permissions->p_masks[phpbb_ext_gallery_core_auth::OWN_ALBUM] = array_merge($permissions->cats[phpbb_ext_gallery_core_auth::OWN_ALBUM]['i'], $permissions->cats[phpbb_ext_gallery_core_auth::OWN_ALBUM]['c'], $permissions->cats[phpbb_ext_gallery_core_auth::OWN_ALBUM]['m'], $permissions->cats[phpbb_ext_gallery_core_auth::OWN_ALBUM]['misc']);
		$permissions->p_masks_anti[phpbb_ext_gallery_core_auth::OWN_ALBUM] = array();// Note: we set i_view to 1 as default, so it's not needed on anti array('i_view');

		// Permissions for personal albums of other users
		// Note: Do !NOT! hide the i_upload. It's used for the moving-permissions
		$permissions->cats[phpbb_ext_gallery_core_auth::PERSONAL_ALBUM] = array(
			'i'		=> array('i_view', 'i_watermark', 'i_upload', /*'i_approve', 'i_edit', 'i_delete', */'i_report', 'i_rate'),
			'c'		=> array('c_read', 'c_post', 'c_edit', 'c_delete'),
			'm'		=> array('m_comments', 'm_delete', 'm_edit', 'm_move', 'm_report', 'm_status'),
			'misc'	=> array('a_list'/*, 'i_count', 'i_unlimited', 'a_count', 'a_unlimited', 'a_restrict'*/),
		);
		$permissions->p_masks[phpbb_ext_gallery_core_auth::PERSONAL_ALBUM] = array_merge($permissions->cats[phpbb_ext_gallery_core_auth::PERSONAL_ALBUM]['i'], $permissions->cats[phpbb_ext_gallery_core_auth::PERSONAL_ALBUM]['c'], $permissions->cats[phpbb_ext_gallery_core_auth::PERSONAL_ALBUM]['m'], $permissions->cats[phpbb_ext_gallery_core_auth::PERSONAL_ALBUM]['misc']);
		$permissions->p_masks_anti[phpbb_ext_gallery_core_auth::PERSONAL_ALBUM] = array('i_approve', 'i_edit', 'i_delete', 'i_count', 'i_unlimited', 'a_count', 'a_unlimited', 'a_restrict');

		switch ($mode)
		{
			case 'manage':
				switch ($action)
				{
					case 'set':
						$this->permissions_set();
					break;

					case 'v_mask':
						if (!$submit)
						{
							$this->permissions_v_mask();
						}
						else
						{
							$this->permissions_p_mask();
						}
					break;

					default:
						$this->permissions_c_mask();
					break;
				}
			break;

			case 'copy':
				$this->copy_album_permissions();
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}

	function permissions_c_mask()
	{
		global $cache, $template;

		// Send contants to the template
		$template->assign_vars(array(
			'C_OWN_PERSONAL_ALBUMS'	=> phpbb_ext_gallery_core_auth::OWN_ALBUM,
			'C_PERSONAL_ALBUMS'		=> phpbb_ext_gallery_core_auth::PERSONAL_ALBUM,
		));

		$submit = (isset($_POST['submit'])) ? true : false;
		$albums = $cache->obtain_album_list();

		$template->assign_vars(array(
			'U_ACTION'					=> $this->u_action . '&amp;action=v_mask',
			'S_PERMISSION_C_MASK'		=> true,
			'ALBUM_LIST'				=> phpbb_ext_gallery_core_album::get_albumbox(true, '', phpbb_ext_gallery_core_auth::SETTING_PERMISSIONS),
		));
	}

	function permissions_v_mask()
	{
		global $cache, $db, $template, $user, $phpbb_ext_gallery;
		$user->add_lang('acp/permissions');

		$submit = (isset($_POST['submit'])) ? true : false;
		$delete = (isset($_POST['delete'])) ? true : false;
		$album_id = request_var('album_id', array(0));
		$group_id = request_var('group_id', array(0));
		$user_id = request_var('user_id', array(0));
		$p_system = request_var('p_system', 0);

		if (!$p_system && !sizeof($album_id))
		{
			trigger_error('NO_PERMISSIONS_SELECTED', E_USER_WARNING);
		}

		// Delete permissions
		if ($delete)
		{
			// Delete group permissions
			if (!empty($group_id))
			{
				// Get the possible outdated p_masks
				$sql = 'SELECT perm_role_id
					FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE ' . ((!$p_system) ? $db->sql_in_set('perm_album_id', $album_id) : $db->sql_in_set('perm_system', $p_system)) . '
						AND ' . $db->sql_in_set('perm_group_id', $group_id);
				$result = $db->sql_query($sql);

				$outdated_p_masks = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$outdated_p_masks[] = $row['perm_role_id'];
				}
				$db->sql_freeresult($result);

				// Delete the permissions and moderators
				$sql = 'DELETE FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE ' . ((!$p_system) ? $db->sql_in_set('perm_album_id', $album_id) : $db->sql_in_set('perm_system', $p_system)) . '
						AND ' . $db->sql_in_set('perm_group_id', $group_id);
				$db->sql_query($sql);
				if (!$p_system)
				{
					// We do not display the moderators on personals so, just on albums
					$sql = 'DELETE FROM ' . GALLERY_MODSCACHE_TABLE . '
						WHERE ' . $db->sql_in_set('album_id', $album_id) . '
							AND ' . $db->sql_in_set('group_id', $group_id);
					$db->sql_query($sql);
				}

				// Check for further usage
				$sql = 'SELECT perm_role_id
					FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE ' . $db->sql_in_set('perm_role_id', $outdated_p_masks, false, true);
				$result = $db->sql_query($sql);

				$still_used_p_masks = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$still_used_p_masks[] = $row['perm_role_id'];
				}
				$db->sql_freeresult($result);

				// Delete the p_masks, which are no longer used
				$sql = 'DELETE FROM ' . GALLERY_ROLES_TABLE . '
					WHERE ' . $db->sql_in_set('role_id', $outdated_p_masks, false, true) . '
						AND ' . $db->sql_in_set('role_id', $still_used_p_masks, true, true);
				$db->sql_query($sql);
			}

			// Delete user permissions
			if (!empty($user_id))
			{
				// Get the possible outdated p_masks
				$sql = 'SELECT perm_role_id
					FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE ' . ((!$p_system) ? $db->sql_in_set('perm_album_id', $album_id) : $db->sql_in_set('perm_system', $p_system)) . '
						AND ' . $db->sql_in_set('perm_user_id', $user_id);
				$result = $db->sql_query($sql);

				$outdated_p_masks = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$outdated_p_masks[] = $row['perm_role_id'];
				}
				$db->sql_freeresult($result);

				// Delete the permissions and moderators
				$sql = 'DELETE FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE ' . ((!$p_system) ? $db->sql_in_set('perm_album_id', $album_id) : $db->sql_in_set('perm_system', $p_system)) . '
						AND ' . $db->sql_in_set('perm_user_id', $user_id);
				$db->sql_query($sql);
				if (!$p_system)
				{
					// We do not display the moderators on personals so, just on albums
					$sql = 'DELETE FROM ' . GALLERY_MODSCACHE_TABLE . '
						WHERE ' . $db->sql_in_set('album_id', $album_id) . '
							AND ' . $db->sql_in_set('user_id', $user_id);
					$db->sql_query($sql);
				}

				// Check for further usage
				$sql = 'SELECT perm_role_id
					FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE ' . $db->sql_in_set('perm_role_id', $outdated_p_masks, false, true);
				$result = $db->sql_query($sql);

				$still_used_p_masks = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$still_used_p_masks[] = $row['perm_role_id'];
				}
				$db->sql_freeresult($result);

				// Delete the p_masks, which are no longer used
				$sql = 'DELETE FROM ' . GALLERY_ROLES_TABLE . '
					WHERE ' . $db->sql_in_set('role_id', $outdated_p_masks, false, true) . '
						AND ' . $db->sql_in_set('role_id', $still_used_p_masks, true, true);
				$db->sql_query($sql);
			}

			// Only clear if we did something
			if (!empty($group_id) || !empty($user_id))
			{
				$cache->destroy('sql', GALLERY_PERMISSIONS_TABLE);
				$cache->destroy('sql', GALLERY_ROLES_TABLE);
				$cache->destroy('sql', GALLERY_MODSCACHE_TABLE);
				phpbb_ext_gallery_core_auth::set_user_permissions('all', '');
			}
		}

		if (!$p_system)
		{
			// Get the album names of the selected albums
			$sql = 'SELECT album_name
				FROM ' . GALLERY_ALBUMS_TABLE . '
				WHERE ' . $db->sql_in_set('album_id', $album_id, false, true) . '
				ORDER BY left_id';
			$result = $db->sql_query($sql);

			$a_names = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$a_names[] = $row['album_name'];
			}
			$db->sql_freeresult($result);
		}

		// Get the groups for selected album/p_system
		$sql_array = array(
			'SELECT'		=> 'g.group_name, g.group_id, g.group_type',
			'FROM'			=> array(GROUPS_TABLE => 'g'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(GALLERY_PERMISSIONS_TABLE => 'p'),
					'ON'		=> 'p.perm_group_id = g.group_id',
				),
			),

			'WHERE'			=> ((!$p_system) ? $db->sql_in_set('p.perm_album_id', $album_id, false, true) : $db->sql_in_set('p.perm_system', $p_system, false, true)),
			'GROUP_BY'		=> 'g.group_id, g.group_type, g.group_name',
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);

		$set_groups = array();
		$s_defined_group_options = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$set_groups[] = $row['group_id'];
			$s_defined_group_options .= '<option value="' . $row['group_id'] . '">' . (($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name']) . '</option>';
		}
		$db->sql_freeresult($result);

		// Get the other groups, so that the user can add them
		$sql = 'SELECT group_name, group_id, group_type
			FROM ' . GROUPS_TABLE . '
			WHERE ' . $db->sql_in_set('group_id', $set_groups, true, true);
		$result = $db->sql_query($sql);

		$s_add_group_options = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$s_add_group_options .= '<option value="' . $row['group_id'] . '">' . (($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name']) . '</option>';
		}
		$db->sql_freeresult($result);

		// Get the users for selected album/p_system
		$sql_array = array(
			'SELECT'		=> 'u.username, u.user_id',
			'FROM'			=> array(USERS_TABLE => 'u'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(GALLERY_PERMISSIONS_TABLE => 'p'),
					'ON'		=> 'p.perm_user_id = u.user_id',
				),
			),

			'WHERE'			=> ((!$p_system) ? $db->sql_in_set('p.perm_album_id', $album_id, false, true) : $db->sql_in_set('p.perm_system', $p_system, false, true)),
			'GROUP_BY'		=> 'u.user_id, u.username',
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);

		$set_users = array();
		$s_defined_user_options = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$set_users[] = $row['user_id'];
			$s_defined_user_options .= '<option value="' . $row['user_id'] . '">' . $row['username'] . '</option>';
		}
		$db->sql_freeresult($result);

		// Setting permissions screen
		$s_hidden_fields = build_hidden_fields(array(
			'album_id'		=> $album_id,
			'p_system'		=> $p_system,
		));

		$template->assign_vars(array(
			'S_HIDDEN_FIELDS'			=> $s_hidden_fields,
			'U_ACTION'					=> $this->u_action . '&amp;action=v_mask',
			'S_PERMISSION_V_MASK'		=> true,

			'C_MASKS_NAMES'				=> (!$p_system) ? implode(', ', $a_names) : (($p_system == phpbb_ext_gallery_core_auth::OWN_ALBUM) ? $user->lang['OWN_PERSONAL_ALBUMS'] : $user->lang['PERSONAL_ALBUMS']),
			'L_C_MASKS'					=> $user->lang['ALBUMS'],

			'S_CAN_SELECT_GROUP'		=> true,
			'S_DEFINED_GROUP_OPTIONS'	=> $s_defined_group_options,
			'S_ADD_GROUP_OPTIONS'		=> $s_add_group_options,

			'S_CAN_SELECT_USER'			=> true,
			'S_DEFINED_USER_OPTIONS'	=> $s_defined_user_options,
			'U_FIND_USERNAME'			=> $phpbb_ext_gallery->url->append_sid('phpbb', 'memberlist', 'mode=searchuser&amp;form=add_user&amp;field=username&amp;select_single=true'),
			'ANONYMOUS_USER_ID'			=> ANONYMOUS,
		));
	}

	function permissions_p_mask()
	{
		global $cache, $db, $permissions, $template, $user, $phpbb_ext_gallery;
		$user->add_lang('acp/permissions');

		if (!check_form_key('acp_gallery'))
		{
			trigger_error('FORM_INVALID');
		}

		$submit = (isset($_POST['submit'])) ? true : false;
		$delete = (isset($_POST['delete'])) ? true : false;
		$album_id = request_var('album_id', array(0));
		$group_id = request_var('group_id', array(0));
		$user_id = request_var('user_id', array(0));
		$username = request_var('username', array(''), true);
		$usernames = request_var('usernames', '', true);
		$p_system = request_var('p_system', 0);

		// Map usernames to ids and vice versa
		if ($usernames)
		{
			$username = explode("\n", $usernames);
		}
		unset($usernames);

		if (sizeof($username) && !sizeof($user_id))
		{
			if (!function_exists('user_get_id_name'))
			{
				$phpbb_ext_gallery->url->_include('functions_user', 'phpbb');
			}
			user_get_id_name($user_id, $username);

			if (!sizeof($user_id))
			{
				trigger_error($user->lang['SELECTED_USER_NOT_EXIST'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}
		unset($username);

		if (!sizeof($group_id) && !sizeof($user_id))
		{
			trigger_error('NO_VICTIM_SELECTED', E_USER_WARNING);
		}
		elseif (sizeof($group_id))
		{
			$victim_mode = 'group';
			$victim_id = $group_id;
		}
		else
		{
			$victim_mode = 'user';
			$victim_id = $user_id;
		}

		// Create the loops for the javascript
		for ($i = 0; $i < sizeof($permissions->cats[$p_system]); $i++)
		{
			$template->assign_block_vars('c_rows', array());
		}


		if ($victim_mode == 'group')
		{
			// Get the group information
			$sql = 'SELECT group_name, group_id, group_type, group_colour
				FROM ' . GROUPS_TABLE . '
				WHERE ' . $db->sql_in_set('group_id', $victim_id);
			$result = $db->sql_query($sql);

			$victim_list = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$row['group_name'] = (($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name']);
				$victim_row = array(
					'victim_id'		=> $row['group_id'],
					'victim_name'	=> $row['group_name'],
					'victim_colour'	=> $row['group_colour'],
				);
				$victim_list[$row['group_id']] = $victim_row;
			}
			$db->sql_freeresult($result);
		}
		else
		{
			// Get the user information
			$sql = 'SELECT username, user_id, user_colour
				FROM ' . USERS_TABLE . '
				WHERE ' . $db->sql_in_set('user_id', $victim_id);
			$result = $db->sql_query($sql);

			$victim_list = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$victim_row = array(
					'victim_id'		=> $row['user_id'],
					'victim_name'	=> $row['username'],
					'victim_colour'	=> $row['user_colour'],
				);
				$victim_list[$row['user_id']] = $victim_row;
			}
			$db->sql_freeresult($result);
		}

		// Fetch the full-permissions-tree
		$sql = 'SELECT perm_role_id, perm_group_id, perm_user_id, perm_album_id
			FROM ' . GALLERY_PERMISSIONS_TABLE . '
			WHERE ' . ((!$p_system) ? $db->sql_in_set('perm_album_id', $album_id) : $db->sql_in_set('perm_system', $p_system)) . '
				AND ' . $db->sql_in_set('perm_' . $victim_mode . '_id', $victim_id);
		$result = $db->sql_query($sql);

		$p_masks = $fetch_roles = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$fetch_roles[] = $row['perm_role_id'];
			$p_masks[((!$p_system) ? $row['perm_album_id'] : $p_system)][$row['perm_' . $victim_mode . '_id']] = $row['perm_role_id'];
		}
		$db->sql_freeresult($result);

		// Fetch the roles
		$roles = array();
		if (!empty($fetch_roles))
		{
			$sql = 'SELECT *
				FROM ' . GALLERY_ROLES_TABLE . '
				WHERE ' . $db->sql_in_set('role_id', $fetch_roles);
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$roles[$row['role_id']] = $row;
			}
			$db->sql_freeresult($result);
		}

		// Album permissions
		if (!$p_system)
		{
			$album_list = $phpbb_ext_gallery->cache->get('albums');
			foreach ($album_id as $album)
			{
				$album_row = $album_list[$album];
				$template->assign_block_vars('c_mask', array(
					'C_MASK_ID'				=> $album_row['album_id'],
					'C_MASK_NAME'			=> $album_row['album_name'],
					'INHERIT_C_MASKS'		=> $this->inherit_albums($album_list, $album_id, $album_row['album_id']),
				));
				foreach ($victim_id as $victim)
				{
					$victim_row = $victim_list[$victim];
					$template->assign_block_vars('c_mask.v_mask', array(
						'VICTIM_ID'				=> $victim_row['victim_id'],
						'VICTIM_NAME'			=> '<span' . (($victim_row['victim_colour']) ? (' style="color: #' . $victim_row['victim_colour'] . '"') : '') . '>' . $victim_row['victim_name'] . '</span>',
						'INHERIT_VICTIMS'		=> $this->inherit_victims($album_list, $album_id, $victim_list, $album_row['album_id'], $victim_row['victim_id']),
					));
					$role_id = (isset($p_masks[$album_row['album_id']][$victim_row['victim_id']])) ? $p_masks[$album_row['album_id']][$victim_row['victim_id']] : 0;
					foreach ($permissions->cats[$p_system] as $category => $permission_values)
					{
						$acl_s_never = $acl_s_no = $acl_s_yes = 0;
						foreach ($permission_values as $permission)
						{
							if (substr($permission, -6, 6) != '_count')
							{
								if (isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_YES))
								{
									$acl_s_yes++;
								}
								else if (isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_NEVER))
								{
									$acl_s_never++;
								}
								else if (isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_NO))
								{
									$acl_s_no++;
								}
							}
						}
						$template->assign_block_vars('c_mask.v_mask.category', array(
							'CAT_NAME'				=> $user->lang['PERMISSION_' . strtoupper($category)],
							'PERM_GROUP_ID'			=> $category,
							'S_YES'					=> ($acl_s_yes && !$acl_s_never && !$acl_s_no) ? true : false,
							'S_NEVER'				=> ($acl_s_never && !$acl_s_yes && !$acl_s_no) ? true : false,
							'S_NO'					=> ($acl_s_no && !$acl_s_never && !$acl_s_yes) ? true : false,
						));
						foreach ($permission_values as $permission)
						{
							$template->assign_block_vars('c_mask.v_mask.category.mask', array(
								'PERMISSION'			=> $user->lang['PERMISSION_' . strtoupper($permission)],
								'PERMISSION_EXPLAIN'	=> (isset($user->lang['PERMISSION_' . strtoupper($permission) . '_EXPLAIN'])) ? $user->lang['PERMISSION_' . strtoupper($permission) . '_EXPLAIN'] : '',
								'S_FIELD_NAME'			=> 'setting[' . $album_row['album_id'] . '][' . $victim_row['victim_id'] . '][' . $permission . ']',
								'S_NO'					=> ((isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_NO)) ? true : false),
								'S_YES'					=> ((isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_YES)) ? true : false),
								'S_NEVER'				=> ((isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_NEVER)) ? true : false),
								'S_VALUE'				=> ((isset($roles[$role_id][$permission])) ? $roles[$role_id][$permission] : 0),
								'S_COUNT_FIELD'			=> (substr($permission, -6, 6) == '_count') ? true : false,
							));
						}
					}
				}
			}
		}
		else
		{
			$template->assign_block_vars('c_mask', array(
				'C_MASK_ID'				=> $p_system,
				'C_MASK_NAME'			=> (($p_system == phpbb_ext_gallery_core_auth::OWN_ALBUM) ? $user->lang['OWN_PERSONAL_ALBUMS'] : $user->lang['PERSONAL_ALBUMS']),
			));
			foreach ($victim_id as $victim)
			{
				$victim_row = $victim_list[$victim];
				$template->assign_block_vars('c_mask.v_mask', array(
					'VICTIM_ID'				=> $victim_row['victim_id'],
					'VICTIM_NAME'			=> '<span' . (($victim_row['victim_colour']) ? (' style="color: #' . $victim_row['victim_colour'] . '"') : '') . '>' . $victim_row['victim_name'] . '</span>',
					'INHERIT_VICTIMS'		=> $this->p_system_inherit_victims($p_system, $victim_list, $victim_row['victim_id']),
				));
				$role_id = (isset($p_masks[$p_system][$victim_row['victim_id']])) ? $p_masks[$p_system][$victim_row['victim_id']] : 0;
				foreach ($permissions->cats[$p_system] as $category => $permission_values)
				{
					$template->assign_block_vars('c_mask.v_mask.category', array(
						'CAT_NAME'				=> $user->lang['PERMISSION_' . strtoupper($category)],
						'PERM_GROUP_ID'			=> $category,
					));
					foreach ($permission_values as $permission)
					{
						$template->assign_block_vars('c_mask.v_mask.category.mask', array(
							'PERMISSION'			=> $user->lang['PERMISSION_' . strtoupper($permission)],
							'PERMISSION_EXPLAIN'	=> (isset($user->lang['PERMISSION_' . strtoupper($permission) . '_EXPLAIN'])) ? $user->lang['PERMISSION_' . strtoupper($permission) . '_EXPLAIN'] : '',
							'S_FIELD_NAME'			=> 'setting[' . $p_system . '][' . $victim_row['victim_id'] . '][' . $permission . ']',
							'S_NO'					=> ((isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_NO)) ? true : false),
							'S_YES'					=> ((isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_YES)) ? true : false),
							'S_NEVER'				=> ((isset($roles[$role_id][$permission]) && ($roles[$role_id][$permission] == phpbb_ext_gallery_core_auth::ACL_NEVER)) ? true : false),
							'S_VALUE'				=> ((isset($roles[$role_id][$permission])) ? $roles[$role_id][$permission] : 0),
							'S_COUNT_FIELD'			=> (substr($permission, -6, 6) == '_count') ? true : false,
						));
					}
				}
			}
		}

		// Setting permissions screen
		$s_hidden_fields = build_hidden_fields(array(
			'user_id'		=> $user_id,
			'group_id'		=> $group_id,
			'album_id'		=> $album_id,
			'p_system'		=> $p_system,
		));

		$template->assign_vars(array(
			'S_HIDDEN_FIELDS'			=> $s_hidden_fields,
			'U_ACTION'					=> $this->u_action . '&amp;action=set',
			'S_PERMISSION_P_MASK'		=> true,
		));
	}

	function permissions_set()
	{
		global $cache, $db, $permissions, $template, $user;

		// Send contants to the template
		$submit = (isset($_POST['submit'])) ? true : false;
		$album_id = request_var('album_id', array(0));
		$group_id = request_var('group_id', array(0));
		$user_id = request_var('user_id', array(0));
		$p_system = request_var('p_system', phpbb_ext_gallery_core_auth::PUBLIC_ALBUM);

		if (!sizeof($group_id) && !sizeof($user_id))
		{
			trigger_error('NO_VICTIM_SELECTED', E_USER_WARNING);
		}
		elseif (sizeof($group_id))
		{
			$victim_mode = 'group';
			$victim_id = $group_id;
		}
		else
		{
			$victim_mode = 'user';
			$victim_id = $user_id;
		}

		if ($submit)
		{
			if (!check_form_key('acp_gallery'))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}
			$coal = $cache->obtain_album_list();

			/**
			* Grab the permissions
			*
			* includes/acp/acp_permissions.php says:
			* // We obtain and check $_POST['setting'][$ug_id][$forum_id] directly and not using request_var() because request_var()
			* // currently does not support the amount of dimensions required. ;)
			*/
			//		$auth_settings = request_var('setting', array(0 => array(0 => array('' => 0))));
			$p_mask_count = 0;
			$auth_settings = $p_mask_storage = $c_mask_storage = $v_mask_storage = array();
			foreach ($_POST['setting'] as $c_mask => $v_sets)
			{
				$c_mask = (int) $c_mask;
				$c_mask_storage[] = $c_mask;
				$auth_settings[$c_mask] = array();
				foreach ($v_sets as $v_mask => $p_sets)
				{
					$v_mask = (int) $v_mask;
					$v_mask_storage[] = $v_mask;
					$auth_settings[$c_mask][$v_mask] = array();
					$is_moderator = false;
					foreach ($p_sets as $p_mask => $value)
					{
						if (!in_array($p_mask, $permissions->p_masks[$p_system]))
						{
							// An admin tried to set a non-existing permission. Hacking attempt?!
							trigger_error('HACKING_ATTEMPT', E_USER_WARNING);
						}
						// Casted all values to integer and checked all strings whether they are permissions!
						// Should be fine than for the .com MOD-Team now =)
						$value = (int) $value;
						if (substr($p_mask, -6, 6) == '_count')
						{
							$auth_settings[$c_mask][$v_mask][$p_mask] = $value;
						}
						else
						{
							$auth_settings[$c_mask][$v_mask][$p_mask] = ($value == ACL_YES) ? phpbb_ext_gallery_core_auth::ACL_YES : (($value == ACL_NEVER) ? phpbb_ext_gallery_core_auth::ACL_NEVER : phpbb_ext_gallery_core_auth::ACL_NO);
							// Do we have moderators?
							if ((substr($p_mask, 0, 2) == 'm_') && ($value == ACL_YES))
							{
								$is_moderator = true;
							}
						}
					}
					// Need to set a defaults here: view your own personal album images
					if ($p_system == phpbb_ext_gallery_core_auth::OWN_ALBUM)
					{
						$auth_settings[$c_mask][$v_mask]['i_view'] = phpbb_ext_gallery_core_auth::ACL_YES;
					}

					$p_mask_storage[$p_mask_count]['p_mask'] = $auth_settings[$c_mask][$v_mask];
					$p_mask_storage[$p_mask_count]['is_moderator'] = $is_moderator;
					$p_mask_storage[$p_mask_count]['usage'][] = array('c_mask' => $c_mask, 'v_mask' => $v_mask);
					$auth_settings[$c_mask][$v_mask] = $p_mask_count;
					$p_mask_count++;
				}
			}
			/**
			* Inherit the permissions
			*/
			foreach ($_POST['inherit'] as $c_mask => $v_sets)
			{
				$c_mask = (int) $c_mask;
				foreach ($v_sets as $v_mask => $i_mask)
				{
					if (($v_mask == 'full') && $i_mask)
					{
						$i_mask = (int) $i_mask;
						// Inherit all permissions of an other c_mask
						if (isset($auth_settings[$i_mask]))
						{
							if ($this->inherit_albums($coal, $c_mask_storage, $c_mask, $i_mask))
							{
								foreach ($auth_settings[$c_mask] as $v_mask => $p_mask)
								{
									// You are not able to inherit a later c_mask, so we can remove the p_mask from the storage,
									// and just use the same p_mask
									unset($p_mask_storage[$auth_settings[$c_mask][$v_mask]]);
									$auth_settings[$c_mask][$v_mask] = $auth_settings[$i_mask][$v_mask];
									$p_mask_storage[$auth_settings[$c_mask][$v_mask]]['usage'][] = array('c_mask' => $c_mask, 'v_mask' => $v_mask);
								}
								// We take all permissions of another c_mask, so:
								break;
							}
							else
							{
								// The choosen option was disabled: Hacking attempt?!
								trigger_error('HACKING_ATTEMPT', E_USER_WARNING);
							}
						}
					}
					elseif ($i_mask)
					{
						// Inherit permissions of one [c_mask][v_mask]
						$v_mask = (int) $v_mask;
						list($ci_mask, $vi_mask) = explode("_", $i_mask);
						$ci_mask = (int) $ci_mask;
						$vi_mask = (int) $vi_mask;
						if (isset($auth_settings[$ci_mask][$vi_mask]))
						{
							$no_hacking_attempt = ((!$p_system) ? $this->inherit_victims($coal, $c_mask_storage, $v_mask_storage, $c_mask, $v_mask, $ci_mask, $vi_mask) : $this->p_system_inherit_victims($p_system, $v_mask_storage, $v_mask, $vi_mask));
							if ($no_hacking_attempt)
							{
								// You are not able to inherit a later c_mask, so we can remove the p_mask from the storage,
								// and just use the same p_mask
								if (isset($auth_settings[$c_mask][$v_mask]))
								{
									// Should exist, but didn't on testing so only do it, when it does exist
									unset($p_mask_storage[$auth_settings[$c_mask][$v_mask]]);
								}
								$auth_settings[$c_mask][$v_mask] = $auth_settings[$ci_mask][$vi_mask];
								$p_mask_storage[$auth_settings[$c_mask][$v_mask]]['usage'][] = array('c_mask' => $c_mask, 'v_mask' => $v_mask);
							}
							else
							{
								// The choosen option was disabled: Hacking attempt?!
								trigger_error('HACKING_ATTEMPT', E_USER_WARNING);
							}
						}
					}
				}
			}
			unset($auth_settings);

			// Get the possible outdated p_masks
			$sql = 'SELECT perm_role_id
				FROM ' . GALLERY_PERMISSIONS_TABLE . '
				WHERE ' . ((!$p_system) ? $db->sql_in_set('perm_album_id', $album_id) : $db->sql_in_set('perm_system', $p_system)) . '
					AND ' . $db->sql_in_set('perm_' . $victim_mode . '_id', $v_mask_storage);
			$result = $db->sql_query($sql);

			$outdated_p_masks = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$outdated_p_masks[] = $row['perm_role_id'];
			}
			$db->sql_freeresult($result);

			// Delete the permissions and moderators
			$sql = 'DELETE FROM ' . GALLERY_PERMISSIONS_TABLE . '
				WHERE ' . ((!$p_system) ? $db->sql_in_set('perm_album_id', $album_id) : $db->sql_in_set('perm_system', $p_system)) . '
					AND ' . $db->sql_in_set('perm_' . $victim_mode . '_id', $v_mask_storage);
			$db->sql_query($sql);
			if (!$p_system)
			{
				$sql = 'DELETE FROM ' . GALLERY_MODSCACHE_TABLE . '
					WHERE ' . $db->sql_in_set('album_id', $c_mask_storage) . '
						AND ' . $db->sql_in_set($victim_mode . '_id', $v_mask_storage);
				$db->sql_query($sql);
			}

			// Check for further usage
			$sql = 'SELECT perm_role_id
				FROM ' . GALLERY_PERMISSIONS_TABLE . '
				WHERE ' . $db->sql_in_set('perm_role_id', $outdated_p_masks, false, true);
			$result = $db->sql_query($sql);

			$still_used_p_masks = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$still_used_p_masks[] = $row['perm_role_id'];
			}
			$db->sql_freeresult($result);

			// Delete the p_masks, which are no longer used
			$sql = 'DELETE FROM ' . GALLERY_ROLES_TABLE . '
				WHERE ' . $db->sql_in_set('role_id', $outdated_p_masks, false, true) . '
					AND ' . $db->sql_in_set('role_id', $still_used_p_masks, true, true);
			$db->sql_query($sql);

			$group_names = array();
			if (!$p_system)
			{
				if ($victim_mode == 'group')
				{
					// Get group_name's for the GALLERY_MODSCACHE_TABLE
					$sql = 'SELECT group_id, group_name
						FROM ' . GROUPS_TABLE . '
						WHERE ' . $db->sql_in_set('group_id', $v_mask_storage);
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$victim_names[$row['group_id']] = $row['group_name'];
					}
					$db->sql_freeresult($result);
				}
				else
				{
					// Get username's for the GALLERY_MODSCACHE_TABLE
					$sql = 'SELECT user_id, username
						FROM ' . USERS_TABLE . '
						WHERE ' . $db->sql_in_set('user_id', $v_mask_storage);
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$victim_names[$row['user_id']] = $row['username'];
					}
					$db->sql_freeresult($result);
				}
			}

			$sql_permissions = $sql_moderators = array();
			foreach ($p_mask_storage as $p_set)
			{
				// Check whether the p_mask is already in the DB
				$sql_where = '';
				foreach ($p_set['p_mask'] as $p_mask => $value)
				{
					$sql_where .= (($sql_where) ? ' AND ' : '') . $p_mask . ' = ' . $value;
				}
				// Check back, so we dont give more permissions than the admin wants to
				$check_permissions_to_default = array_diff($permissions->p_masks_anti[$p_system], $p_set['p_mask']);
				foreach ($check_permissions_to_default as $p_mask)
				{
					$sql_where .= (($sql_where) ? ' AND ' : '') . $p_mask . ' = 0';
				}

				$role_id = 0;
				$sql = 'SELECT role_id
					FROM ' . GALLERY_ROLES_TABLE . "
					WHERE $sql_where";
				$result = $db->sql_query_limit($sql, 1);
				$role_id = (int) $db->sql_fetchfield('role_id');
				$db->sql_freeresult($result);

				if (!$role_id)
				{
					// Note: Do not collect the roles to insert, to deny doubles and we need the ID!
					$sql = 'INSERT INTO ' . GALLERY_ROLES_TABLE . ' ' . $db->sql_build_array('INSERT', $p_set['p_mask']);
					$db->sql_query($sql);
					$role_id = $db->sql_nextid();
				}

				foreach ($p_set['usage'] as $usage)
				{
					if (!$p_system)
					{
						$sql_permissions[] = array(
							'perm_role_id'					=> $role_id,
							'perm_album_id'					=> $usage['c_mask'],
							'perm_' . $victim_mode . '_id'	=> $usage['v_mask'],
						);
						if ($p_set['is_moderator'])
						{
							if ($victim_mode == 'group')
							{
								$sql_moderators[] = array(
									'album_id'		=> $usage['c_mask'],
									'group_id'		=> $usage['v_mask'],
									'group_name'	=> $victim_names[$usage['v_mask']],
								);
							}
							else
							{
								$sql_moderators[] = array(
									'album_id'		=> $usage['c_mask'],
									'user_id'		=> $usage['v_mask'],
									'username'		=> $victim_names[$usage['v_mask']],
								);
							}
						}
					}
					else
					{
						$sql_permissions[] = array(
							'perm_role_id'					=> $role_id,
							'perm_system'					=> $usage['c_mask'],
							'perm_' . $victim_mode . '_id'	=> $usage['v_mask'],
						);
					}
				}
			}
			$db->sql_multi_insert(GALLERY_PERMISSIONS_TABLE, $sql_permissions);
			$db->sql_multi_insert(GALLERY_MODSCACHE_TABLE, $sql_moderators);

			$cache->destroy('sql', GALLERY_PERMISSIONS_TABLE);
			$cache->destroy('sql', GALLERY_ROLES_TABLE);
			$cache->destroy('sql', GALLERY_MODSCACHE_TABLE);
			phpbb_ext_gallery_core_auth::set_user_permissions('all', '');

			trigger_error($user->lang['PERMISSIONS_STORED'] . adm_back_link($this->u_action));
		}
		trigger_error('HACKING_ATTEMPT', E_USER_WARNING);
	}

	/**
	* Handles copying permissions from one album to others
	*/
	function copy_album_permissions()
	{
		global $cache, $db, $template, $user;

		$submit = isset($_POST['submit']) ? true : false;

		if ($submit)
		{
			$src = request_var('src_album_id', 0);
			$dest = request_var('dest_album_ids', array(0));

			$sql = 'SELECT album_id
				FROM ' . GALLERY_ALBUMS_TABLE . '
				WHERE album_id = ' . $src;
			$result = $db->sql_query($sql);
			$src = (int) $db->sql_fetchfield('album_id');
			$db->sql_freeresult($result);

			if (!$src)
			{
				trigger_error($user->lang['SELECTED_ALBUM_NOT_EXIST'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			if (!sizeof($dest))
			{
				trigger_error($user->lang['SELECTED_ALBUM_NOT_EXIST'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			if (confirm_box(true))
			{
				$sql = 'SELECT *
					FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE perm_album_id = ' . $src;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					foreach ($dest as $album_id)
					{
						$perm_data[] = array(
							'perm_role_id'					=> $row['perm_role_id'],
							'perm_album_id'					=> $album_id,
							'perm_user_id'					=> $row['perm_user_id'],
							'perm_group_id'					=> $row['perm_group_id'],
							'perm_system'					=> $row['perm_system'],
						);
					}
				}
				$db->sql_freeresult($result);

				$modscache_ary = array();
				$sql = 'SELECT * FROM ' . GALLERY_MODSCACHE_TABLE . '
					WHERE album_id = ' . $src;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					foreach ($dest as $album_id)
					{
						$modscache_ary[] = array(
							'album_id'			=> $album_id,
							'user_id'			=> $row['user_id'],
							'username'			=> $row['username'],
							'group_id'			=> $row['group_id'],
							'group_name'		=> $row['group_name'],
							'display_on_index'	=> $row['display_on_index'],
						);
					}
				}
				$db->sql_freeresult($result);

				$sql = 'DELETE FROM ' . GALLERY_PERMISSIONS_TABLE . '
					WHERE ' . $db->sql_in_set('perm_album_id', $dest);
				$db->sql_query($sql);

				$sql = 'DELETE FROM ' . GALLERY_MODSCACHE_TABLE . '
					WHERE ' . $db->sql_in_set('album_id', $dest);
				$db->sql_query($sql);

				$db->sql_multi_insert(GALLERY_PERMISSIONS_TABLE, $perm_data);
				$db->sql_multi_insert(GALLERY_MODSCACHE_TABLE, $modscache_ary);

				$cache->destroy('sql', GALLERY_MODSCACHE_TABLE);
				$cache->destroy('sql', GALLERY_PERMISSIONS_TABLE);
				phpbb_ext_gallery_core_auth::set_user_permissions('all', '');

				trigger_error($user->lang['COPY_PERMISSIONS_SUCCESSFUL'] . adm_back_link($this->u_action));
			}
			else
			{
				$s_hidden_fields = array(
					'submit'			=> $submit,
					'src_album_id'		=> $src,
					'dest_album_ids'	=> $dest,
				);

				$s_hidden_fields = build_hidden_fields($s_hidden_fields);

				confirm_box(false, $user->lang['COPY_PERMISSIONS_CONFIRM'], $s_hidden_fields);
			}
		}

		$template->assign_vars(array(
			'S_ALBUM_OPTIONS'		=> phpbb_ext_gallery_core_album::get_albumbox(true, ''),
			'S_COPY_PERMISSIONS'	=> true,
		));
	}

	/**
	* Create the drop-down-options to inherit the c_masks
	* or check, whether the choosen option is valid
	*/
	function inherit_albums($cache_obtain_album_list, $allowed_albums, $album_id, $check_inherit_album = 0)
	{
		global $user;
		$disabled = false;

		$return = '';
		$return .= '<option value="0" selected="selected">' . $user->lang['NO_INHERIT'] . '</option>';
		foreach ($cache_obtain_album_list as $album)
		{
			if (in_array($album['album_id'], $allowed_albums))
			{
				// We found the requested album: return true!
				if ($check_inherit_album && ($album['album_id'] == $check_inherit_album))
				{
					return true;
				}
				if ($album['album_id'] == $album_id)
				{
					$disabled = true;
					// Could we find the requested album so far? No? Hacking attempt?!
					if ($check_inherit_album)
					{
						return false;
					}
				}
				$return .= '<option value="' . $album['album_id'] . '"';
				if ($disabled)
				{
					$return .= ' disabled="disabled" class="disabled-option"';
				}
				$return .= '>' . $album['album_name'] . '</option>';
			}
		}
		// Could we not find the requested album even here?
		if ($check_inherit_album)
		{
			// Something went really wrong here!
			return false;
		}
		return $return;
	}

	/**
	* Create the drop-down-options to inherit the v_masks
	* or check, whether the choosen option is valid
	*/
	function inherit_victims($cache_obtain_album_list, $allowed_albums, $allowed_victims, $album_id, $victim_id, $check_inherit_album = 0, $check_inherit_victim = 0)
	{
		global $user;

		$disabled = false;
		// We submit a "wrong" array on the check (to make it more easy) so we convert it here
		if ($check_inherit_album && $check_inherit_victim)
		{
			$converted_victims = array();
			foreach ($allowed_victims as $victim)
			{
				$converted_victims[] = array(
					'victim_id'		=> $victim,
					'victim_name'	=> '',
				);
			}
			$allowed_victims = $converted_victims;
			unset ($converted_victims);
		}

		$return = '';
		$return .= '<option value="0" selected="selected">' . $user->lang['NO_INHERIT'] . '</option>';
		foreach ($cache_obtain_album_list as $album)
		{
			if (in_array($album['album_id'], $allowed_albums))
			{
				$return .= '<option value="0" disabled="disabled" class="disabled-option">' . $album['album_name'] . '</option>';
				foreach ($allowed_victims as $victim)
				{
					// We found the requested album_group: return true!
					if ($check_inherit_album && $check_inherit_victim && (($album['album_id'] == $check_inherit_album) && ($victim['victim_id'] == $check_inherit_victim)))
					{
						return true;
					}
					if (($album['album_id'] == $album_id) && ($victim['victim_id'] == $victim_id))
					{
						$disabled = true;
						// Could we find the requested album_victim so far? No? Hacking attempt?!
						if ($check_inherit_album && $check_inherit_victim)
						{
							return false;
						}
					}
					$return .= '<option value="' . $album['album_id'] . '_' . $victim['victim_id'] . '"';
					if ($disabled)
					{
						$return .= ' disabled="disabled" class="disabled-option"';
					}
					$return .= '>&nbsp;&nbsp;&nbsp;' . $album['album_name'] . ' >>> ' . $victim['victim_name'] . '</option>';
				}
			}
		}
		// Could we not find the requested album_victim even here?
		if ($check_inherit_album && $check_inherit_victim)
		{
			// Something went really wrong here!
			return false;
		}
		return $return;
	}

	/**
	* Create the drop-down-options to inherit the v_masks
	* or check, whether the choosen option is valid
	*/
	function p_system_inherit_victims($p_system, $allowed_victims, $victim_id, $check_inherit_victim = 0)
	{
		global $user;

		$disabled = false;
		// We submit a "wrong" array on the check (to make it more easy) so we convert it here
		if ($check_inherit_victim)
		{
			$converted_groups = array();
			foreach ($allowed_victims as $victim)
			{
				$converted_victims[] = array(
					'victim_id'		=> $victim,
					'victim_name'	=> '',
				);
			}
			$allowed_victims = $converted_victims;
			unset ($converted_victims);
		}

		$return = '';
		$return .= '<option value="0" selected="selected">' . $user->lang['NO_INHERIT'] . '</option>';
		foreach ($allowed_victims as $victim)
		{
			// We found the requested {$p_system}_victim: return true!
			if ($check_inherit_victim && ($victim['victim_id'] == $check_inherit_victim))
			{
				return true;
			}
			if ($victim['victim_id'] == $victim_id)
			{
				$disabled = true;
				// Could we find the requested {$p_system}_victim so far? No? Hacking attempt?!
				if ($check_inherit_victim)
				{
					return false;
				}
			}
			$return .= '<option value="' . $p_system . '_' . $victim['victim_id'] . '"';
			if ($disabled)
			{
				$return .= ' disabled="disabled" class="disabled-option"';
			}
			$return .= '>&nbsp;&nbsp;&nbsp;' . (($p_system == phpbb_ext_gallery_core_auth::OWN_ALBUM) ? $user->lang['OWN_PERSONAL_ALBUMS'] : $user->lang['PERSONAL_ALBUMS']) . ' >>> ' . $victim['victim_name'] . '</option>';
		}
		// Could we not find the requested {$p_system}_victim even here?
		if ($check_inherit_victim)
		{
			// Something went really wrong here!
			return false;
		}
		return $return;
	}
}
