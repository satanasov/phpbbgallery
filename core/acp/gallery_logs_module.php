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
		global $auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path, $phpbb_ext_gallery, $table_prefix, $phpbb_dispatcher, $request;
		global $phpbb_container;
		
		$user->add_lang_ext('phpbbgallery/core', array('info_acp_gallery_logs'));
		$this->tpl_name = 'gallery_logs';
		add_form_key('acp_logs');
		$page = $request->variable('page', 1);
		$filter_log = $request->variable('lf', 'all');
		$log = $phpbb_container->get('phpbbgallery.core.log');

		

		switch ($mode)
		{
			case 'main':
				switch ($filter_log)
				{
					case 'all':
						$title = 'ACP_GALLERY_LOGS';
						$template->assign_vars(array(
							'L_TITLE'	=> $user->lang('ACP_GALLERY_LOGS'),
							'L_EXPLAIN'	=> '',
							'S_SELECT_OPTION'	=> 'all'
						));
					break;
					case 'admin':
						$title = 'ACP_LOG_GALLERY_ADM';
						$template->assign_vars(array(
							'L_TITLE'	=> $user->lang('ACP_LOG_GALLERY_ADM'),
							'L_EXPLAIN'	=> $user->lang('ACP_LOG_GALLERY_ADM_EXP'),
							'S_SELECT_OPTION'	=> 'admin'
						));
					break;
					case 'moderator':
						$title = 'ACP_LOG_GALLERY_MOD';
						$template->assign_vars(array(
							'L_TITLE'	=> $user->lang('ACP_LOG_GALLERY_MOD'),
							'L_EXPLAIN'	=> $user->lang('ACP_LOG_GALLERY_MOD_EXP'),
							'S_SELECT_OPTION'	=> 'moderator'
						));
					break;
					case 'system':
						$title = 'ACP_LOG_GALLERY_SYSTEM';
						$template->assign_vars(array(
							'L_TITLE'	=> $user->lang('ACP_LOG_GALLERY_SYSTEM'),
							'L_EXPLAIN'	=> $user->lang('ACP_LOG_GALLERY_SYSTEM_EXP'),
							'S_SELECT_OPTION'	=> 'system'
						));
					break;
				}
				$this->page_title = $user->lang($title);

				$log->build_list($filter_log, 25, $page/25 + 1, -1);
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}
}
