<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\auth;

class level
{

	/**
	* Gallery Auth Object
	* @var \phpbbgallery\core\auth\auth
	*/
	protected $auth;

	/**
	* Config Object
	* @var \phpbb\config\config
	*/
	protected $config;

	/**
	* Template Object
	* @var \phpbb\template\template
	*/
	protected $template;

	/**
	* User Object
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* Construct
	*
	* @param \phpbbgallery\core\auth\auth	$auth		Gallery Auth Object
	* @param \phpbb\config\config			$config		Config Object
	* @param \phpbb\template\template		$template	Template Object
	* @param \phpbb\user					$user		User Object
	*/
	public function __construct(\phpbbgallery\core\auth\auth $auth, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* User authorisation levels output
	*
	* @param	int		$album_id		The current album the user is in.
	* @param	int		$album_status	The albums status bit.
	* @param	int		$album_user_id	The user-id of the album owner. Saves us a call to the cache if it is set.
	* @return		null
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: gen_forum_auth_level
	*/
	public function display($album_id, $album_status, $album_user_id = -1)
	{
		$locked = ($album_status == ITEM_LOCKED && !$this->auth->acl_check('m_', $album_id, $album_user_id)) ? true : false;

		$rules = array(
			($this->auth->acl_check('i_view', $album_id, $album_user_id) && !$locked) ? $this->user->lang['ALBUM_VIEW_CAN'] : $this->user->lang['ALBUM_VIEW_CANNOT'],
			($this->auth->acl_check('i_upload', $album_id, $album_user_id) && !$locked) ? $this->user->lang['ALBUM_UPLOAD_CAN'] : $this->user->lang['ALBUM_UPLOAD_CANNOT'],
			($this->auth->acl_check('i_edit', $album_id, $album_user_id) && !$locked) ? $this->user->lang['ALBUM_EDIT_CAN'] : $this->user->lang['ALBUM_EDIT_CANNOT'],
			($this->auth->acl_check('i_delete', $album_id, $album_user_id) && !$locked) ? $this->user->lang['ALBUM_DELETE_CAN'] : $this->user->lang['ALBUM_DELETE_CANNOT'],
		);
		if ($this->config['phpbb_gallery_allow_comments'] && $this->auth->acl_check('c_read', $album_id, $album_user_id))
		{
			$rules[] = ($this->auth->acl_check('c_post', $album_id, $album_user_id) && !$locked) ? $this->user->lang['ALBUM_COMMENT_CAN'] : $this->user->lang['ALBUM_COMMENT_CANNOT'];
		}
		if ($this->config['phpbb_gallery_allow_rates'])
		{
			$rules[] = ($this->auth->acl_check('i_rate', $album_id, $album_user_id) && !$locked) ? $this->user->lang['ALBUM_RATE_CAN'] : $this->user->lang['ALBUM_RATE_CANNOT'];
		}

		foreach ($rules as $rule)
		{
			$this->template->assign_block_vars('rules', array('RULE' => $rule));
		}
	}
}
