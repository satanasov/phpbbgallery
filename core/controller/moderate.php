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
	 * @param \phpbb\auth\auth                                          $auth      Auth object
	 * @param \phpbb\config\config                                      $config    Config object
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db        Database object
	 * @param \phpbb\request\request                                    $request   Request object
	 * @param \phpbb\template\template                                  $template  Template object
	 * @param \phpbb\user                                               $user      User object
	 * @param \phpbb\controller\helper                                  $helper    Controller helper object
	 * @param \phpbbgallery\core\album\display                          $display   Albums display object
	 * @param \phpbbgallery\core\moderate                               $moderate
	 * @param \phpbbgallery\core\auth\auth                              $gallery_auth
	 * @param \phpbbgallery\core\misc                                   $misc
	 * @param \phpbbgallery\core\album\album                            $album
	 * @param \phpbbgallery\core\image\image                            $image
	 * @param \phpbbgallery\core\notification\helper                    $notification_helper
	 * @param \phpbbgallery\core\url                                    $url
	 * @param \phpbbgallery\core\log                                    $gallery_log
	 * @param \phpbbgallery\core\report                                 $report
	 * @param \phpbb\user_loader                                        $user_loader
	 * @param string                                                    $root_path Root path
	 * @param string                                                    $php_ext   php file extension
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request,
	\phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbbgallery\core\album\display $display, \phpbbgallery\core\moderate $moderate,
	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\misc $misc, \phpbbgallery\core\album\album $album, \phpbbgallery\core\image\image $image,
	\phpbbgallery\core\notification\helper $notification_helper, \phpbbgallery\core\url $url, \phpbbgallery\core\log $gallery_log, \phpbbgallery\core\report $report,
	\phpbb\user_loader $user_loader,
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
		$this->user_loader = $user_loader;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Index Controller
	 *    Route: gallery/modarate
	 *
	 * @param int $album_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function base($album_id = 0)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_core_moderate') : $this->helper->route('phpbbgallery_core_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
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
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
			'U_OVERVIEW'					=> true,
		));

		return $this->helper->render('gallery/moderate_overview.html', $this->user->lang('GALLERY'));
	}

	/**
	 * Index Controller
	 *    Route: gallery/modarate/approve
	 *
	 * @param $page
	 * @param $album_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function queue_approve($page, $album_id)
	{
		$approve_ary = $this->request->variable('approval', array('' => array(0)));
		$action_ary = $this->request->variable('action', array('' => 0));
		$back_link = $this->request->variable('back_link', $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_queue_approve'));
		list($action, ) = each($action_ary);

		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_core_moderate') : $this->helper->route('phpbbgallery_core_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
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
					foreach ($approve_ary as $album_id => $approve_array)
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
						$this->moderate->delete_images($delete_array);
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
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
		));
		$this->moderate->build_list($album_id, $page);
		return $this->helper->render('gallery/moderate_approve.html', $this->user->lang('GALLERY'));
	}

	/**
	 * Index Controller
	 *    Route: gallery/modarate/actions
	 *
	 * @param $page
	 * @param $album_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function action_log($page, $album_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_core_moderate') : $this->helper->route('phpbbgallery_core_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
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
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
		));
		$this->gallery_log->build_list('moderator', 0, $page, $album_id);
		return $this->helper->render('gallery/moderate_actions.html', $this->user->lang('GALLERY'));
	}

	/**
	 * Index Controller
	 *    Route: gallery/modarate/reports
	 *
	 * @param $page
	 * @param $album_id
	 * @param $status
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function reports($page, $album_id, $status)
	{
		$report_ary = $this->request->variable('report', array(0));
		$action_ary = $this->request->variable('action', array('' => 0));
		$back_link = $this->request->variable('back_link', $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_reports'));
		list($action, ) = each($action_ary);

		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		if (!empty($report_ary))
		{
			if (confirm_box(true))
			{
				$this->report->close_reports_by_image($report_ary);
				$message = $this->user->lang('WAITING_REPORTED_DONE', count($report_ary));
				$this->url->meta_refresh(3, $back_link);
				trigger_error($message);
			}
			else
			{
				$s_hidden_fields = '<input type="hidden" name="action['.$action.']" value="' . $action . '" />';
				$s_hidden_fields .= '<input type="hidden" name="back_link" value="' . $back_link . '" />';
				foreach ($report_ary as $var)
				{
					$s_hidden_fields .= '<input type="hidden" name="report[]" value="' . $var . '" />';
				}
				confirm_box(false, $this->user->lang['REPORTS_A_CLOSE2_CONFIRM'], $s_hidden_fields);
			}
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_core_moderate') : $this->helper->route('phpbbgallery_core_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
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
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MODERATE_REPORT_CLOSED'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_closed_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_reports_closed'),
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
			'U_STATUS'						=> $status == 1 ? true : false,
		));

		$this->report->build_list($album_id, $page, $this->config['phpbb_gallery_items_per_page'], $status);
		return $this->helper->render('gallery/moderate_reports.html', $this->user->lang('GALLERY'));
	}

	/**
	 * Moderate Controller
	 *    Route: gallery/moderate/{album_id}/list
	 *
	 * @param $album_id
	 * @param $page
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function album_overview($album_id, $page)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');

		$actions_array = $this->request->variable('action', array(0));
		$action = $this->request->variable('select_action', '');
		$back_link = $this->request->variable('back_link', $this->helper->route('phpbbgallery_core_moderate_view', array('album_id' => $album_id)));
		$moving_target = $this->request->variable('moving_target', '');
		if (!empty($actions_array))
		{
			if (confirm_box(true) || $moving_target)
			{
				switch ($action)
				{
					case 'approve':
						$this->image->approve_images($actions_array, $album_id);
						$this->album->update_info($album_id);

						$message = $this->user->lang('WAITING_APPROVED_IMAGE', count($actions_array));
						$this->url->meta_refresh(3, $back_link);
						trigger_error($message);
					break;
					case 'unapprove':
						$this->image->unapprove_images($actions_array, $album_id);
						$this->album->update_info($album_id);

						$message = $this->user->lang('WAITING_UNAPPROVED_IMAGE', count($actions_array));
						$this->url->meta_refresh(3, $back_link);
						trigger_error($message);
					break;
					case 'lock':
						$this->image->lock_images($actions_array, $album_id);
						$this->album->update_info($album_id);

						$message = $this->user->lang('WAITING_LOCKED_IMAGE', count($actions_array));
						$this->url->meta_refresh(3, $back_link);
						trigger_error($message);
					break;
					case 'delete':
						$this->moderate->delete_images($actions_array);
						$this->album->update_info($album_id);

						$message = $this->user->lang('DELETED_IMAGES', count($actions_array));
						$this->url->meta_refresh(3, $back_link);
						trigger_error($message);
					break;
					case 'move':
						$this->image->move_image($actions_array, $moving_target);
						$this->album->update_info($album_id);
						$this->album->update_info($moving_target);

						$message = $this->user->lang('MOVED_IMAGES', count($actions_array));
						$this->url->meta_refresh(3, $back_link);
						trigger_error($message);
					break;
					case 'report':
						$this->report->close_reports_by_image($actions_array);
						$message = $this->user->lang('WAITING_REPORTED_DONE', count($actions_array));
						$this->url->meta_refresh(3, $back_link);
						trigger_error($message);
					break;
				}
			}
			else
			{
				$s_hidden_fields = '<input type="hidden" name="select_action" value="' . $action . '" />';
				$s_hidden_fields .= '<input type="hidden" name="back_link" value="' . $back_link . '" />';
				foreach ($actions_array as $var)
				{
					$s_hidden_fields .= '<input type="hidden" name="action[]" value="' . $var . '" />';
				}
				if ($action == 'report')
				{
					confirm_box(false, $this->user->lang['REPORT_A_CLOSE2_CONFIRM'], $s_hidden_fields);
				}
				if ($action == 'move')
				{
					$category_select = $this->album->get_albumbox(false, 'moving_target', $album_id, 'm_move', $album_id);
					$this->template->assign_vars(array(
						'S_MOVING_IMAGES'	=> true,
						'S_ALBUM_SELECT'	=> $category_select,
						'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
					));
					return $this->helper->render('gallery/mcp_body.html', $this->user->lang('GALLERY'));
				}
				else
				{
					confirm_box(false, $this->user->lang['QUEUES_A_' . strtoupper($action) . '2_CONFIRM'], $s_hidden_fields);
				}
			}
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$album_backlink = $album_id === 0 ? $this->helper->route('phpbbgallery_core_moderate') : $this->helper->route('phpbbgallery_core_moderate_album', array('album_id'	=> $album_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
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
			'U_GALLERY_MODERATE_OVERVIEW'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate'),
			'U_GALLERY_MODERATE_APPROVE'	=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_queue_approve_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_queue_approve'),
			'U_GALLERY_MODERATE_REPORT'		=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_reports_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_reports'),
			'U_ALBUM_OVERVIEW'				=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_view', array('album_id' => $album_id)) : false,
			'U_GALLERY_MCP_LOGS'			=> $album_id > 0 ? $this->helper->route('phpbbgallery_core_moderate_action_log_album', array('album_id' => $album_id)) : $this->helper->route('phpbbgallery_core_moderate_action_log'),
			'U_ALBUM_NAME'					=> $album_id > 0 ? $album['album_name'] : false,
		));
		$this->moderate->album_overview($album_id, $page);
		return $this->helper->render('gallery/moderate_album_overview.html', $this->user->lang('GALLERY'));
	}

	/**
	 * Index Controller
	 *    Route: gallery/modarate/image/{image_id}
	 *
	 * @param $image_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function image($image_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('mcp');
		$quick_action = $this->request->variable('action', '');

		// If we have quick mode (EDIT, DELETE) just send us to the page we need
		switch ($quick_action)
		{
			case 'images_move':
				$route = $this->helper->route('phpbbgallery_core_moderate_image_move', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'image_edit':
				$route = $this->helper->route('phpbbgallery_core_image_edit', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_unapprove':
				$route = $this->helper->route('phpbbgallery_core_moderate_image_unapprove', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_approve':
				$route = $this->helper->route('phpbbgallery_core_moderate_image_approve', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_lock':
				$route = $this->helper->route('phpbbgallery_core_moderate_image_lock', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'images_delete':
				$route = $this->helper->route('phpbbgallery_core_image_delete', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
			case 'reports_close':
				if (confirm_box(true))
				{
					$back_link =  $this->helper->route('phpbbgallery_core_moderate_image', array('image_id' => $image_id));
					$this->report->close_reports_by_image($image_id);
					$message = $this->user->lang('WAITING_REPORTED_DONE', 1);
					$this->url->meta_refresh(3, $back_link);
					trigger_error($message);
				}
				else
				{
					$s_hidden_fields = '<input type="hidden" name="action" value="reports_close" />';
					confirm_box(false, $this->user->lang['REPORT_A_CLOSE2_CONFIRM'], $s_hidden_fields);
				}
			break;
			case 'reports_open':
				$route = $this->helper->route('phpbbgallery_core_image_report', array('image_id'	=> $image_id));
				redirect($this->url->get_uri($route));
			break;
		}
		$image_data = $this->image->get_image_data($image_id);
		$album_data = $this->album->get_info($image_data['image_album_id']);
		$users_array = $report_data = array();
		$open_report = false;
		$report_data = $this->report->get_data_by_image($image_id);
		foreach ($report_data as $var)
		{
			$users_array[$var['reporter_id']] = array('');
			$users_array[$var['report_manager']] = array('');
			if ($var['report_status'] == 1)
			{
				$open_report = true;
			}
		}
		$users_array[$image_data['image_user_id']] = array('');
		$this->user_loader->load_users(array_keys($users_array));
		// Now let's get some ACL
		$select_select = '<option value="" selected="selected">' . $this->user->lang('CHOOSE_ACTION') . '</option>';
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if ($this->gallery_auth->acl_check('m_status', $album_data['album_id'], $album_data['album_user_id']))
		{
			if ($image_data['image_status'] == 0)
			{
				$select_select .= '<option value="images_approve">' . $this->user->lang('QUEUE_A_APPROVE') . '</option>';
				$select_select .= '<option value="images_lock">' . $this->user->lang('QUEUE_A_LOCK') . '</option>';
			}
			if ($image_data['image_status'] == 1)
			{
				$select_select .= '<option value="images_unapprove">' . $this->user->lang('QUEUE_A_UNAPPROVE') . '</option>';
				$select_select .= '<option value="images_lock">' . $this->user->lang('QUEUE_A_LOCK') . '</option>';
			}
			else
			{
				$select_select .= '<option value="images_approve">' . $this->user->lang('QUEUE_A_APPROVE') . '</option>';
				$select_select .= '<option value="images_unapprove">' . $this->user->lang('QUEUE_A_UNAPPROVE') . '</option>';
			}
		}
		if ($this->gallery_auth->acl_check('m_delete', $album_data['album_id'], $album_data['album_user_id']))
		{
			$select_select .= '<option value="images_delete">' . $this->user->lang('QUEUE_A_DELETE') . '</option>';
		}
		if ($this->gallery_auth->acl_check('m_move', $album_data['album_id'], $album_data['album_user_id']))
		{
			$select_select .= '<option value="images_move">' . $this->user->lang('QUEUES_A_MOVE') . '</option>';
		}
		if ($this->gallery_auth->acl_check('m_report', $album_data['album_id'], $album_data['album_user_id']))
		{
			if ($open_report)
			{
				$select_select .= '<option value="reports_close">' . $this->user->lang('REPORT_A_CLOSE') . '</option>';
			}
			else
			{
				$select_select .= '<option value="reports_open">' . $this->user->lang('REPORT_A_OPEN') . '</option>';
			}
		}
		$this->template->assign_vars(array(
			'ALBUM_NAME'		=> $album_data['album_name'],
			'U_VIEW_ALBUM'		=> $this->helper->route('phpbbgallery_core_moderate_album', array('album_id' => $image_data['image_album_id'])),
			'U_EDIT_IMAGE'		=> $this->helper->route('phpbbgallery_core_image_edit', array('image_id'	=> $image_id)),
			'U_DELETE_IMAGE'	=> $this->helper->route('phpbbgallery_core_image_delete', array('image_id'	=> $image_id)),
			'IMAGE_NAME'		=> $image_data['image_name'],
			'IMAGE_TIME'		=> $this->user->format_date($image_data['image_time']),
			'UPLOADER'			=> $this->user_loader->get_username($image_data['image_user_id'], 'full'),
			'U_MOVE_IMAGE'		=> $this->helper->route('phpbbgallery_core_moderate_image_move', array('image_id'	=> $image_id)),
			'STATUS'			=> $this->user->lang['QUEUE_STATUS_' . $image_data['image_status']],
			'UC_IMAGE'			=> $this->image->generate_link('medium', $this->config['phpbb_gallery_link_thumbnail'], $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
			'IMAGE_DESC'		=> generate_text_for_display($image_data['image_desc'], $image_data['image_desc_uid'], $image_data['image_desc_bitfield'], 7),
			'U_SELECT'			=> $select_select,
			'S_MCP_ACTION'		=> $this->helper->route('phpbbgallery_core_moderate_image', array('image_id' => $image_id)),
		));
		foreach ($report_data as $var)
		{
			$this->template->assign_block_vars('reports', array(
				'REPORTER'		=> $this->user_loader->get_username($var['reporter_id'], 'full'),
				'REPORT_TIME'	=> $this->user->format_date($var['report_time']),
				'REPORT_NOTE'	=> $var['report_note'],
				'STATUS'		=> $var['report_status'],
				'MANAGER'		=> $var['report_manager'] != 0 ?  $this->user_loader->get_username($var['report_manager'], 'full') : false,
			));
		}
		return $this->helper->render('gallery/moderate_image_overview.html', $this->user->lang('GALLERY'));
	}

	/**
	 * Index Controller
	 *    Route: gallery/modarate/image/{image_id}/approve
	 *
	 * @param $image_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function approve($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_data = $this->album->get_info($image_data['image_album_id']);

		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id' => $image_data['image_album_id']));
		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
		$meta_refresh_time = 2;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_status', $image_data['image_album_id'], $album_data['album_user_id']))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		$action_ary = $this->request->variable('action', array('' => 0));
		list($action, ) = each($action_ary);

		if ($action == 'disapprove')
		{
			redirect($this->helper->route('phpbbgallery_core_image_delete', array('image_id'	=> $image_id)));
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
					'S_CONFIRM_ACTION'	=> $this->helper->route('phpbbgallery_core_moderate_image_approve', array('image_id' => $image_id)),
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
	 *    Route: gallery/modarate/image/{image_id}/unapprove
	 *
	 * @param $image_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function unapprove($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_data = $this->album->get_info($image_data['image_album_id']);

		$album_backlink = $this->helper->route('phpbbgallery_core_index');
		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
		$meta_refresh_time = 2;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_status', $image_data['image_album_id'], $album_data['album_user_id']))
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
	 *    Route: gallery/modarate/image/{image_id}/move
	 *
	 * @param $image_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function move($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$user_id = $image_data['image_user_id'];
		$album_data =  $this->album->get_info($album_id);
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id));
		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
		$meta_refresh_time = 2;
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_move', $image_data['image_album_id'], $album_data['album_user_id']))
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
			$category_select = $this->album->get_albumbox(false, 'moving_target', $album_id, 'm_move', $album_id);
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
	 *    Route: gallery/modarate/image/{image_id}/lock
	 *
	 * @param $image_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function lock($image_id)
	{
		$image_data = $this->image->get_image_data($image_id);
		$album_id = $image_data['image_album_id'];
		$user_id = $image_data['image_user_id'];
		$album_data =  $this->album->get_info($album_id);
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id));
		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_loginlink = append_sid($this->root_path . 'ucp.' . $this->php_ext . '?mode=login');
		$meta_refresh_time = 2;
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery_mcp'));
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('m_status', $image_data['image_album_id'], $album_data['album_user_id']))
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
