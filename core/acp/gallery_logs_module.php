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

		$this->tpl_name = 'gallery_logs';
		add_form_key('acp_logs');
		$submode = request_var('submode', '');
		$page = $request->variable('page', 1);

		switch ($mode)
		{
			case 'overview':
				$title = 'ACP_GALLERY_OVERVIEW';
				$this->page_title = $user->lang[$title];

				$this->overview($page);
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}

	function overview($page)
	{
		global $auth, $config, $db, $template, $user, $phpbb_ext_gallery, $table_prefix, $phpbb_dispatcher, $phpbb_root_path;
		global $phpbb_container;

		$log = $phpbb_container->get('phpbbgallery.core.log');
		
		$log->build_list('admin', 5, $page);
	}
}
