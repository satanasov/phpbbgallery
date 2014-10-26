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
* BBCODE FIRSTPASS
* BBCODE first pass class (functions for parsing messages for db storage)
*/
class bbcode_firstpass extends bbcode
{
	var $message = '';
	var $warn_msg = array();
	var $parsed_items = array();

	/**
	* Parse BBCode
	*/
	function parse_bbcode()
	{
		if (!$this->bbcodes)
		{
			$this->bbcode_init();
		}

		global $user;

		$this->bbcode_bitfield = '';
		$bitfield = new bitfield();

		foreach ($this->bbcodes as $bbcode_name => $bbcode_data)
		{
			if (isset($bbcode_data['disabled']) && $bbcode_data['disabled'])
			{
				foreach ($bbcode_data['regexp'] as $regexp => $replacement)
				{
					if (preg_match($regexp, $this->message))
					{
						$this->warn_msg[] = sprintf($user->lang['UNAUTHORISED_BBCODE'] , '[' . $bbcode_name . ']');
						continue;
					}
				}
			}
			else
			{
				foreach ($bbcode_data['regexp'] as $regexp => $replacement)
				{
					// The pattern gets compiled and cached by the PCRE extension,
					// it should not demand recompilation
					if (preg_match($regexp, $this->message))
					{
						$this->message = preg_replace($regexp, $replacement, $this->message);
						$bitfield->set($bbcode_data['bbcode_id']);
					}
				}
			}
		}

		$this->bbcode_bitfield = $bitfield->get_base64();
	}

	/**
	* Prepare some bbcodes for better parsing
	*/
	function prepare_bbcodes()
	{
		// Ok, seems like users instead want the no-parsing of urls, smilies, etc. after and before and within quote tags being tagged as "not a bug".
		// Fine by me ;) Will ease our live... but do not come back and cry at us, we won't hear you.

		/* Add newline at the end and in front of each quote block to prevent parsing errors (urls, smilies, etc.)
		if (strpos($this->message, '[quote') !== false && strpos($this->message, '[/quote]') !== false)
		{
			$this->message = str_replace("\r\n", "\n", $this->message);

			// We strip newlines and spaces after and before quotes in quotes (trimming) and then add exactly one newline
			$this->message = preg_replace('#\[quote(=&quot;.*?&quot;)?\]\s*(.*?)\s*\[/quote\]#siu', '[quote\1]' . "\n" . '\2' ."\n[/quote]", $this->message);
		}
		*/

		// Add other checks which needs to be placed before actually parsing anything (be it bbcodes, smilies, urls...)
	}

