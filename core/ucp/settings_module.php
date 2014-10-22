<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\ucp;

/**
 * UCP Module for the User base settings
 * @package phpbbgallery\core\ucp
 */
class settings_module
{
	/** @var string */
	public $u_action;

	/** @var string */
	public $page_title;

	/** @var string */
	public $tpl_name;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver */
	protected $db;

	/** @var \phpbb\event\dispatcher */
	protected $dispatcher;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbbgallery\core\user */
	protected $gallery_user;

	public function main($id, $mode)
	{
		global $config, $db, $template, $user, $request, $phpbb_dispatcher, $phpbb_container;

		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $phpbb_dispatcher;
		$this->template = $template;
		$this->user = $user;
		$this->request = $request;

		$this->gallery_user = $phpbb_container->get('phpbbgallery.core.user');
		$this->gallery_user->set_user_id($this->user->data['user_id']);

		$user->add_lang_ext('phpbbgallery/core', array('gallery', 'gallery_acp', 'gallery_mcp', 'gallery_ucp'));
		$this->tpl_name = 'gallery/ucp_gallery';
		add_form_key('ucp_gallery');

		switch ($mode)
		{
			case 'manage':
				$title = 'UCP_GALLERY_SETTINGS';
				$this->page_title = $user->lang[$title];
				$this->set_personal_settings();
			break;
		}
	}

	protected function set_personal_settings()
	{
		if ($this->request->is_set_post('submit'))
		{
			$gallery_settings = array(
				'watch_own'				=> $this->request->variable('watch_own', false),
				'watch_com'				=> $this->request->variable('watch_com', false),
				'user_allow_comments'	=> $this->request->variable('allow_comments', false),
			);
			$additional_settings = array();

			$vars = array('additional_settings');
			extract($this->dispatcher->trigger_event('gallery.core.ucp.set_settings_submit', compact($vars)));

			$gallery_settings = array_merge($gallery_settings, $additional_settings);

			if (!$this->config['phpbb_gallery_allow_comments'] || !$this->config['phpbb_gallery_comment_user_control'])
			{
				unset($gallery_settings['user_allow_comments']);
			}

			$this->gallery_user->update_data($gallery_settings);

			meta_refresh(3, $this->u_action);
			trigger_error($this->user->lang['WATCH_CHANGED'] . '<br /><br />' . sprintf($this->user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>'));
		}

		$this->dispatcher->trigger_event('gallery.core.ucp.set_settings_nosubmit');

		$this->template->assign_vars(array(
			'S_PERSONAL_SETTINGS'	=> true,
			'S_UCP_ACTION'			=> $this->u_action,

			'L_TITLE'			=> $this->user->lang['UCP_GALLERY_SETTINGS'],
			'L_TITLE_EXPLAIN'	=> $this->user->lang['WATCH_NOTE'],

			'S_WATCH_OWN'		=> $this->gallery_user->get_data('watch_own'),
			'S_WATCH_COM'		=> $this->gallery_user->get_data('watch_com'),
			'S_ALLOW_COMMENTS'	=> $this->gallery_user->get_data('user_allow_comments'),
			'S_COMMENTS_ENABLED'=> $this->config['phpbb_gallery_allow_comments'] && $this->config['phpbb_gallery_comment_user_control'],
		));
	}
}
