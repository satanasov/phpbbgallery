<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

/**
* @ignore
*/
namespace phpbbgallery\core\parser;

/**
* Main message parser for posting, pm, etc. takes raw message
* and parses it for attachments, bbcode and smilies
*/
class parse_message extends bbcode_firstpass
{
	var $attachment_data = array();
	var $filename_data = array();

	// Helps ironing out user error
	var $message_status = '';

	var $allow_img_bbcode = true;
	var $allow_flash_bbcode = true;
	var $allow_quote_bbcode = true;
	var $allow_url_bbcode = true;

	var $mode;

	/**
	* The plupload object used for dealing with attachments
	* @var \phpbb\plupload\plupload
	*/
	protected $plupload;

	/**
	* The mimetype guesser object used for attachment mimetypes
	* @var \phpbb\mimetype\guesser
	*/
	protected $mimetype_guesser;

	/**
	* Init - give message here or manually
	*/
	function parse_message($message = '')
	{
		// Init BBCode UID
		$this->bbcode_uid = substr(base_convert(unique_id(), 16, 36), 0, BBCODE_UID_LEN);
		$this->message = $message;
	}

	/**
	* Parse Message
	*/
	function parse($allow_bbcode, $allow_magic_url, $allow_smilies, $allow_img_bbcode = true, $allow_flash_bbcode = true, $allow_quote_bbcode = true, $allow_url_bbcode = true, $update_this_message = true, $mode = 'post')
	{
		global $config, $db, $user;

		$this->mode = $mode;

		foreach (array('chars', 'smilies', 'urls', 'font_size', 'img_height', 'img_width') as $key)
		{
			if (!isset($config['max_' . $mode . '_' . $key]))
			{
				$config['max_' . $mode . '_' . $key] = 0;
			}
		}

		$this->allow_img_bbcode = $allow_img_bbcode;
		$this->allow_flash_bbcode = $allow_flash_bbcode;
		$this->allow_quote_bbcode = $allow_quote_bbcode;
		$this->allow_url_bbcode = $allow_url_bbcode;

		// If false, then $this->message won't be altered, the text will be returned instead.
		if (!$update_this_message)
		{
			$tmp_message = $this->message;
			$return_message = &$this->message;
		}

		if ($this->message_status == 'display')
		{
			$this->decode_message();
		}

		// Do some general 'cleanup' first before processing message,
		// e.g. remove excessive newlines(?), smilies(?)
		$match = array('#(script|about|applet|activex|chrome):#i');
		$replace = array("\\1&#058;");
		$this->message = preg_replace($match, $replace, trim($this->message));

		// Store message length...
		$message_length = ($mode == 'post') ? utf8_strlen($this->message) : utf8_strlen(preg_replace('#\[\/?[a-z\*\+\-]+(=[\S]+)?\]#ius', ' ', $this->message));

		// Maximum message length check. 0 disables this check completely.
		if ((int) $config['max_' . $mode . '_chars'] > 0 && $message_length > (int) $config['max_' . $mode . '_chars'])
		{
			$this->warn_msg[] = $user->lang('CHARS_' . strtoupper($mode) . '_CONTAINS', $message_length) . '<br />' . $user->lang('TOO_MANY_CHARS_LIMIT', (int) $config['max_' . $mode . '_chars']);
			return (!$update_this_message) ? $return_message : $this->warn_msg;
		}

		// Minimum message length check for post only
		if ($mode === 'post')
		{
			if (!$message_length || $message_length < (int) $config['min_post_chars'])
			{
				$this->warn_msg[] = (!$message_length) ? $user->lang['TOO_FEW_CHARS'] : ($user->lang('CHARS_POST_CONTAINS', $message_length) . '<br />' . $user->lang('TOO_FEW_CHARS_LIMIT', (int) $config['min_post_chars']));
				return (!$update_this_message) ? $return_message : $this->warn_msg;
			}
		}

		// Prepare BBcode (just prepares some tags for better parsing)
		if ($allow_bbcode && strpos($this->message, '[') !== false)
		{
			$this->bbcode_init();
			$disallow = array('img', 'flash', 'quote', 'url');
			foreach ($disallow as $bool)
			{
				if (!${'allow_' . $bool . '_bbcode'})
				{
					$this->bbcodes[$bool]['disabled'] = true;
				}
			}

			$this->prepare_bbcodes();
		}

		// Parse smilies
		if ($allow_smilies)
		{
			$this->smilies($config['max_' . $mode . '_smilies']);
		}

		$num_urls = 0;

		// Parse BBCode
		if ($allow_bbcode && strpos($this->message, '[') !== false)
		{
			$this->parse_bbcode();
			$num_urls += $this->parsed_items['url'];
		}

		// Parse URL's
		if ($allow_magic_url)
		{
			$this->magic_url(generate_board_url());

			if ($config['max_' . $mode . '_urls'])
			{
				$num_urls += preg_match_all('#\<!-- ([lmwe]) --\>.*?\<!-- \1 --\>#', $this->message, $matches);
			}
		}

		// Check for out-of-bounds characters that are currently
		// not supported by utf8_bin in MySQL
		if (preg_match_all('/[\x{10000}-\x{10FFFF}]/u', $this->message, $matches))
		{
			$character_list = implode('<br />', $matches[0]);
			$this->warn_msg[] = $user->lang('UNSUPPORTED_CHARACTERS_MESSAGE', $character_list);
			return $update_this_message ? $this->warn_msg : $return_message;
		}

		// Check for "empty" message. We do not check here for maximum length, because bbcode, smilies, etc. can add to the length.
		// The maximum length check happened before any parsings.
		if ($mode === 'post' && utf8_clean_string($this->message) === '')
		{
			$this->warn_msg[] = $user->lang['TOO_FEW_CHARS'];
			return (!$update_this_message) ? $return_message : $this->warn_msg;
		}

		// Check number of links
		if ($config['max_' . $mode . '_urls'] && $num_urls > $config['max_' . $mode . '_urls'])
		{
			$this->warn_msg[] = sprintf($user->lang['TOO_MANY_URLS'], $config['max_' . $mode . '_urls']);
			return (!$update_this_message) ? $return_message : $this->warn_msg;
		}

		if (!$update_this_message)
		{
			unset($this->message);
			$this->message = $tmp_message;
			return $return_message;
		}

		$this->message_status = 'parsed';
		return false;
	}

