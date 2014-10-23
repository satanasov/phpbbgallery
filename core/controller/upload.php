<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

class upload
{
	/* @var \phpbb\user */
	protected $user;
	
	/* @var \phpbbgallery\core\misc */
	protected $misc;
	
	/* @var \phpbbgallery\core\album\album */
	protected $album;
	
	/* @var \phpbbgallery\core\album\album */
	protected $display;
	
	/**
	* Constructor
	*
	* @param \phpbb\user				$user		User object
	* @param \phpbbgallery\core\misc	$misc		Misc class
	* @param \phpbbgallery\core\album\album	$album	Album class
	* @param \phpbbgallery\core\album\display	$display	Display class
	*/
	
	public function __construct(\phpbb\user $user, \phpbbgallery\core\album\album $album, \phpbbgallery\core\misc $misc, \phpbbgallery\core\auth\auth $auth, \phpbbgallery\core\album\display $display, \phpbb\controller\helper $helper)
	{
		$this->user = $user;
		$this->album = $album;
		$this->misc = $misc;
		$this->auth = $auth;
		$this->display = $display;
		$this->helper = $helper;
	}
	
	public function upload($album_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$album_data = $this->album->get_info($album_id);
		$this->display->generate_navigation($album_data);
		$album_backlink = './index.php';
		$album_loginlink;
		//Let's get authorization
		if ($this->auth->acl_check('i_upload', $album_id, $album_data['album_user_id']) || ($album_data['album_status'] == $this->album->status_locked()))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		return $this->helper->render('gallery/album_body.html', $page_title);
	}
}