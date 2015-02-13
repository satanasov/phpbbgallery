<?php
/**
 *
 * @package phpBB Gallery
 * @copyright (c) 2014 nickvergessen
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace phpbbgallery\core\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_header_link',
			'core.memberlist_prepare_profile_data'	       => 'user_profile_galleries',
			//'core.viewonline_overwrite_location'	=> 'add_newspage_viewonline',
		);
	}
	/* @var \phpbb\controller\helper */
	protected $helper;
	/* @var \phpbb\template\template */
	protected $template;
	/* @var \phpbb\user */
	protected $user;
	/* @var string phpEx */
	protected $php_ext;
	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Newspage helper object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	* @param string						$php_ext	phpEx
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbbgallery\core\search $gallery_search,
	\phpbbgallery\core\config $gallery_config,
	$php_ext)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->gallery_search = $gallery_search;
		$this->gallery_config = $gallery_config;
		$this->php_ext = $php_ext;
	}
	public function load_language_on_setup($event)
	{
		$this->user->add_lang_ext('phpbbgallery/core', 'info_acp_gallery');
		$this->user->add_lang_ext('phpbbgallery/core', 'gallery_notifications');
		$this->user->add_lang_ext('phpbbgallery/core', 'permissions_gallery');
	}
	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
			'U_GALLERY'	=> $this->helper->route('phpbbgallery_index'),
		));
	}
	public function user_profile_galleries($event)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('search');
		$random = $recent = false;
		$show_parts = $this->gallery_config->get('rrc_profile_mode');
		if ($show_parts >= 2)
		{
			$random = true;
			$show_parts = $show_parts - 2;
		}
		if ($show_parts == 1)
		{
			$recent = true;
		}
		if ($recent)
		{
			$block_name	= $this->user->lang['RECENT_IMAGES'];
			$u_block = ' ';
			$this->gallery_search->recent($this->gallery_config->get('rrc_profile_items'), -1, $event['data']['user_id'], 'rrc_profile_display', $block_name, $u_block);
		}
		if ($random)
		{
			$block_name	= $this->user->lang['RANDOM_IMAGES'];
			$u_block = ' ';
			$this->gallery_search->random($this->gallery_config->get('rrc_profile_items'), $event['data']['user_id'], 'rrc_profile_display', $block_name, $u_block);
		}
	}
}