	/**
	* Init bbcode data for later parsing
	*/
	function bbcode_init($allow_custom_bbcode = true)
	{
		global $phpbb_dispatcher;

		static $rowset;

		// This array holds all bbcode data. BBCodes will be processed in this
		// order, so it is important to keep [code] in first position and
		// [quote] in second position.
		// To parse multiline URL we enable dotall option setting only for URL text
		// but not for link itself, thus [url][/url] is not affected.
		$this->bbcodes = array(
			'code'			=> array('bbcode_id' => 8,	'regexp' => array('#\[code(?:=([a-z]+))?\](.+\[/code\])#uise' => "\$this->bbcode_code('\$1', '\$2')")),
			'quote'			=> array('bbcode_id' => 0,	'regexp' => array('#\[quote(?:=&quot;(.*?)&quot;)?\](.+)\[/quote\]#uise' => "\$this->bbcode_quote('\$0')")),
			'attachment'	=> array('bbcode_id' => 12,	'regexp' => array('#\[attachment=([0-9]+)\](.*?)\[/attachment\]#uise' => "\$this->bbcode_attachment('\$1', '\$2')")),
			'b'				=> array('bbcode_id' => 1,	'regexp' => array('#\[b\](.*?)\[/b\]#uise' => "\$this->bbcode_strong('\$1')")),
			'i'				=> array('bbcode_id' => 2,	'regexp' => array('#\[i\](.*?)\[/i\]#uise' => "\$this->bbcode_italic('\$1')")),
			'url'			=> array('bbcode_id' => 3,	'regexp' => array('#\[url(=(.*))?\](?(1)((?s).*(?-s))|(.*))\[/url\]#uiUe' => "\$this->validate_url('\$2', ('\$3') ? '\$3' : '\$4')")),
			'img'			=> array('bbcode_id' => 4,	'regexp' => array('#\[img\](.*)\[/img\]#uiUe' => "\$this->bbcode_img('\$1')")),
			'size'			=> array('bbcode_id' => 5,	'regexp' => array('#\[size=([\-\+]?\d+)\](.*?)\[/size\]#uise' => "\$this->bbcode_size('\$1', '\$2')")),
			'color'			=> array('bbcode_id' => 6,	'regexp' => array('!\[color=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)\](.*?)\[/color\]!uise' => "\$this->bbcode_color('\$1', '\$2')")),
			'u'				=> array('bbcode_id' => 7,	'regexp' => array('#\[u\](.*?)\[/u\]#uise' => "\$this->bbcode_underline('\$1')")),
			'list'			=> array('bbcode_id' => 9,	'regexp' => array('#\[list(?:=(?:[a-z0-9]|disc|circle|square))?].*\[/list]#uise' => "\$this->bbcode_parse_list('\$0')")),
			'email'			=> array('bbcode_id' => 10,	'regexp' => array('#\[email=?(.*?)?\](.*?)\[/email\]#uise' => "\$this->validate_email('\$1', '\$2')")),
			'flash'			=> array('bbcode_id' => 11,	'regexp' => array('#\[flash=([0-9]+),([0-9]+)\](.*?)\[/flash\]#uie' => "\$this->bbcode_flash('\$1', '\$2', '\$3')"))
		);

		// Zero the parsed items array
		$this->parsed_items = array();

		foreach ($this->bbcodes as $tag => $bbcode_data)
		{
			$this->parsed_items[$tag] = 0;
		}

		if (!$allow_custom_bbcode)
		{
			return;
		}

		if (!is_array($rowset))
		{
			global $db;
			$rowset = array();

			$sql = 'SELECT *
				FROM ' . BBCODES_TABLE;
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$rowset[] = $row;
			}
			$db->sql_freeresult($result);
		}

		foreach ($rowset as $row)
		{
			$this->bbcodes[$row['bbcode_tag']] = array(
				'bbcode_id'	=> (int) $row['bbcode_id'],
				'regexp'	=> array($row['first_pass_match'] => str_replace('$uid', $this->bbcode_uid, $row['first_pass_replace']))
			);
		}

		$bbcodes = $this->bbcodes;

		/**
		* Event to modify the bbcode data for later parsing
		*
		* @event core.modify_bbcode_init
		* @var array	bbcodes		Array of bbcode data for use in parsing
		* @var array	rowset		Array of bbcode data from the database
		* @since 3.1.0-a3
		*/
		$vars = array('bbcodes', 'rowset');
		extract($phpbb_dispatcher->trigger_event('core.modify_bbcode_init', compact($vars)));