	/**
	* Formatting text for display
	*/
	function format_display($allow_bbcode, $allow_magic_url, $allow_smilies, $update_this_message = true)
	{
		global $phpbb_dispatcher;

		// If false, then the parsed message get returned but internal message not processed.
		if (!$update_this_message)
		{
			$tmp_message = $this->message;
			$return_message = &$this->message;
		}

		if ($this->message_status == 'plain')
		{
			// Force updating message - of course.
			$this->parse($allow_bbcode, $allow_magic_url, $allow_smilies, $this->allow_img_bbcode, $this->allow_flash_bbcode, $this->allow_quote_bbcode, $this->allow_url_bbcode, true);
		}

		// Replace naughty words such as farty pants
		$this->message = censor_text($this->message);

		// Parse BBcode
		if ($allow_bbcode)
		{
			$this->bbcode_cache_init();

			// We are giving those parameters to be able to use the bbcode class on its own
			$this->bbcode_second_pass($this->message, $this->bbcode_uid);
		}

		$this->message = bbcode_nl2br($this->message);
		$this->message = smiley_text($this->message, !$allow_smilies);

		$text = $this->message;
		$uid = $this->bbcode_uid;

		/**
		* Event to modify the text after it is parsed
		*
		* @event core.modify_format_display_text_after
		* @var string	text				The message text to parse
		* @var string	uid					The bbcode uid
		* @var bool		allow_bbcode		Do we allow bbcodes
		* @var bool		allow_magic_url		Do we allow magic urls
		* @var bool		allow_smilies		Do we allow smilies
		* @var bool		update_this_message	Do we update the internal message
		*									with the parsed result
		* @since 3.1.0-a3
		*/
		$vars = array('text', 'uid', 'allow_bbcode', 'allow_magic_url', 'allow_smilies', 'update_this_message');
		extract($phpbb_dispatcher->trigger_event('core.modify_format_display_text_after', compact($vars)));

		$this->message = $text;
		$this->bbcode_uid = $uid;

		if (!$update_this_message)
		{
			unset($this->message);
			$this->message = $tmp_message;
			return $return_message;
		}

		$this->message_status = 'display';
		return false;
	}

	/**
	* Decode message to be placed back into form box
	*/
	function decode_message($custom_bbcode_uid = '', $update_this_message = true)
	{
		// If false, then the parsed message get returned but internal message not processed.
		if (!$update_this_message)
		{
			$tmp_message = $this->message;
			$return_message = &$this->message;
		}

		($custom_bbcode_uid) ? decode_message($this->message, $custom_bbcode_uid) : decode_message($this->message, $this->bbcode_uid);

		if (!$update_this_message)
		{
			unset($this->message);
			$this->message = $tmp_message;
			return $return_message;
		}

		$this->message_status = 'plain';
		return false;
	}

