<?php

/**
* Rating
*/
if ($phpbb_ext_gallery->config->get('allow_rates'))
{
	$rating = new phpbb_ext_gallery_core_rating($image_id, $image_data, $album_data);

	$user_rating = $rating->get_user_rating($user->data['user_id']);

	// Check: User didn't rate yet, has permissions, it's not the users own image and the user is logged in
	if (!$user_rating && $rating->is_allowed())
	{
		$rating->display_box();
	}
	$template->assign_vars(array(
		'IMAGE_RATING'			=> $rating->get_image_rating($user_rating),
		'S_ALLOWED_TO_RATE'		=> (!$user_rating && $rating->is_allowed()),
		'S_VIEW_RATE'			=> ($phpbb_ext_gallery->auth->acl_check('i_rate', $album_id, $album_data['album_user_id'])) ? true : false,
		'S_COMMENT_ACTION'		=> $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=rate"),
	));
	unset($rating);
}

/**
* Posting comment
*/
$comments_disabled = (!$phpbb_ext_gallery->config->get('allow_comments') || ($phpbb_ext_gallery->config->get('comment_user_control') && !$image_data['image_allow_comments']));
if (!$comments_disabled && $phpbb_ext_gallery->auth->acl_check('c_post', $album_id, $album_data['album_user_id']) && ($album_data['album_status'] != ITEM_LOCKED) && (($image_data['image_status'] != phpbb_ext_gallery_core_image::STATUS_LOCKED) || $phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id'])))
{
	$user->add_lang('posting');
	$phpbb_ext_gallery->url->_include('functions_posting', 'phpbb');

	$bbcode_status	= ($config['allow_bbcode']) ? true : false;
	$smilies_status	= ($config['allow_smilies']) ? true : false;
	$img_status		= ($bbcode_status) ? true : false;
	$url_status		= ($config['allow_post_links']) ? true : false;
	$flash_status	= false;
	$quote_status	= true;

	// Build custom bbcodes array
	display_custom_bbcodes();

	// Build smilies array
	generate_smilies('inline', 0);

	$s_hide_comment_input = (time() < ($album_data['contest_start'] + $album_data['contest_end'])) ? true : false;

	$template->assign_vars(array(
		'S_ALLOWED_TO_COMMENT'	=> true,
		'S_HIDE_COMMENT_INPUT'	=> $s_hide_comment_input,
		'CONTEST_COMMENTS'		=> sprintf($user->lang['CONTEST_COMMENTS_STARTS'], $user->format_date(($album_data['contest_start'] + $album_data['contest_end']), false, true)),

		'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($user->lang['BBCODE_IS_ON'], '<a href="' . $phpbb_ext_gallery->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>') : sprintf($user->lang['BBCODE_IS_OFF'], '<a href="' . $phpbb_ext_gallery->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>'),
		'IMG_STATUS'			=> ($img_status) ? $user->lang['IMAGES_ARE_ON'] : $user->lang['IMAGES_ARE_OFF'],
		'FLASH_STATUS'			=> ($flash_status) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],
		'SMILIES_STATUS'		=> ($smilies_status) ? $user->lang['SMILIES_ARE_ON'] : $user->lang['SMILIES_ARE_OFF'],
		'URL_STATUS'			=> ($bbcode_status && $url_status) ? $user->lang['URL_IS_ON'] : $user->lang['URL_IS_OFF'],
		'S_SIGNATURE_CHECKED'	=> ($user->optionget('attachsig')) ? ' checked="checked"' : '',

		'S_BBCODE_ALLOWED'		=> $bbcode_status,
		'S_SMILIES_ALLOWED'		=> $smilies_status,
		'S_LINKS_ALLOWED'		=> $url_status,
		'S_BBCODE_IMG'			=> $img_status,
		'S_BBCODE_URL'			=> $url_status,
		'S_BBCODE_FLASH'		=> $flash_status,
		'S_BBCODE_QUOTE'		=> $quote_status,
		'L_COMMENT_LENGTH'		=> sprintf($user->lang['COMMENT_LENGTH'], $phpbb_ext_gallery->config->get('comment_length')),
	));

	if (false)//@todo: phpbb_gallery_misc::display_captcha('comment'))
	{
		// Get the captcha instance
		$phpbb_ext_gallery->url->_include('captcha/captcha_factory', 'phpbb');
		$captcha =& phpbb_captcha_factory::get_instance($config['captcha_plugin']);
		$captcha->init(CONFIRM_POST);

		$template->assign_vars(array(
			'S_CONFIRM_CODE'		=> true,
			'CAPTCHA_TEMPLATE'		=> $captcha->get_template(),
		));
	}

	// Different link, when we rate and dont comment
	if (!$s_hide_comment_input)
	{
		$template->assign_var('S_COMMENT_ACTION', $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=add"));
	}
}
elseif ($phpbb_ext_gallery->config->get('comment_user_control') && !$image_data['image_allow_comments'])
{
	$template->assign_var('S_COMMENTS_DISABLED', true);
}

/**
* Listing comment
*/
if (($phpbb_ext_gallery->config->get('allow_comments') && $phpbb_ext_gallery->auth->acl_check('c_read', $album_id, $album_data['album_user_id'])) && (time() > ($album_data['contest_start'] + $album_data['contest_end'])))
{
	$start = request_var('start', 0);
	$sort_order = (request_var('sort_order', 'ASC') == 'ASC') ? 'ASC' : 'DESC';
	$template->assign_vars(array(
		'S_ALLOWED_READ_COMMENTS'	=> true,
		'IMAGE_COMMENTS'			=> $image_data['image_comments'],
		'SORT_ASC'					=> ($sort_order == 'ASC') ? true : false,
	));

	if ($image_data['image_comments'] > 0)
	{
		if (!class_exists('bbcode'))
		{
			$phpbb_ext_gallery->url->_include('bbcode', 'phpbb');
		}
		$bbcode = new bbcode();

		$comments = $users = $user_cache = array();
		$users[] = $image_data['image_user_id'];
		$sql = 'SELECT *
			FROM ' . GALLERY_COMMENTS_TABLE . '
			WHERE comment_image_id = ' . $image_id . '
			ORDER BY comment_id ' . $sort_order;
		$result = $db->sql_query_limit($sql, $config['posts_per_page'], $start);

		while ($row = $db->sql_fetchrow($result))
		{
			$comments[] = $row;
			$users[] = $row['comment_user_id'];
			if ($row['comment_edit_count'] > 0)
			{
				$users[] = $row['comment_edit_user_id'];
			}
		}
		$db->sql_freeresult($result);

		$users = array_unique($users);
		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> 'u.*, gu.personal_album_id, gu.user_images',
			'FROM'		=> array(USERS_TABLE => 'u'),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GALLERY_USERS_TABLE => 'gu'),
					'ON'	=> 'gu.user_id = u.user_id'
				),
			),

			'WHERE'		=> $db->sql_in_set('u.user_id', $users),
		));
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			phpbb_ext_gallery_core_user::add_user_to_cache($user_cache, $row);
		}
		$db->sql_freeresult($result);

		if ($config['load_onlinetrack'] && sizeof($users))
		{
			// Load online-information
			$sql = 'SELECT session_user_id, MAX(session_time) as online_time, MIN(session_viewonline) AS viewonline
				FROM ' . SESSIONS_TABLE . '
				WHERE ' . $db->sql_in_set('session_user_id', $users) . '
				GROUP BY session_user_id';
			$result = $db->sql_query($sql);

			$update_time = $config['load_online_time'] * 60;
			while ($row = $db->sql_fetchrow($result))
			{
				$user_cache[$row['session_user_id']]['online'] = (time() - $update_time < $row['online_time'] && (($row['viewonline']) || $auth->acl_get('u_viewonline'))) ? true : false;
			}
			$db->sql_freeresult($result);
		}

		foreach ($comments as $row)
		{
			$edit_info = '';
			if ($row['comment_edit_count'] > 0)
			{
				$edit_info = ($row['comment_edit_count'] == 1) ? $user->lang['EDITED_TIME_TOTAL'] : $user->lang['EDITED_TIMES_TOTAL'];
				$edit_info = sprintf($edit_info, get_username_string('full', $user_cache[$row['comment_edit_user_id']]['user_id'], $user_cache[$row['comment_edit_user_id']]['username'], $user_cache[$row['comment_edit_user_id']]['user_colour']), $user->format_date($row['comment_edit_time'], false, true), $row['comment_edit_count']);
			}

			$user_id = $row['comment_user_id'];
			if ($user_cache[$user_id]['sig'] && empty($user_cache[$user_id]['sig_parsed']))
			{
				$user_cache[$user_id]['sig'] = censor_text($user_cache[$user_id]['sig']);

				if ($user_cache[$user_id]['sig_bbcode_bitfield'])
				{
					$bbcode->bbcode_second_pass($user_cache[$user_id]['sig'], $user_cache[$user_id]['sig_bbcode_uid'], $user_cache[$user_id]['sig_bbcode_bitfield']);
				}

				$user_cache[$user_id]['sig'] = bbcode_nl2br($user_cache[$user_id]['sig']);
				$user_cache[$user_id]['sig'] = smiley_text($user_cache[$user_id]['sig']);
				$user_cache[$user_id]['sig_parsed'] = true;
			}

			$template->assign_block_vars('commentrow', array(
				'U_COMMENT'		=> $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id&amp;start=$start&amp;sort_order=$sort_order") . '#comment_' . $row['comment_id'],
				'COMMENT_ID'	=> $row['comment_id'],
				'TIME'			=> $user->format_date($row['comment_time']),
				'TEXT'			=> generate_text_for_display($row['comment'], $row['comment_uid'], $row['comment_bitfield'], 7),
				'EDIT_INFO'		=> $edit_info,
				'U_DELETE'		=> ($phpbb_ext_gallery->auth->acl_check('m_comments', $album_id, $album_data['album_user_id']) || ($phpbb_ext_gallery->auth->acl_check('c_delete', $album_id, $album_data['album_user_id']) && ($row['comment_user_id'] == $user->data['user_id']) && $user->data['is_registered'])) ? $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=delete&amp;comment_id=" . $row['comment_id']) : '',
				'U_QUOTE'		=> ($phpbb_ext_gallery->auth->acl_check('c_post', $album_id, $album_data['album_user_id'])) ? $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=add&amp;comment_id=" . $row['comment_id']) : '',
				'U_EDIT'		=> ($phpbb_ext_gallery->auth->acl_check('m_comments', $album_id, $album_data['album_user_id']) || ($phpbb_ext_gallery->auth->acl_check('c_edit', $album_id, $album_data['album_user_id']) && ($row['comment_user_id'] == $user->data['user_id']) && $user->data['is_registered'])) ? $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=edit&amp;comment_id=" . $row['comment_id']) : '',
				'U_INFO'		=> ($auth->acl_get('a_')) ? $phpbb_ext_gallery->url->append_sid('mcp', 'mode=whois&amp;ip=' . $row['comment_user_ip']) : '',

				'POST_AUTHOR_FULL'		=> get_username_string('full', $user_id, $row['comment_username'], $user_cache[$user_id]['user_colour']),
				'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $user_id, $row['comment_username'], $user_cache[$user_id]['user_colour']),
				'POST_AUTHOR'			=> get_username_string('username', $user_id, $row['comment_username'], $user_cache[$user_id]['user_colour']),
				'U_POST_AUTHOR'			=> get_username_string('profile', $user_id, $row['comment_username'], $user_cache[$user_id]['user_colour']),

				'SIGNATURE'			=> ($row['comment_signature']) ? $user_cache[$user_id]['sig'] : '',
				'RANK_TITLE'		=> $user_cache[$user_id]['rank_title'],
				'RANK_IMG'			=> $user_cache[$user_id]['rank_image'],
				'RANK_IMG_SRC'		=> $user_cache[$user_id]['rank_image_src'],
				'POSTER_JOINED'		=> $user_cache[$user_id]['joined'],
				'POSTER_POSTS'		=> $user_cache[$user_id]['posts'],
				'POSTER_FROM'		=> $user_cache[$user_id]['from'],
				'POSTER_AVATAR'		=> $user_cache[$user_id]['avatar'],
				'POSTER_WARNINGS'	=> $user_cache[$user_id]['warnings'],
				'POSTER_AGE'		=> $user_cache[$user_id]['age'],

				'ICQ_STATUS_IMG'	=> $user_cache[$user_id]['icq_status_img'],
				'ONLINE_IMG'		=> ($user_id == ANONYMOUS || !$config['load_onlinetrack']) ? '' : (($user_cache[$user_id]['online']) ? $user->img('icon_user_online', 'ONLINE') : $user->img('icon_user_offline', 'OFFLINE')),
				'S_ONLINE'			=> ($user_id == ANONYMOUS || !$config['load_onlinetrack']) ? false : (($user_cache[$user_id]['online']) ? true : false),

				'U_PROFILE'		=> $user_cache[$user_id]['profile'],
				'U_SEARCH'		=> $user_cache[$user_id]['search'],
				'U_PM'			=> ($user_id != ANONYMOUS && $config['allow_privmsg'] && $auth->acl_get('u_sendpm') && ($user_cache[$user_id]['allow_pm'] || $auth->acl_gets('a_', 'm_'))) ? $phpbb_ext_gallery->url->append_sid('phpbb', 'ucp', 'i=pm&amp;mode=compose&amp;u=' . $user_id) : '',
				'U_EMAIL'		=> $user_cache[$user_id]['email'],
				'U_WWW'			=> $user_cache[$user_id]['www'],
				'U_ICQ'			=> $user_cache[$user_id]['icq'],
				'U_AIM'			=> $user_cache[$user_id]['aim'],
				'U_MSN'			=> $user_cache[$user_id]['msn'],
				'U_YIM'			=> $user_cache[$user_id]['yim'],
				'U_JABBER'		=> $user_cache[$user_id]['jabber'],

				'U_GALLERY'			=> $user_cache[$user_id]['gallery_album'],
				'GALLERY_IMAGES'	=> $user_cache[$user_id]['gallery_images'],
				'U_GALLERY_SEARCH'	=> $user_cache[$user_id]['gallery_search'],
			));
		}
		$db->sql_freeresult($result);

		phpbb_generate_template_pagination($template, $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id&amp;sort_order=$sort_order"), 'pagination', 'start', $image_data['image_comments'], $config['posts_per_page'], $start);

		$template->assign_vars(array(
			'TOTAL_COMMENTS'	=> $user->lang('VIEW_IMAGE_COMMENTS', $image_data['image_comments']),
			'PAGE_NUMBER'		=> phpbb_on_page($template, $user, $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id&amp;sort_order=$sort_order"), $image_data['image_comments'], $config['posts_per_page'], $start),

			'DELETE_IMG'		=> $user->img('icon_post_delete', 'DELETE_COMMENT'),
			'EDIT_IMG'			=> $user->img('icon_post_edit', 'EDIT_COMMENT'),
			'QUOTE_IMG'			=> $user->img('icon_post_quote', 'QUOTE_COMMENT'),
			'INFO_IMG'			=> $user->img('icon_post_info', 'IP'),
			'MINI_POST_IMG'		=> $user->img('icon_post_target', 'COMMENT'),
		));
	}
}

