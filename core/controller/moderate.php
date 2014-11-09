<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

class moderate
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\db\driver\driver */
	protected $db;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbbgallery\core\album\display */
	protected $display;

	/* @var string */
	protected $root_path;

	/* @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth		Auth object
	* @param \phpbb\config\config		$config		Config object
	* @param \phpbb\db\driver\driver	$db			Database object
	* @param \phpbb\request\request		$request	Request object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbbgallery\core\album\display	$display	Albums display object
	* @param string						$root_path	Root path
	* @param string						$php_ext	php file extension
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, 
	\phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbbgallery\core\album\display $display, \phpbbgallery\core\moderate $moderate, 
	$root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->display = $display;
		$this->moderate = $moderate;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Index Controller
	*	Route: gallery/modarate
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function base()
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->display->display_albums(false, $this->config['load_moderators']);

		// This is the overview page, so we will need to create some queries
		// We will use the special moderate helper
		
		$this->moderate->build_queue('short', 'report_image_open');

		return $this->helper->render('gallery/moderate_overview.html', $this->user->lang('GALLERY'));
	}
	
	/**
	* Index Controller
	*	Route: gallery/modarate/image/{image_id}
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function image($image_id)
	{
		$quick_action = $this->request->variable('action', '');

		// If we have quick mode (EDIT, DELETE) just send us to the page we need
		switch($quick_action)
		{
			case 'image_edit':
				redirect('gallery/image/' . $image_id . '/edit');
			break;
			case 'image_delete':
				redirect('gallery/image/' . $image_id . '/delete');
			break;
		}
		
		return $this->helper->render('gallery/moderate_overview.html', $this->user->lang('GALLERY'));
	}
}