	/**
	* Replace magic urls of form http://xxx.xxx., www.xxx. and xxx@xxx.xxx.
	* Cuts down displayed size of link if over 50 chars, turns absolute links
	* into relative versions when the server/script path matches the link
	*/
	function magic_url($server_url)
	{
		// We use the global make_clickable function
		$this->message = make_clickable($this->message, $server_url);
	}

	/**
	* Parse Smilies
	*/
	function smilies($max_smilies = 0)
	{
		global $db, $user;
		static $match;
		static $replace;

		// See if the static arrays have already been filled on an earlier invocation
		if (!is_array($match))
		{
			$match = $replace = array();

			// NOTE: obtain_* function? chaching the table contents?

			// For now setting the ttl to 10 minutes
			switch ($db->get_sql_layer())
			{
				case 'mssql':
				case 'mssql_odbc':
				case 'mssqlnative':
					$sql = 'SELECT *
						FROM ' . SMILIES_TABLE . '
						ORDER BY LEN(code) DESC';
				break;

				// LENGTH supported by MySQL, IBM DB2, Oracle and Access for sure...
				default:
					$sql = 'SELECT *
						FROM ' . SMILIES_TABLE . '
						ORDER BY LENGTH(code) DESC';
				break;
			}
			$result = $db->sql_query($sql, 600);

			while ($row = $db->sql_fetchrow($result))
			{
				if (empty($row['code']))
				{
					continue;
				}

				// (assertion)
				$match[] = preg_quote($row['code'], '#');
				$replace[] = '<!-- s' . $row['code'] . ' --><img src="{SMILIES_PATH}/' . $row['smiley_url'] . '" alt="' . $row['code'] . '" title="' . $row['emotion'] . '" /><!-- s' . $row['code'] . ' -->';
			}
			$db->sql_freeresult($result);
		}

		if (sizeof($match))
		{
			if ($max_smilies)
			{
				// 'u' modifier has been added to correctly parse smilies within unicode strings
				// For details: http://tracker.phpbb.com/browse/PHPBB3-10117
				$num_matches = preg_match_all('#(?<=^|[\n .])(?:' . implode('|', $match) . ')(?![^<>]*>)#u', $this->message, $matches);
				unset($matches);

				if ($num_matches !== false && $num_matches > $max_smilies)
				{
					$this->warn_msg[] = sprintf($user->lang['TOO_MANY_SMILIES'], $max_smilies);
					return;
				}
			}

			// Make sure the delimiter # is added in front and at the end of every element within $match
			// 'u' modifier has been added to correctly parse smilies within unicode strings
			// For details: http://tracker.phpbb.com/browse/PHPBB3-10117

			$this->message = trim(preg_replace(explode(chr(0), '#(?<=^|[\n .])' . implode('(?![^<>]*>)#u' . chr(0) . '#(?<=^|[\n .])', $match) . '(?![^<>]*>)#u'), $replace, $this->message));
		}
	}