		$this->bbcodes = $bbcodes;
	}

	/**
	* Making some pre-checks for bbcodes as well as increasing the number of parsed items
	*/
	function check_bbcode($bbcode, &$in)
	{
		// when using the /e modifier, preg_replace slashes double-quotes but does not
		// seem to slash anything else
		$in = str_replace("\r\n", "\n", str_replace('\"', '"', $in));

		// Trimming here to make sure no empty bbcodes are parsed accidently
		if (trim($in) == '')
		{
			return false;
		}

		$this->parsed_items[$bbcode]++;

		return true;
	}

	/**
	* Transform some characters in valid bbcodes
	*/
	function bbcode_specialchars($text)
	{
		$str_from = array('<', '>', '[', ']', '.', ':');
		$str_to = array('&lt;', '&gt;', '&#91;', '&#93;', '&#46;', '&#58;');

		return str_replace($str_from, $str_to, $text);
	}

	/**
	* Parse size tag
	*/
	function bbcode_size($stx, $in)
	{
		global $user, $config;

		if (!$this->check_bbcode('size', $in))
		{
			return $in;
		}

		if ($config['max_' . $this->mode . '_font_size'] && $config['max_' . $this->mode . '_font_size'] < $stx)
		{
			$this->warn_msg[] = $user->lang('MAX_FONT_SIZE_EXCEEDED', (int) $config['max_' . $this->mode . '_font_size']);

			return '[size=' . $stx . ']' . $in . '[/size]';
		}

		// Do not allow size=0
		if ($stx <= 0)
		{
			return '[size=' . $stx . ']' . $in . '[/size]';
		}

		return '[size=' . $stx . ':' . $this->bbcode_uid . ']' . $in . '[/size:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse color tag
	*/
	function bbcode_color($stx, $in)
	{
		if (!$this->check_bbcode('color', $in))
		{
			return $in;
		}

		return '[color=' . $stx . ':' . $this->bbcode_uid . ']' . $in . '[/color:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse u tag
	*/
	function bbcode_underline($in)
	{
		if (!$this->check_bbcode('u', $in))
		{
			return $in;
		}

		return '[u:' . $this->bbcode_uid . ']' . $in . '[/u:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse b tag
	*/
	function bbcode_strong($in)
	{
		if (!$this->check_bbcode('b', $in))
		{
			return $in;
		}

		return '[b:' . $this->bbcode_uid . ']' . $in . '[/b:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse i tag
	*/
	function bbcode_italic($in)
	{
		if (!$this->check_bbcode('i', $in))
		{
			return $in;
		}

		return '[i:' . $this->bbcode_uid . ']' . $in . '[/i:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse img tag
	*/
	function bbcode_img($in)
	{
		global $user, $config;

		if (!$this->check_bbcode('img', $in))
		{
			return $in;
		}

		$in = trim($in);
		$error = false;

		$in = str_replace(' ', '%20', $in);

		// Checking urls
		if (!preg_match('#^' . get_preg_expression('url') . '$#i', $in) && !preg_match('#^' . get_preg_expression('www_url') . '$#i', $in))
		{
			return '[img]' . $in . '[/img]';
		}

		// Try to cope with a common user error... not specifying a protocol but only a subdomain
		if (!preg_match('#^[a-z0-9]+://#i', $in))
		{
			$in = 'http://' . $in;
		}

		if ($config['max_' . $this->mode . '_img_height'] || $config['max_' . $this->mode . '_img_width'])
		{
			$stats = @getimagesize(htmlspecialchars_decode($in));

			if ($stats === false)
			{
				$error = true;
				$this->warn_msg[] = $user->lang['UNABLE_GET_IMAGE_SIZE'];
			}
			else
			{
				if ($config['max_' . $this->mode . '_img_height'] && $config['max_' . $this->mode . '_img_height'] < $stats[1])
				{
					$error = true;
					$this->warn_msg[] = $user->lang('MAX_IMG_HEIGHT_EXCEEDED', (int) $config['max_' . $this->mode . '_img_height']);
				}

				if ($config['max_' . $this->mode . '_img_width'] && $config['max_' . $this->mode . '_img_width'] < $stats[0])
				{
					$error = true;
					$this->warn_msg[] = $user->lang('MAX_IMG_WIDTH_EXCEEDED', (int) $config['max_' . $this->mode . '_img_width']);
				}
			}
		}

		if ($error || $this->path_in_domain($in))
		{
			return '[img]' . $in . '[/img]';
		}

		return '[img:' . $this->bbcode_uid . ']' . $this->bbcode_specialchars($in) . '[/img:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse flash tag
	*/
	function bbcode_flash($width, $height, $in)
	{
		global $user, $config;

		if (!$this->check_bbcode('flash', $in))
		{
			return $in;
		}

		$in = trim($in);
		$error = false;

		// Do not allow 0-sizes generally being entered
		if ($width <= 0 || $height <= 0)
		{
			return '[flash=' . $width . ',' . $height . ']' . $in . '[/flash]';
		}

		$in = str_replace(' ', '%20', $in);

		// Make sure $in is a URL.
		if (!preg_match('#^' . get_preg_expression('url') . '$#i', $in) &&
			!preg_match('#^' . get_preg_expression('www_url') . '$#i', $in))
		{
			return '[flash=' . $width . ',' . $height . ']' . $in . '[/flash]';
		}

		// Apply the same size checks on flash files as on images
		if ($config['max_' . $this->mode . '_img_height'] || $config['max_' . $this->mode . '_img_width'])
		{
			if ($config['max_' . $this->mode . '_img_height'] && $config['max_' . $this->mode . '_img_height'] < $height)
			{
				$error = true;
				$this->warn_msg[] = $user->lang('MAX_FLASH_HEIGHT_EXCEEDED', (int) $config['max_' . $this->mode . '_img_height']);
			}

			if ($config['max_' . $this->mode . '_img_width'] && $config['max_' . $this->mode . '_img_width'] < $width)
			{
				$error = true;
				$this->warn_msg[] = $user->lang('MAX_FLASH_WIDTH_EXCEEDED', (int) $config['max_' . $this->mode . '_img_width']);
			}
		}

		if ($error || $this->path_in_domain($in))
		{
			return '[flash=' . $width . ',' . $height . ']' . $in . '[/flash]';
		}

		return '[flash=' . $width . ',' . $height . ':' . $this->bbcode_uid . ']' . $this->bbcode_specialchars($in) . '[/flash:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse inline attachments [ia]
	*/
	function bbcode_attachment($stx, $in)
	{
		if (!$this->check_bbcode('attachment', $in))
		{
			return $in;
		}

		return '[attachment=' . $stx . ':' . $this->bbcode_uid . ']<!-- ia' . $stx . ' -->' . trim($in) . '<!-- ia' . $stx . ' -->[/attachment:' . $this->bbcode_uid . ']';
	}

	/**
	* Parse code text from code tag
	* @access private
	*/
	function bbcode_parse_code($stx, &$code)
	{
		switch (strtolower($stx))
		{
			case 'php':

				$remove_tags = false;

				$str_from = array('&lt;', '&gt;', '&#91;', '&#93;', '&#46;', '&#58;', '&#058;');
				$str_to = array('<', '>', '[', ']', '.', ':', ':');
				$code = str_replace($str_from, $str_to, $code);

				if (!preg_match('/\<\?.*?\?\>/is', $code))
				{
					$remove_tags = true;
					$code = "<?php $code ?>";
				}

				$conf = array('highlight.bg', 'highlight.comment', 'highlight.default', 'highlight.html', 'highlight.keyword', 'highlight.string');
				foreach ($conf as $ini_var)
				{
					@ini_set($ini_var, str_replace('highlight.', 'syntax', $ini_var));
				}

				// Because highlight_string is specialcharing the text (but we already did this before), we have to reverse this in order to get correct results
				$code = htmlspecialchars_decode($code);
				$code = highlight_string($code, true);

				$str_from = array('<span style="color: ', '<font color="syntax', '</font>', '<code>', '</code>','[', ']', '.', ':');
				$str_to = array('<span class="', '<span class="syntax', '</span>', '', '', '&#91;', '&#93;', '&#46;', '&#58;');

				if ($remove_tags)
				{
					$str_from[] = '<span class="syntaxdefault">&lt;?php </span>';
					$str_to[] = '';
					$str_from[] = '<span class="syntaxdefault">&lt;?php&nbsp;';
					$str_to[] = '<span class="syntaxdefault">';
				}

				$code = str_replace($str_from, $str_to, $code);
				$code = preg_replace('#^(<span class="[a-z_]+">)\n?(.*?)\n?(</span>)$#is', '$1$2$3', $code);

				if ($remove_tags)
				{
					$code = preg_replace('#(<span class="[a-z]+">)?\?&gt;(</span>)#', '$1&nbsp;$2', $code);
				}

				$code = preg_replace('#^<span class="[a-z]+"><span class="([a-z]+)">(.*)</span></span>#s', '<span class="$1">$2</span>', $code);
				$code = preg_replace('#(?:\s++|&nbsp;)*+</span>$#u', '</span>', $code);

				// remove newline at the end
				if (!empty($code) && substr($code, -1) == "\n")
				{
					$code = substr($code, 0, -1);
				}

				return "[code=$stx:" . $this->bbcode_uid . ']' . $code . '[/code:' . $this->bbcode_uid . ']';
			break;

			default:
				return '[code:' . $this->bbcode_uid . ']' . $this->bbcode_specialchars($code) . '[/code:' . $this->bbcode_uid . ']';
			break;
		}
	}

	/**
	* Parse code tag
	* Expects the argument to start right after the opening [code] tag and to end with [/code]
	*/
	function bbcode_code($stx, $in)
	{
		if (!$this->check_bbcode('code', $in))
		{
			return $in;
		}

		// We remove the hardcoded elements from the code block here because it is not used in code blocks
		// Having it here saves us one preg_replace per message containing [code] blocks
		// Additionally, magic url parsing should go after parsing bbcodes, but for safety those are stripped out too...
		$htm_match = get_preg_expression('bbcode_htm');
		unset($htm_match[4], $htm_match[5]);
		$htm_replace = array('\1', '\1', '\2', '\1');

		$out = $code_block = '';
		$open = 1;

		while ($in)
		{
			// Determine position and tag length of next code block
			preg_match('#(.*?)(\[code(?:=([a-z]+))?\])(.+)#is', $in, $buffer);
			$pos = (isset($buffer[1])) ? strlen($buffer[1]) : false;
			$tag_length = (isset($buffer[2])) ? strlen($buffer[2]) : false;

			// Determine position of ending code tag
			$pos2 = stripos($in, '[/code]');

			// Which is the next block, ending code or code block
			if ($pos !== false && $pos < $pos2)
			{
				// Open new block
				if (!$open)
				{
					$out .= substr($in, 0, $pos);
					$in = substr($in, $pos);
					$stx = (isset($buffer[3])) ? $buffer[3] : '';
					$code_block = '';
				}
				else
				{
					// Already opened block, just append to the current block
					$code_block .= substr($in, 0, $pos) . ((isset($buffer[2])) ? $buffer[2] : '');
					$in = substr($in, $pos);
				}

				$in = substr($in, $tag_length);
				$open++;
			}
			else
			{
				// Close the block
				if ($open == 1)
				{
					$code_block .= substr($in, 0, $pos2);
					$code_block = preg_replace($htm_match, $htm_replace, $code_block);

					// Parse this code block
					$out .= $this->bbcode_parse_code($stx, $code_block);
					$code_block = '';
					$open--;
				}
				else if ($open)
				{
					// Close one open tag... add to the current code block
					$code_block .= substr($in, 0, $pos2 + 7);
					$open--;
				}
				else
				{
					// end code without opening code... will be always outside code block
					$out .= substr($in, 0, $pos2 + 7);
				}

				$in = substr($in, $pos2 + 7);
			}
		}

		// if now $code_block has contents we need to parse the remaining code while removing the last closing tag to match up.
		if ($code_block)
		{
			$code_block = substr($code_block, 0, -7);
			$code_block = preg_replace($htm_match, $htm_replace, $code_block);

			$out .= $this->bbcode_parse_code($stx, $code_block);
		}

		return $out;
	}

	/**
	* Parse list bbcode
	* Expects the argument to start with a tag
	*/
	function bbcode_parse_list($in)
	{
		if (!$this->check_bbcode('list', $in))
		{
			return $in;
		}

		// $tok holds characters to stop at. Since the string starts with a '[' we'll get everything up to the first ']' which should be the opening [list] tag
		$tok = ']';
		$out = '[';

		// First character is [
		$in = substr($in, 1);
		$list_end_tags = $item_end_tags = array();

		do
		{
			$pos = strlen($in);

			for ($i = 0, $tok_len = strlen($tok); $i < $tok_len; ++$i)
			{
				$tmp_pos = strpos($in, $tok[$i]);

				if ($tmp_pos !== false && $tmp_pos < $pos)
				{
					$pos = $tmp_pos;
				}
			}

			$buffer = substr($in, 0, $pos);
			$tok = $in[$pos];

			$in = substr($in, $pos + 1);

			if ($tok == ']')
			{
				// if $tok is ']' the buffer holds a tag
				if (strtolower($buffer) == '/list' && sizeof($list_end_tags))
				{
					// valid [/list] tag, check nesting so that we don't hit false positives
					if (sizeof($item_end_tags) && sizeof($item_end_tags) >= sizeof($list_end_tags))
					{
						// current li tag has not been closed
						$out = preg_replace('/\n?\[$/', '[', $out) . array_pop($item_end_tags) . '][';
					}

					$out .= array_pop($list_end_tags) . ']';
					$tok = '[';
				}
				else if (preg_match('#^list(=[0-9a-z]+)?$#i', $buffer, $m))
				{
					// sub-list, add a closing tag
					if (empty($m[1]) || preg_match('/^=(?:disc|square|circle)$/i', $m[1]))
					{
						array_push($list_end_tags, '/list:u:' . $this->bbcode_uid);
					}
					else
					{
						array_push($list_end_tags, '/list:o:' . $this->bbcode_uid);
					}
					$out .= 'list' . substr($buffer, 4) . ':' . $this->bbcode_uid . ']';
					$tok = '[';
				}
				else
				{
					if (($buffer == '*' || substr($buffer, -2) == '[*') && sizeof($list_end_tags))
					{
						// the buffer holds a bullet tag and we have a [list] tag open
						if (sizeof($item_end_tags) >= sizeof($list_end_tags))
						{
							if (substr($buffer, -2) == '[*')
							{
								$out .= substr($buffer, 0, -2) . '[';
							}
							// current li tag has not been closed
							if (preg_match('/\n\[$/', $out, $m))
							{
								$out = preg_replace('/\n\[$/', '[', $out);
								$buffer = array_pop($item_end_tags) . "]\n[*:" . $this->bbcode_uid;
							}
							else
							{
								$buffer = array_pop($item_end_tags) . '][*:' . $this->bbcode_uid;
							}
						}
						else
						{
							$buffer = '*:' . $this->bbcode_uid;
						}

						$item_end_tags[] = '/*:m:' . $this->bbcode_uid;
					}
					else if ($buffer == '/*')
					{
						array_pop($item_end_tags);
						$buffer = '/*:' . $this->bbcode_uid;
					}

					$out .= $buffer . $tok;
					$tok = '[]';
				}
			}
			else
			{
				// Not within a tag, just add buffer to the return string
				$out .= $buffer . $tok;
				$tok = ($tok == '[') ? ']' : '[]';
			}
		}
		while ($in);

		// do we have some tags open? close them now
		if (sizeof($item_end_tags))
		{
			$out .= '[' . implode('][', $item_end_tags) . ']';
		}
		if (sizeof($list_end_tags))
		{
			$out .= '[' . implode('][', $list_end_tags) . ']';
		}

		return $out;
	}

	/**
	* Parse quote bbcode
	* Expects the argument to start with a tag
	*/
	function bbcode_quote($in)
	{
		global $config, $user;

		$in = str_replace("\r\n", "\n", str_replace('\"', '"', trim($in)));

		if (!$in)
		{
			return '';
		}

		// To let the parser not catch tokens within quote_username quotes we encode them before we start this...
		$in = preg_replace('#quote=&quot;(.*?)&quot;\]#ie', "'quote=&quot;' . str_replace(array('[', ']', '\\\"'), array('&#91;', '&#93;', '\"'), '\$1') . '&quot;]'", $in);

		$tok = ']';
		$out = '[';

		$in = substr($in, 1);
		$close_tags = $error_ary = array();
		$buffer = '';

		do
		{
			$pos = strlen($in);
			for ($i = 0, $tok_len = strlen($tok); $i < $tok_len; ++$i)
			{
				$tmp_pos = strpos($in, $tok[$i]);
				if ($tmp_pos !== false && $tmp_pos < $pos)
				{
					$pos = $tmp_pos;
				}
			}

			$buffer .= substr($in, 0, $pos);
			$tok = $in[$pos];
			$in = substr($in, $pos + 1);

			if ($tok == ']')
			{
				if (strtolower($buffer) == '/quote' && sizeof($close_tags) && substr($out, -1, 1) == '[')
				{
					// we have found a closing tag
					$out .= array_pop($close_tags) . ']';
					$tok = '[';
					$buffer = '';

					/* Add space at the end of the closing tag if not happened before to allow following urls/smilies to be parsed correctly
					* Do not try to think for the user. :/ Do not parse urls/smilies if there is no space - is the same as with other bbcodes too.
					* Also, we won't have any spaces within $in anyway, only adding up spaces -> #10982
					if (!$in || $in[0] !== ' ')
					{
						$out .= ' ';
					}*/
				}
				else if (preg_match('#^quote(?:=&quot;(.*?)&quot;)?$#is', $buffer, $m) && substr($out, -1, 1) == '[')
				{
					$this->parsed_items['quote']++;

					// the buffer holds a valid opening tag
					if ($config['max_quote_depth'] && sizeof($close_tags) >= $config['max_quote_depth'])
					{
						if ($config['max_quote_depth'] == 1)
						{
							// Depth 1 - no nesting is allowed
							$error_ary['quote_depth'] = $user->lang('QUOTE_NO_NESTING');
						}
						else
						{
							// There are too many nested quotes
							$error_ary['quote_depth'] = $user->lang('QUOTE_DEPTH_EXCEEDED', (int) $config['max_quote_depth']);
						}

						$out .= $buffer . $tok;
						$tok = '[]';
						$buffer = '';

						continue;
					}

					array_push($close_tags, '/quote:' . $this->bbcode_uid);

					if (isset($m[1]) && $m[1])
					{
						$username = str_replace(array('&#91;', '&#93;'), array('[', ']'), $m[1]);
						$username = preg_replace('#\[(?!b|i|u|color|url|email|/b|/i|/u|/color|/url|/email)#iU', '&#91;$1', $username);

						$end_tags = array();
						$error = false;

						preg_match_all('#\[((?:/)?(?:[a-z]+))#i', $username, $tags);
						foreach ($tags[1] as $tag)
						{
							if ($tag[0] != '/')
							{
								$end_tags[] = '/' . $tag;
							}
							else
							{
								$end_tag = array_pop($end_tags);
								$error = ($end_tag != $tag) ? true : false;
							}
						}

						if ($error)
						{
							$username = $m[1];
						}

						$out .= 'quote=&quot;' . $username . '&quot;:' . $this->bbcode_uid . ']';
					}
					else
					{
						$out .= 'quote:' . $this->bbcode_uid . ']';
					}

					$tok = '[';
					$buffer = '';
				}
				else if (preg_match('#^quote=&quot;(.*?)#is', $buffer, $m))
				{
					// the buffer holds an invalid opening tag
					$buffer .= ']';
				}
				else
				{
					$out .= $buffer . $tok;
					$tok = '[]';
					$buffer = '';
				}
			}
			else
			{
/**
*				Old quote code working fine, but having errors listed in bug #3572
*
*				$out .= $buffer . $tok;
*				$tok = ($tok == '[') ? ']' : '[]';
*				$buffer = '';
*/

				$out .= $buffer . $tok;

				if ($tok == '[')
				{
					// Search the text for the next tok... if an ending quote comes first, then change tok to []
					$pos1 = stripos($in, '[/quote');
					// If the token ] comes first, we change it to ]
					$pos2 = strpos($in, ']');
					// If the token [ comes first, we change it to [
					$pos3 = strpos($in, '[');

					if ($pos1 !== false && ($pos2 === false || $pos1 < $pos2) && ($pos3 === false || $pos1 < $pos3))
					{
						$tok = '[]';
					}
					else if ($pos3 !== false && ($pos2 === false || $pos3 < $pos2))
					{
						$tok = '[';
					}
					else
					{
						$tok = ']';
					}
				}
				else
				{
					$tok = '[]';
				}
				$buffer = '';
			}
		}
		while ($in);

		$out .= $buffer;

		if (sizeof($close_tags))
		{
			$out .= '[' . implode('][', $close_tags) . ']';
		}

		foreach ($error_ary as $error_msg)
		{
			$this->warn_msg[] = $error_msg;
		}

		return $out;
	}

	/**
	* Validate email
	*/
	function validate_email($var1, $var2)
	{
		$var1 = str_replace("\r\n", "\n", str_replace('\"', '"', trim($var1)));
		$var2 = str_replace("\r\n", "\n", str_replace('\"', '"', trim($var2)));

		$txt = $var2;
		$email = ($var1) ? $var1 : $var2;

		$validated = true;

		if (!preg_match('/^' . get_preg_expression('email') . '$/i', $email))
		{
			$validated = false;
		}

		if (!$validated)
		{
			return '[email' . (($var1) ? "=$var1" : '') . ']' . $var2 . '[/email]';
		}

		$this->parsed_items['email']++;

		if ($var1)
		{
			$retval = '[email=' . $this->bbcode_specialchars($email) . ':' . $this->bbcode_uid . ']' . $txt . '[/email:' . $this->bbcode_uid . ']';
		}
		else
		{
			$retval = '[email:' . $this->bbcode_uid . ']' . $this->bbcode_specialchars($email) . '[/email:' . $this->bbcode_uid . ']';
		}

		return $retval;
	}

	/**
	* Validate url
	*
	* @param string $var1 optional url parameter for url bbcode: [url(=$var1)]$var2[/url]
	* @param string $var2 url bbcode content: [url(=$var1)]$var2[/url]
	*/
	function validate_url($var1, $var2)
	{
		global $config;

		$var1 = str_replace("\r\n", "\n", str_replace('\"', '"', trim($var1)));
		$var2 = str_replace("\r\n", "\n", str_replace('\"', '"', trim($var2)));

		$url = ($var1) ? $var1 : $var2;

		if ($var1 && !$var2)
		{
			$var2 = $var1;
		}

		if (!$url)
		{
			return '[url' . (($var1) ? '=' . $var1 : '') . ']' . $var2 . '[/url]';
		}

		$valid = false;

		$url = str_replace(' ', '%20', $url);

		// Checking urls
		if (preg_match('#^' . get_preg_expression('url') . '$#i', $url) ||
			preg_match('#^' . get_preg_expression('www_url') . '$#i', $url) ||
			preg_match('#^' . preg_quote(generate_board_url(), '#') . get_preg_expression('relative_url') . '$#i', $url))
		{
			$valid = true;
		}

		if ($valid)
		{
			$this->parsed_items['url']++;

			// if there is no scheme, then add http schema
			if (!preg_match('#^[a-z][a-z\d+\-.]*:/{2}#i', $url))
			{
				$url = 'http://' . $url;
			}

			// Is this a link to somewhere inside this board? If so then remove the session id from the url
			if (strpos($url, generate_board_url()) !== false && strpos($url, 'sid=') !== false)
			{
				$url = preg_replace('/(&amp;|\?)sid=[0-9a-f]{32}&amp;/', '\1', $url);
				$url = preg_replace('/(&amp;|\?)sid=[0-9a-f]{32}$/', '', $url);
				$url = append_sid($url);
			}

			return ($var1) ? '[url=' . $this->bbcode_specialchars($url) . ':' . $this->bbcode_uid . ']' . $var2 . '[/url:' . $this->bbcode_uid . ']' : '[url:' . $this->bbcode_uid . ']' . $this->bbcode_specialchars($url) . '[/url:' . $this->bbcode_uid . ']';
		}

		return '[url' . (($var1) ? '=' . $var1 : '') . ']' . $var2 . '[/url]';
	}

	/**
	* Check if url is pointing to this domain/script_path/php-file
	*
	* @param string $url the url to check
	* @return true if the url is pointing to this domain/script_path/php-file, false if not
	*
	* @access private
	*/
	function path_in_domain($url)
	{
		global $config, $phpEx, $user;

		if ($config['force_server_vars'])
		{
			$check_path = $config['script_path'];
		}
		else
		{
			$check_path = ($user->page['root_script_path'] != '/') ? substr($user->page['root_script_path'], 0, -1) : '/';
		}

		// Is the user trying to link to a php file in this domain and script path?
		if (strpos($url, ".{$phpEx}") !== false && strpos($url, $check_path) !== false)
		{
			$server_name = $user->host;

			// Forcing server vars is the only way to specify/override the protocol
			if ($config['force_server_vars'] || !$server_name)
			{
				$server_name = $config['server_name'];
			}

			// Check again in correct order...
			$pos_ext = strpos($url, ".{$phpEx}");
			$pos_path = strpos($url, $check_path);
			$pos_domain = strpos($url, $server_name);

			if ($pos_domain !== false && $pos_path >= $pos_domain && $pos_ext >= $pos_path)
			{
				// Ok, actually we allow linking to some files (this may be able to be extended in some way later...)
				if (strpos($url, '/' . $check_path . '/download/file.' . $phpEx) !== 0)
				{
					return false;
				}

				return true;
			}
		}

		return false;
	}
}
