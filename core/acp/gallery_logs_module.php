<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace phpbbgallery\core\acp;

/**
* @package acp
*/
class gallery_logs_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $auth, $template, $user, $request;
		global $phpbb_container;

		$this->language = $phpbb_container->get('language');

		$user->add_lang(array('info_acp_gallery_logs'), 'phpbbgallery/core');
		$this->tpl_name = 'gallery_logs';
		add_form_key('acp_logs');
		$page = $request->variable('page', 0);
		$filter_log = $request->variable('lf', 'all');
		$sort_days	= $request->variable('st', 0);
		$sort_key	= $request->variable('sk', 't');
		$sort_dir	= $request->variable('sd', 'd');
		$deletemark = $request->variable('delmarked', false, false, \phpbb\request\request_interface::POST);
		$marked		= $request->variable('mark', array(0));
		$log = $phpbb_container->get('phpbbgallery.core.log');

		// Delete entries if requested and able
		if (($deletemark) && $auth->acl_get('a_clearlogs'))
		{
			if (confirm_box(true))
			{
				$log->delete_logs($marked);
			}
			else
			{
				confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields(array(
					'page'		=> $page,
					'delmarked'	=> $deletemark,
					'mark'		=> $marked,
					'st'		=> $sort_days,
					'sk'		=> $sort_key,
					'sd'		=> $sort_dir,
					'i'			=> $id,
					'mode'		=> $mode,
					'action'	=> $this->u_action,
				)));
			}
		}
		switch ($mode)
		{
			case 'main':
				switch ($filter_log)
				{
					case 'all':
						$title = 'ACP_GALLERY_LOGS';
						$template->assign_vars(array(
							'L_TITLE'	=> $this->language->lang('ACP_GALLERY_LOGS'),
							'L_EXPLAIN'	=> '',
							'S_SELECT_OPTION'	=> 'all'
						));
					break;
					case 'admin':
						$title = 'ACP_LOG_GALLERY_ADM';
						$template->assign_vars(array(
							'L_TITLE'	=> $this->language->lang('ACP_LOG_GALLERY_ADM'),
							'L_EXPLAIN'	=> $this->language->lang('ACP_LOG_GALLERY_ADM_EXP'),
							'S_SELECT_OPTION'	=> 'admin'
						));
					break;
					case 'moderator':
						$title = 'ACP_LOG_GALLERY_MOD';
						$template->assign_vars(array(
							'L_TITLE'	=> $this->language->lang('ACP_LOG_GALLERY_MOD'),
							'L_EXPLAIN'	=> $this->language->lang('ACP_LOG_GALLERY_MOD_EXP'),
							'S_SELECT_OPTION'	=> 'moderator'
						));
					break;
					case 'system':
						$title = 'ACP_LOG_GALLERY_SYSTEM';
						$template->assign_vars(array(
							'L_TITLE'	=> $this->language->lang('ACP_LOG_GALLERY_SYSTEM'),
							'L_EXPLAIN'	=> $this->language->lang('ACP_LOG_GALLERY_SYSTEM_EXP'),
							'S_SELECT_OPTION'	=> 'system'
						));
					break;
				}
				$limit_days = array(0 => $this->language->lang('ALL_ENTRIES'), 1 => $this->language->lang('1_DAY'), 7 => $this->language->lang('7_DAYS'), 14 => $this->language->lang('2_WEEKS'), 30 => $this->language->lang('1_MONTH'), 90 => $this->language->lang('3_MONTHS'), 180 => $this->language->lang('6_MONTHS'), 365 => $this->language->lang('1_YEAR'));
				$sort_by_text = array('u' => $this->language->lang('SORT_USER_ID'), 't' => $this->language->lang('SORT_DATE'), 'i' => $this->language->lang('SORT_IP'), 'o' => $this->language->lang('SORT_ACTION'));
				$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
				gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

				$template->assign_vars(array(
					'S_LIMIT_DAYS'	=> $s_limit_days,
					'S_SORT_KEY'	=> $s_sort_key,
					'S_SORT_DIR'	=> $s_sort_dir,
					'S_CLEARLOGS'	=> $auth->acl_get('a_clearlogs'),
					'U_ACTION'	=> $this->u_action . "&amp;$u_sort_param&amp;page=$page",
				));
				$this->page_title = $this->language->lang($title);
				// Let's build some additional parameters for the log
				$additional = array();
				if ($sort_days > 0)
				{
					$additional['sort_days'] = $sort_days;
				}
				if ($sort_key != 't')
				{
					$additional['sort_key'] = $sort_key;
				}
				if ($sort_dir != 'd')
				{
					$additional['sort_dir'] = $sort_dir;
				}
				$log->build_list($filter_log, 25, ($page/25) + 1, -1, 0, $additional);
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}
}
