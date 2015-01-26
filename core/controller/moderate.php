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
	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\misc $misc, \phpbbgallery\core\album\album $album, \phpbbgallery\core\image\image $image,
	\phpbbgallery\core\notification\helper $notification_helper, \phpbbgallery\core\url $url, \phpbbgallery\core\log $gallery_log, \phpbbgallery\core\report $report,
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
		$this->gallery_auth = $gallery_auth;
		$this->misc = $misc;
		$this->album = $album;
		$this->image = $image;
		$this->notification_helper = $notification_helper;
		$this->url = $url;
		$this->gallery_log = $gallery_log;
		$this->report = $report;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Index Controller
	*	Route: gallery/modarate
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function base($album_id = 0)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_moderate') : $this->helper->route('phpbbgallery_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid('/ucp.php?mode=login');
		if ($album_id === 0)
		{
			if (!$this->gallery_auth->acl_check_global('m_'))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		else
		{
			$album = $this->album->get_info($album_id);
			if (!$this->gallery_auth->acl_check('m_', $album['album_id'], $album['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');
		$this->display->display_albums(false, $this->config['load_moderators']);
		// This is the overview page, so we will need to create some queries
		// We will use the special moderate helper

		$this->report->build_list($album_id, 1, 5);
		$this->moderate->build_list($album_id, 1, 5);
		$this->gallery_log->build_list('moderator', 5, 1, $album_id);

		$this->template->assign_vars(array(
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_reports'),
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
			'U_OVERVIEW'					=> true,
		));

		return $this->helper->render('gallery/moderate_overview.html', $this->user->lang('GALLERY'));
	}

	/**
	* Index Controller
	*	Route: gallery/modarate/approve
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function queue_approve($page, $album_id)
	{
		$approve_ary = $this->request->variable('approval', array('' => array(0)));
		$action_ary = $this->request->variable('action', array('' => 0));
		$back_link = $this->request->variable('back_link', $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_queue_approve'));
		list($action, ) = each($action_ary);

		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_moderate') : $this->helper->route('phpbbgallery_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid('/ucp.php?mode=login');
		if ($album_id === 0)
		{
			if (!$this->gallery_auth->acl_check_global('m_status'))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		else
		{
			$album = $this->album->get_info($album_id);
			if (!$this->gallery_auth->acl_check('m_status', $album['album_id'], $album['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		if (!empty($approve_ary))
		{
			if (confirm_box(true))
			{
				if ($action == 'approve')
				{
					$count = 0;
					foreach($approve_ary as $album_id => $approve_array)
					{
						$this->image->approve_images($approve_array, $album_id);
						$this->album->update_info($album_id);
						$count = $count + count($approve_array);
					}

					$message = $this->user->lang('WAITING_APPROVED_IMAGE', $count);
					$this->url->meta_refresh(3, $back_link);
					trigger_error($message);
				}
				if ($action == 'disapprove')
				{
					$count = 0;
					foreach ($approve_ary as $album_id => $delete_array)
					{
						// Let's load info for images, so we can
						$filenames = $this->image->get_filenames($delete_array);
						// Let's log the action
						foreach ($filenames as $name)
						{
							$this->gallery_log->add_log('moderator', 'disapprove', $album_id, 0, array('LOG_GALLERY_DISAPPROVED', $name));
						}
						$this->image->delete_images($delete_array);
						$count = $count + count($delete_array);
					}
					$message = $this->user->lang('WAITING_DISPPROVED_IMAGE', $count);
					$this->url->meta_refresh(3, $back_link);
					trigger_error($message);
				}
			}
			else
			{
				$s_hidden_fields = '<input type="hidden" name="action['.$action.']" value="' . $action . '" />';
				$s_hidden_fields .= '<input type="hidden" name="back_link" value="' . $back_link . '" />';
				foreach ($approve_ary as $id => $var)
				{
					foreach ($var as $var1)
					{
						$s_hidden_fields .= '<input type="hidden" name="approval[' . $id . '][]" value="' . $var1 . '" />';
					}
				}
				confirm_box(false, $this->user->lang['QUEUES_A_' . strtoupper($action) . '2_CONFIRM'], $s_hidden_fields);
			}
		}

		$this->template->assign_vars(array(
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
		));
		$this->moderate->build_list($album_id, $page);
		return $this->helper->render('gallery/moderate_approve.html', $this->user->lang('GALLERY'));
	}

	/**
	* Index Controller
	*	Route: gallery/modarate/actions
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function action_log($page, $album_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_moderate') : $this->helper->route('phpbbgallery_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid('/ucp.php?mode=login');
		if ($album_id === 0)
		{
			if (!$this->gallery_auth->acl_check_global('m_'))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		else
		{
			$album = $this->album->get_info($album_id);
			if (!$this->gallery_auth->acl_check('m_', $album['album_id'], $album['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		$this->template->assign_vars(array(
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
		));
		$this->gallery_log->build_list('moderator', 0, $page, $album_id);
		return $this->helper->render('gallery/moderate_actions.html', $this->user->lang('GALLERY'));
	}
	/**
	* Index Controller
	*	Route: gallery/modarate/reports
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function reports($page, $album_id, $status)
	{
		$report_ary = $this->request->variable('report', array(0));
		$action_ary = $this->request->variable('action', array('' => 0));
		$back_link = $this->request->variable('back_link', $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_reports'));
		$count = $this->request->variable('count', 0);
		list($action, ) = each($action_ary);

		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		if (!empty($report_ary))
		{
			if (confirm_box(true))
			{
				$this->report->close_reports($report_ary);
				$message = $this->user->lang('WAITING_REPORTED_DONE', $count);
				$this->url->meta_refresh(3, $back_link);
				trigger_error($message);
			}
			else
			{
				$s_hidden_fields = '<input type="hidden" name="action['.$action.']" value="' . $action . '" />';
				$s_hidden_fields .= '<input type="hidden" name="back_link" value="' . $back_link . '" />';
				$count = 0;
				foreach ($report_ary as $var)
				{
					$s_hidden_fields .= '<input type="hidden" name="report[]" value="' . $var . '" />';
					$count ++;
				}
				$s_hidden_fields .= '<input type="hidden" name="count" value="' . $count . '" />';
				confirm_box(false, $this->user->lang['REPORTS_A_CLOSE2_CONFIRM'], $s_hidden_fields);
			}
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_moderate') : $this->helper->route('phpbbgallery_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid('/ucp.php?mode=login');
		if ($album_id === 0)
		{
			if (!$this->gallery_auth->acl_check_global('m_report'))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		else
		{
			$album = $this->album->get_info($album_id);
			if (!$this->gallery_auth->acl_check('m_report', $album['album_id'], $album['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}

		$this->template->assign_vars(array(
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MODERATE_REPORT_CLOSED'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_reports_closed_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_reports_closed'),
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
			'U_STATUS'						=> $status == 1 ? true : false,
		));

		$this->report->build_list($album_id, $page, $this->config['phpbb_gallery_items_per_page'], $status);
		return $this->helper->render('gallery/moderate_reports.html', $this->user->lang('GALLERY'));
	}

	/**
	* Moderate Controller
	* 	Route: gallery/moderate/{album_id}/list
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function album_overview($album_id, $page)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_moderate') : $this->helper->route('phpbbgallery_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid('/ucp.php?mode=login');
		if ($album_id === 0)
		{
			if (!$this->gallery_auth->acl_check_global('m_'))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		else
		{
			$album = $this->album->get_info($album_id);
			if (!$this->gallery_auth->acl_check('m_', $album['album_id'], $album['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		$this->template->assign_vars(array(
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
		));
		$this->moderate->album_overview($album_id, $page, $this->config['phpbb_gallery_items_per_page'], $status);
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
			case 'images_move':
				$route = $this->helper->route('phpbbgallery_moderate_image_move', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'image_edit':
				$route = $this->helper->route('phpbbgallery_image_edit', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_unapprove':
				$route = $this->helper->route('phpbbgallery_moderate_image_unapprove', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_approve':
				$route = $this->helper->route('phpbbgallery_moderate_image_approve', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_lock':
				$route = $this->helper->route('phpbbgallery_moderate_image_lock', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_delete':
				$route = $this->helper->route('phpbbgallery_image_delete', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
		}

		return $this->helper->render('gallery/moderate_overview.html', $this->user->lang('GALLERY'));
	}

	/**
	* Index Controller
	*	Route: gallery/modarate/image/{image_id}/approve
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function approve($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_data = $this->album->get_info($image_data['image_album_id']);

		$album_backlink = $this->helper->route('phpbbgallery_album', array('album_id' => $image_data['image_album_id']));
		$image_backlink = $this->helper->route('phpbbgallery_image', array('image_id' => $image_id));
		$album_loginlink = append_sid('/ucp.php?mode=login');
		$meta_refresh_time = 2;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_status', $image_data['image_album_id'], $album_data))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		$action_ary = $this->request->variable('action', array('' => 0));
		list($action, ) = each($action_ary);

		if ($action == 'disapprove')
		{
			redirect($this->helper->route('phpbbgallery_image_delete', array('image_id'	=> $image_id)));
		}
		$show_notify = true;
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');
			if (confirm_box(true))
			{
				$np = $this->request->variable('notify_poster', '');
				$notify_poster = ($action == 'approve' && $np);
				$image_id_ary = array($image_id);
				$this->image->approve_images($image_id_ary, $album_data['album_id']);
				$this->album->update_info($album_data['album_id']);
				// So we need to see if there are still unapproved images in the album
				$this->notification_helper->read('approval', $album_data['album_id']);
				$message = sprintf($this->user->lang['WAITING_APPROVED_IMAGE'][1]);
				meta_refresh($meta_refresh_time, $image_backlink);
				trigger_error($message);
			}
			else
			{
				$this->template->assign_vars(array(
					'S_NOTIFY_POSTER'			=> $show_notify,
					'S_' . strtoupper($action)	=> true,
					'S_CONFIRM_ACTION'	=> $this->helper->route('phpbbgallery_moderate_image_approve', array('image_id' => $image_id)),
				));
				$action_msg = $this->user->lang['QUEUES_A_APPROVE2_CONFIRM'];
				$s_hidden_fields = build_hidden_fields(array(
					'action'		=> 'approve',
				));
				confirm_box(false, $action_msg, $s_hidden_fields, 'mcp_approve.html');
			}

		return $this->helper->render('gallery/moderate_overview.html', $this->user->lang('GALLERY'));
	}

	/**
	* Index Controller
	*	Route: gallery/modarate/image/{image_id}/unapprove
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function unapprove($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_data = $this->album->get_info($image_data['image_album_id']);

		$album_backlink = append_sid('/gallery');
		$image_backlink = append_sid('/gallery/image/' . $image_id);
		$album_loginlink = append_sid('/ucp.php?mode=login');
		$meta_refresh_time = 2;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_status', $image_data['image_album_id'], $album_data))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}

		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');
		if (confirm_box(true))
		{
			$image_id_ary = array($image_id);
			$this->image->unapprove_images($image_id_ary, $album_data['album_id']);
			// To DO - add notification
			$message = sprintf($this->user->lang['WAITING_UNAPPROVED_IMAGE'][1]);
			meta_refresh($meta_refresh_time, $image_backlink);
			trigger_error($message);
		}
		else
		{
			$s_hidden_fields = '';
			confirm_box(false, 'QUEUE_A_UNAPPROVE2', $s_hidden_fields);
		}
	}

	/**
	* Index Controller
	*	Route: gallery/modarate/image/{image_id}/move
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function move($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$user_id = $image_data['image_user_id'];
		$album_data =  $this->album->get_info($album_id);
		$album_backlink = append_sid('/gallery/' . $album_id);
		$image_backlink = append_sid('/gallery/image/' . $image_id);
		$album_loginlink = append_sid('/ucp.php?mode=login');
		$meta_refresh_time = 2;
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_move', $image_data['image_album_id'], $album_data))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		$moving_target = $this->request->variable('moving_target', '');

		if ($moving_target)
		{
			$target = array($image_id);
			$this->image->move_image($target, $moving_target);
			$message = sprintf($this->user->lang['IMAGES_MOVED'][1]);
			$this->album->update_info($album_id);
			$this->album->update_info($moving_target);
			meta_refresh($meta_refresh_time, $image_backlink);
			trigger_error($message);
		}
		else
		{
			$category_select = $this->album->get_albumbox(false, 'moving_target', $album_id, 'i_upload', $album_id);
			$this->template->assign_vars(array(
				'S_MOVING_IMAGES'	=> true,
				'S_ALBUM_SELECT'	=> $category_select,
				//'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
			));
		}

		return $this->helper->render('gallery/mcp_body.html', $this->user->lang('GALLERY'));
	}

	/**
	* Index Controller
	*	Route: gallery/modarate/image/{image_id}/lock
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function lock($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$user_id = $image_data['image_user_id'];
		$album_data =  $this->album->get_info($album_id);
		$album_backlink = append_sid('/gallery/' . $album_id);
		$image_backlink = append_sid('/gallery/image/' . $image_id);
		$album_loginlink = append_sid('/ucp.php?mode=login');
		$meta_refresh_time = 2;
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_status', $image_data['image_album_id'], $album_data))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		if (confirm_box(true))
		{
			$image_id_ary = array($image_id);
			$this->image->lock_images($image_id_ary, $album_data['album_id']);
			// To DO - add notification
			$message = sprintf($this->user->lang['WAITING_LOCKED_IMAGE'][1]);
			meta_refresh($meta_refresh_time, $image_backlink);
			trigger_error($message);
		}
		else
		{
			$s_hidden_fields = '';
			confirm_box(false, 'QUEUE_A_LOCK2', $s_hidden_fields);
		}
	}
}
