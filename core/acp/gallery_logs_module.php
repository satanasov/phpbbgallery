<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2025 Leinad4Mind https://leinad4mind.top/forum
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
		$this->language->add_lang(['info_acp_gallery_logs'], 'phpbbgallery/core');

		$this->tpl_name = 'gallery_logs';
		add_form_key('acp_logs');

		$page				= $request->variable('page', 0);
		$filter_log		= $request->variable('lf', 'all');
		$sort_days		= $request->variable('st', 0);
		$sort_key		= $request->variable('sk', 't');
		$sort_dir		= $request->variable('sd', 'd');
		$deletemark		= $request->is_set_post('delmarked');
		$marked			= $request->variable('mark', []);

		$log = $phpbb_container->get('phpbbgallery.core.log');

		$valid_filters   = ['all', 'admin', 'moderator', 'system'];
		$valid_sort_keys = ['u', 't', 'i', 'o'];
		$valid_sort_dirs = ['a', 'd'];

		// Sanitize inputs
		if (!in_array($filter_log, $valid_filters))
		{
			$filter_log = 'all';
		}
		if (!in_array($sort_key, $valid_sort_keys))
		{
			$sort_key = 't';
		}
		if (!in_array($sort_dir, $valid_sort_dirs))
		{
			$sort_dir = 'd';
		}

		// Delete entries if requested and able
		if ($deletemark && $auth->acl_get('a_clearlogs') && !empty($marked))
		{
			if (!check_form_key('acp_logs'))
			{
				trigger_error($this->language->lang('FORM_INVALID'));
			}

			if (confirm_box(true))
			{
				$log->delete_logs($marked);
			}
			else
			{
				confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields([
					'page'		=> $page,
					'delmarked'	=> $deletemark,
					'mark'		=> $marked,
					'st'		=> $sort_days,
					'sk'		=> $sort_key,
					'sd'		=> $sort_dir,
					'i'			=> $id,
					'mode'		=> $mode,
					'action'		=> $this->u_action,
				]));
			}
		}

		switch ($mode)
		{
			case 'main':
				// Template vars based on filter
				$log_titles = [
					'all'      => ['ACP_GALLERY_LOGS', ''],
					'admin'    => ['ACP_LOG_GALLERY_ADM', 'ACP_LOG_GALLERY_ADM_EXP'],
					'moderator'=> ['ACP_LOG_GALLERY_MOD', 'ACP_LOG_GALLERY_MOD_EXP'],
					'system'   => ['ACP_LOG_GALLERY_SYSTEM', 'ACP_LOG_GALLERY_SYSTEM_EXP'],
				];
				$title = $log_titles[$filter_log][0];

				$template->assign_vars([
					'L_TITLE'         => $this->language->lang($log_titles[$filter_log][0]),
					'L_EXPLAIN'       => $log_titles[$filter_log][1] ? $this->language->lang($log_titles[$filter_log][1]) : '',
					'S_SELECT_OPTION' => $filter_log,
				]);

				// Sorting
				$limit_days = [
					0   => $this->language->lang('ALL_ENTRIES'),
					1   => $this->language->lang('1_DAY'),
					7   => $this->language->lang('7_DAYS'),
					14  => $this->language->lang('2_WEEKS'),
					30  => $this->language->lang('1_MONTH'),
					90  => $this->language->lang('3_MONTHS'),
					180 => $this->language->lang('6_MONTHS'),
					365 => $this->language->lang('1_YEAR'),
				];
				$sort_by_text = [
					'u' => $this->language->lang('SORT_USER_ID'),
					't' => $this->language->lang('SORT_DATE'),
					'i' => $this->language->lang('SORT_IP'),
					'o' => $this->language->lang('SORT_ACTION'),
				];

				$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
				gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

				$template->assign_vars(array(
					'S_LIMIT_DAYS'	=> $s_limit_days,
					'S_SORT_KEY'	=> $s_sort_key,
					'S_SORT_DIR'	=> $s_sort_dir,
					'S_CLEARLOGS'	=> $auth->acl_get('a_clearlogs'),
					'U_ACTION'		=> $this->u_action . "&amp;$u_sort_param&amp;page=$page",
				));
				$this->page_title = $this->language->lang($title);

				// Build additional filters
				$additional = [];
				if ($sort_days > 0)
				{
					$additional['sort_days'] = $sort_days;
				}
				if ($sort_key !== 't')
				{
					$additional['sort_key'] = $sort_key;
				}
				if ($sort_dir !== 'd')
				{
					$additional['sort_dir'] = $sort_dir;
				}

				$log->build_list($filter_log, 0, $page, -1, 0, $additional);
				break;

			default:
			trigger_error('NO_MODE', E_USER_ERROR);
		}
	}
}