	/**
	* Parse Attachments
	*/
	function parse_attachments($form_name, $mode, $forum_id, $submit, $preview, $refresh, $is_message = false)
	{
		global $config, $auth, $user, $phpbb_root_path, $phpEx, $db, $request;

		$error = array();

		$num_attachments = sizeof($this->attachment_data);
		$this->filename_data['filecomment'] = utf8_normalize_nfc(request_var('filecomment', '', true));
		$upload = $request->file($form_name);
		$upload_file = (!empty($upload) && $upload['name'] !== 'none' && trim($upload['name']));

		$add_file		= (isset($_POST['add_file'])) ? true : false;
		$delete_file	= (isset($_POST['delete_file'])) ? true : false;

		// First of all adjust comments if changed
		$actual_comment_list = utf8_normalize_nfc(request_var('comment_list', array(''), true));

		foreach ($actual_comment_list as $comment_key => $comment)
		{
			if (!isset($this->attachment_data[$comment_key]))
			{
				continue;
			}

			if ($this->attachment_data[$comment_key]['attach_comment'] != $actual_comment_list[$comment_key])
			{
				$this->attachment_data[$comment_key]['attach_comment'] = $actual_comment_list[$comment_key];
			}
		}

		$cfg = array();
		$cfg['max_attachments'] = ($is_message) ? $config['max_attachments_pm'] : $config['max_attachments'];
		$forum_id = ($is_message) ? 0 : $forum_id;

		if ($submit && in_array($mode, array('post', 'reply', 'quote', 'edit')) && $upload_file)
		{
			if ($num_attachments < $cfg['max_attachments'] || $auth->acl_get('a_') || $auth->acl_get('m_', $forum_id))
			{
				$filedata = upload_attachment($form_name, $forum_id, false, '', $is_message);
				$error = $filedata['error'];

				if ($filedata['post_attach'] && !sizeof($error))
				{
					$sql_ary = array(
						'physical_filename'	=> $filedata['physical_filename'],
						'attach_comment'	=> $this->filename_data['filecomment'],
						'real_filename'		=> $filedata['real_filename'],
						'extension'			=> $filedata['extension'],
						'mimetype'			=> $filedata['mimetype'],
						'filesize'			=> $filedata['filesize'],
						'filetime'			=> $filedata['filetime'],
						'thumbnail'			=> $filedata['thumbnail'],
						'is_orphan'			=> 1,
						'in_message'		=> ($is_message) ? 1 : 0,
						'poster_id'			=> $user->data['user_id'],
					);

					$db->sql_query('INSERT INTO ' . ATTACHMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

					$new_entry = array(
						'attach_id'		=> $db->sql_nextid(),
						'is_orphan'		=> 1,
						'real_filename'	=> $filedata['real_filename'],
						'attach_comment'=> $this->filename_data['filecomment'],
						'filesize'		=> $filedata['filesize'],
					);

					$this->attachment_data = array_merge(array(0 => $new_entry), $this->attachment_data);
					$this->message = preg_replace('#\[attachment=([0-9]+)\](.*?)\[\/attachment\]#e', "'[attachment='.(\\1 + 1).']\\2[/attachment]'", $this->message);

					$this->filename_data['filecomment'] = '';

					// This Variable is set to false here, because Attachments are entered into the
					// Database in two modes, one if the id_list is 0 and the second one if post_attach is true
					// Since post_attach is automatically switched to true if an Attachment got added to the filesystem,
					// but we are assigning an id of 0 here, we have to reset the post_attach variable to false.
					//
					// This is very relevant, because it could happen that the post got not submitted, but we do not
					// know this circumstance here. We could be at the posting page or we could be redirected to the entered
					// post. :)
					$filedata['post_attach'] = false;
				}
			}
			else
			{
				$error[] = $user->lang('TOO_MANY_ATTACHMENTS', (int) $cfg['max_attachments']);
			}
		}

		if ($preview || $refresh || sizeof($error))
		{
			if (isset($this->plupload) && $this->plupload->is_active())
			{
				$json_response = new \phpbb\json_response();
			}

			// Perform actions on temporary attachments
			if ($delete_file)
			{
				include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);

				$index = array_keys(request_var('delete_file', array(0 => 0)));
				$index = (!empty($index)) ? $index[0] : false;

				if ($index !== false && !empty($this->attachment_data[$index]))
				{
					// delete selected attachment
					if ($this->attachment_data[$index]['is_orphan'])
					{
						$sql = 'SELECT attach_id, physical_filename, thumbnail
							FROM ' . ATTACHMENTS_TABLE . '
							WHERE attach_id = ' . (int) $this->attachment_data[$index]['attach_id'] . '
								AND is_orphan = 1
								AND poster_id = ' . $user->data['user_id'];
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if ($row)
						{
							phpbb_unlink($row['physical_filename'], 'file');

							if ($row['thumbnail'])
							{
								phpbb_unlink($row['physical_filename'], 'thumbnail');
							}

							$db->sql_query('DELETE FROM ' . ATTACHMENTS_TABLE . ' WHERE attach_id = ' . (int) $this->attachment_data[$index]['attach_id']);
						}
					}
					else
					{
						delete_attachments('attach', array(intval($this->attachment_data[$index]['attach_id'])));
					}

					unset($this->attachment_data[$index]);
					$this->message = preg_replace('#\[attachment=([0-9]+)\](.*?)\[\/attachment\]#e', "(\\1 == \$index) ? '' : ((\\1 > \$index) ? '[attachment=' . (\\1 - 1) . ']\\2[/attachment]' : '\\0')", $this->message);

					// Reindex Array
					$this->attachment_data = array_values($this->attachment_data);
					if (isset($this->plupload) && $this->plupload->is_active())
					{
						$json_response->send($this->attachment_data);
					}
				}
			}
			else if (($add_file || $preview) && $upload_file)
			{
				if ($num_attachments < $cfg['max_attachments'] || $auth->acl_gets('m_', 'a_', $forum_id))
				{
					$filedata = upload_attachment($form_name, $forum_id, false, '', $is_message, false, $this->mimetype_guesser, $this->plupload);
					$error = array_merge($error, $filedata['error']);

					if (!sizeof($error))
					{
						$sql_ary = array(
							'physical_filename'	=> $filedata['physical_filename'],
							'attach_comment'	=> $this->filename_data['filecomment'],
							'real_filename'		=> $filedata['real_filename'],
							'extension'			=> $filedata['extension'],
							'mimetype'			=> $filedata['mimetype'],
							'filesize'			=> $filedata['filesize'],
							'filetime'			=> $filedata['filetime'],
							'thumbnail'			=> $filedata['thumbnail'],
							'is_orphan'			=> 1,
							'in_message'		=> ($is_message) ? 1 : 0,
							'poster_id'			=> $user->data['user_id'],
						);

						$db->sql_query('INSERT INTO ' . ATTACHMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

						$new_entry = array(
							'attach_id'		=> $db->sql_nextid(),
							'is_orphan'		=> 1,
							'real_filename'	=> $filedata['real_filename'],
							'attach_comment'=> $this->filename_data['filecomment'],
							'filesize'		=> $filedata['filesize'],
						);

						$this->attachment_data = array_merge(array(0 => $new_entry), $this->attachment_data);
						$this->message = preg_replace('#\[attachment=([0-9]+)\](.*?)\[\/attachment\]#e', "'[attachment='.(\\1 + 1).']\\2[/attachment]'", $this->message);
						$this->filename_data['filecomment'] = '';

						if (isset($this->plupload) && $this->plupload->is_active())
						{
							$download_url = append_sid("{$phpbb_root_path}download/file.{$phpEx}", 'mode=view&amp;id=' . $new_entry['attach_id']);

							// Send the client the attachment data to maintain state
							$json_response->send(array('data' => $this->attachment_data, 'download_url' => $download_url));
						}
					}
				}
				else
				{
					$error[] = $user->lang('TOO_MANY_ATTACHMENTS', (int) $cfg['max_attachments']);
				}

				if (!empty($error) && isset($this->plupload) && $this->plupload->is_active())
				{
					// If this is a plupload (and thus ajax) request, give the
					// client the first error we have
					$json_response->send(array(
						'jsonrpc' => '2.0',
						'id' => 'id',
						'error' => array(
							'code' => 105,
							'message' => current($error),
						),
					));
				}
			}
		}

		foreach ($error as $error_msg)
		{
			$this->warn_msg[] = $error_msg;
		}
	}

	/**
	* Get Attachment Data
	*/
	function get_submitted_attachment_data($check_user_id = false)
	{
		global $user, $db, $phpbb_root_path, $phpEx, $config;
		global $request;

		$this->filename_data['filecomment'] = utf8_normalize_nfc(request_var('filecomment', '', true));
		$attachment_data = $request->variable('attachment_data', array(0 => array('' => '')), true, \phpbb\request\request_interface::POST);
		$this->attachment_data = array();

		$check_user_id = ($check_user_id === false) ? $user->data['user_id'] : $check_user_id;

		if (!sizeof($attachment_data))
		{
			return;
		}

		$not_orphan = $orphan = array();

		foreach ($attachment_data as $pos => $var_ary)
		{
			if ($var_ary['is_orphan'])
			{
				$orphan[(int) $var_ary['attach_id']] = $pos;
			}
			else
			{
				$not_orphan[(int) $var_ary['attach_id']] = $pos;
			}
		}

		// Regenerate already posted attachments
		if (sizeof($not_orphan))
		{
			// Get the attachment data, based on the poster id...
			$sql = 'SELECT attach_id, is_orphan, real_filename, attach_comment, filesize
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE ' . $db->sql_in_set('attach_id', array_keys($not_orphan)) . '
					AND poster_id = ' . $check_user_id;
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$pos = $not_orphan[$row['attach_id']];
				$this->attachment_data[$pos] = $row;
				$this->attachment_data[$pos]['attach_comment'] = $attachment_data[$pos]['attach_comment'];

				unset($not_orphan[$row['attach_id']]);
			}
			$db->sql_freeresult($result);
		}

		if (sizeof($not_orphan))
		{
			trigger_error('NO_ACCESS_ATTACHMENT', E_USER_ERROR);
		}

		// Regenerate newly uploaded attachments
		if (sizeof($orphan))
		{
			$sql = 'SELECT attach_id, is_orphan, real_filename, attach_comment, filesize
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE ' . $db->sql_in_set('attach_id', array_keys($orphan)) . '
					AND poster_id = ' . $user->data['user_id'] . '
					AND is_orphan = 1';
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$pos = $orphan[$row['attach_id']];
				$this->attachment_data[$pos] = $row;
				$this->attachment_data[$pos]['attach_comment'] = $attachment_data[$pos]['attach_comment'];

				unset($orphan[$row['attach_id']]);
			}
			$db->sql_freeresult($result);
		}

