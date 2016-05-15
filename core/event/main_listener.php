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
			'core.generate_profile_fields_template_data_before'	       => 'profile_fileds',
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
	/* @var string phpEx */
	protected $php_ext;

	protected $user_ids = array();
	protected $target = 0;
	protected $albums = array();

	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper          $helper   Newspage helper object
	 * @param \phpbb\template\template          $template Template object
	 * @param \phpbb\user                       $user     User object
	 * @param \phpbbgallery\core\search         $gallery_search
	 * @param \phpbbgallery\core\config         $gallery_config
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param                                   $albums_table
	 * @param                                   $users_table
	 * @param string                            $php_ext  phpEx
	 */
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbbgallery\core\search $gallery_search,
	\phpbbgallery\core\config $gallery_config, \phpbb\db\driver\driver_interface $db,
	$albums_table, $users_table, $php_ext)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->gallery_search = $gallery_search;
		$this->gallery_config = $gallery_config;
		$this->db = $db;
		$this->php_ext = $php_ext;
		$this->albums_table = $albums_table;
		$this->users_table = $users_table;
	}
	public function load_language_on_setup($event)
	{
		$this->user->add_lang_ext('phpbbgallery/core', 'info_acp_gallery');
		$this->user->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->user->add_lang_ext('phpbbgallery/core', 'gallery_notifications');
		$this->user->add_lang_ext('phpbbgallery/core', 'permissions_gallery');
		if ($this->gallery_config->get('disp_total_images') == 1)
		{
			$this->template->assign_vars(array(
				'PHPBBGALLERY_INDEX_STATS'	=> $this->gallery_config->get('num_images'),
			));
		}
	}
	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
			'U_GALLERY'	=> $this->helper->route('phpbbgallery_core_index'),
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
			$this->gallery_search->recent($this->gallery_config->get('rrc_profile_items'), -1, $event['member']['user_id'], 'rrc_profile_display', $block_name, $u_block);
		}
		if ($random)
		{
			$block_name	= $this->user->lang['RANDOM_IMAGES'];
			$u_block = ' ';
			$this->gallery_search->random($this->gallery_config->get('rrc_profile_items'), $event['member']['user_id'], 'rrc_profile_display', $block_name, $u_block);
		}

		// Now - do we show statistics
		if ($this->gallery_config->get('profile_user_images') == 1)
		{
			$sql = 'SELECT * FROM ' . $this->users_table . ' WHERE user_id = ' . $event['member']['user_id'];
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
	public function profile_fileds($event)
	{
		if (count($this->user_ids) == 1)
		{
			if (isset($this->albums[$this->user_ids[0]]))
			{
				$data = $event['profile_row'];
				$data['phpbb_gallery'] = array(
					'value' => $this->helper->route('phpbbgallery_core_album', array('album_id' => $this->albums[$this->user_ids[$this->target]])),
					'data' => array(
						'field_id'				=> '',
						'lang_id'				=> '',
						'lang_name'				=> 'GALLERY',
						'lang_explain'			=> '',
						'lang_default_value'	=> '',
						'field_name'			=> 'phpbb_gallery',
						'field_type'			=> 'profilefields.type.url',
						'field_ident'			=> 'phpbb_gallery',
						'field_length'			=> '40',
						'field_minlen'			=> '12',
						'field_maxlen'			=> '255',
						'field_novalue'			=> '',
						'field_default_value'	=> '',
						'field_validation'		=> '',
						'field_required'		=> '0',
						'field_show_on_reg'		=> '0',
						'field_hide'			=> '0',
						'field_no_view'			=> '0',
						'field_active'			=> '1',
						'field_order'			=> '',
						'field_show_profile'	=> '1',
						'field_show_on_vt'		=> '1',
						'field_show_novalue'	=> '0',
						'field_show_on_pm'		=> '1',
						'field_show_on_ml'		=> '1',
						'field_is_contact'		=> '1',
						'field_contact_desc'	=> 'VISIT_GALLERY',
						'field_contact_url'		=> '%s',
					),
				);
				$event['profile_row'] = $data;
			}
		}
	}
}
