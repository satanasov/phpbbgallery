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
			'core.memberlist_view_profile'	       => 'user_profile_galleries',
			//'core.generate_profile_fields_template_data_before'	       => 'profile_fileds',
			'core.grab_profile_fields_data'	       => 'get_user_ids',
			//'core.viewonline_overwrite_location'	=> 'add_newspage_viewonline',
		);
	}
	/* @var \phpbb\controller\helper */
	protected $helper;
	/* @var \phpbb\template\template */
	protected $template;
	/* @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language  */
	protected $language;

	/** @var \phpbbgallery\core\search  */
	protected $gallery_search;

	/** @var \phpbbgallery\core\config  */
	protected $gallery_config;
	/** @var \phpbb\db\driver\driver_interface  */
	protected $db;

	/** @var string */
	protected $albums_table;

	/** @var string */
	protected $users_table;

	/* @var string phpEx */
	protected $php_ext;

	protected $user_ids = array();
	protected $target = 0;
	protected $albums = array();

	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper $helper Newspage helper object
	 * @param \phpbb\template\template $template Template object
	 * @param \phpbb\user $user User object
	 * @param \phpbb\language\language $lang
	 * @param \phpbbgallery\core\search $gallery_search
	 * @param \phpbbgallery\core\config $gallery_config
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param $albums_table
	 * @param $users_table
	 * @param string $php_ext phpEx
	 */
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user,
								\phpbb\language\language $lang, \phpbbgallery\core\search $gallery_search,
								\phpbbgallery\core\config $gallery_config, \phpbb\db\driver\driver_interface $db,
								$albums_table, $users_table, $php_ext)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->language = $lang;
		$this->gallery_search = $gallery_search;
		$this->gallery_config = $gallery_config;
		$this->db = $db;
		$this->php_ext = $php_ext;
		$this->albums_table = $albums_table;
		$this->users_table = $users_table;
	}
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name'	=> 'phpbbgallery/core',
			'lang_set'	=> array('info_acp_gallery', 'gallery', 'gallery_notifications'/*, 'permissions_gallery'*/),
		);
		$event['lang_set_ext'] = $lang_set_ext;
		if ($this->gallery_config->get('disp_total_images') == 1)
		{
			$this->template->assign_vars(array(
				'PHPBBGALLERY_INDEX_STATS'	=> $this->gallery_config->get('num_images'),
			));
		}
	}
	public function add_page_header_link($event)
	{
		if ($this->gallery_config->get('disp_gallery_icon') == 1)
		{
			$this->template->assign_vars(array(
				'U_GALLERY'	=> $this->helper->route('phpbbgallery_core_index'),
			));
		}
	}
	public function user_profile_galleries($event)
	{
		$this->language->add_lang(array('gallery'), 'phpbbgallery/core');
		$this->language->add_lang('search');
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
			$block_name	= $this->language->lang('RECENT_IMAGES');
			$u_block = ' ';
			$this->gallery_search->recent($this->gallery_config->get('rrc_profile_items'), -1, $event['member']['user_id'], 'rrc_profile_display', $block_name, $u_block);
		}
		if ($random)
		{
			$block_name	= $this->language->lang('RANDOM_IMAGES');
			$u_block = ' ';
			$this->gallery_search->random($this->gallery_config->get('rrc_profile_items'), $event['member']['user_id'], 'rrc_profile_display', $block_name, $u_block);
		}

		// Now - do we show statistics
		if ($this->gallery_config->get('profile_user_images') == 1)
		{
			$sql = 'SELECT * FROM ' . $this->users_table . ' WHERE user_id = ' . (int) $event['member']['user_id'];
			$result = $this->db->sql_query($sql);
			$user_info = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if ($user_info)
			{
				$this->template->assign_vars(array(
					'U_GALLERY_IMAGES_ALLOW'	=> true,
					'U_GALLERY_IMAGES'	=> $user_info['user_images'],
				));
			}
			else
			{
				$this->template->assign_vars(array(
					'U_GALLERY_IMAGES_ALLOW'	=> true,
					'U_GALLERY_IMAGES'	=> 0,
				));
			}
		}
	}
	public function get_user_ids($event)
	{
		if (count($event['user_ids']) == 1)
		{
			$this->user_ids = $event['user_ids'];
			if ($this->gallery_config->get('profile_pega'))
			{
				$sql = 'SELECT album_id, album_user_id FROM ' . $this->albums_table . ' WHERE parent_id = 0 and ' . $this->db->sql_in_set('album_user_id', $this->user_ids);
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$this->albums[$row['album_user_id']] = (int) $row['album_id'];
				}
				$this->db->sql_freeresult($result);
			}
		}
	}
}