		if (sizeof($orphan))
		{
			trigger_error('NO_ACCESS_ATTACHMENT', E_USER_ERROR);
		}

		ksort($this->attachment_data);
	}

	/**
	* Parse Poll
	*/
	function parse_poll(&$poll)
	{
		global $auth, $user, $config;

		$poll_max_options = $poll['poll_max_options'];

		// Parse Poll Option text ;)
		$tmp_message = $this->message;
		$this->message = $poll['poll_option_text'];
		$bbcode_bitfield = $this->bbcode_bitfield;

		$poll['poll_option_text'] = $this->parse($poll['enable_bbcode'], ($config['allow_post_links']) ? $poll['enable_urls'] : false, $poll['enable_smilies'], $poll['img_status'], false, false, $config['allow_post_links'], false, 'poll');

		$bbcode_bitfield = base64_encode(base64_decode($bbcode_bitfield) | base64_decode($this->bbcode_bitfield));
		$this->message = $tmp_message;

		// Parse Poll Title
		$tmp_message = $this->message;
		$this->message = $poll['poll_title'];
		$this->bbcode_bitfield = $bbcode_bitfield;

		$poll['poll_options'] = explode("\n", trim($poll['poll_option_text']));
		$poll['poll_options_size'] = sizeof($poll['poll_options']);

		if (!$poll['poll_title'] && $poll['poll_options_size'])
		{
			$this->warn_msg[] = $user->lang['NO_POLL_TITLE'];
		}
		else
		{
			if (utf8_strlen(preg_replace('#\[\/?[a-z\*\+\-]+(=[\S]+)?\]#ius', ' ', $this->message)) > 100)
			{
				$this->warn_msg[] = $user->lang['POLL_TITLE_TOO_LONG'];
			}
			$poll['poll_title'] = $this->parse($poll['enable_bbcode'], ($config['allow_post_links']) ? $poll['enable_urls'] : false, $poll['enable_smilies'], $poll['img_status'], false, false, $config['allow_post_links'], false, 'poll');
			if (strlen($poll['poll_title']) > 255)
			{
				$this->warn_msg[] = $user->lang['POLL_TITLE_COMP_TOO_LONG'];
			}
		}

		$this->bbcode_bitfield = base64_encode(base64_decode($bbcode_bitfield) | base64_decode($this->bbcode_bitfield));
		$this->message = $tmp_message;
		unset($tmp_message);

		if (sizeof($poll['poll_options']) == 1)
		{
			$this->warn_msg[] = $user->lang['TOO_FEW_POLL_OPTIONS'];
		}
		else if ($poll['poll_options_size'] > (int) $config['max_poll_options'])
		{
			$this->warn_msg[] = $user->lang['TOO_MANY_POLL_OPTIONS'];
		}
		else if ($poll_max_options > $poll['poll_options_size'])
		{
			$this->warn_msg[] = $user->lang['TOO_MANY_USER_OPTIONS'];
		}

		$poll['poll_max_options'] = ($poll['poll_max_options'] < 1) ? 1 : (($poll['poll_max_options'] > $config['max_poll_options']) ? $config['max_poll_options'] : $poll['poll_max_options']);
	}

	/**
	* Setter function for passing the plupload object
	*
	* @param \phpbb\plupload\plupload $plupload The plupload object
	*
	* @return null
	*/
	public function set_plupload(\phpbb\plupload\plupload $plupload)
	{
		$this->plupload = $plupload;
	}

	/**
	* Setter function for passing the mimetype_guesser object
	*
	* @param \phpbb\mimetype\guesser $mimetype_guesser The mimetype_guesser object
	*
	* @return null
	*/
	public function set_mimetype_guesser(\phpbb\mimetype\guesser $mimetype_guesser)
	{
		$this->mimetype_guesser = $mimetype_guesser;
	}
}
