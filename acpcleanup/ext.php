<?php
/**
 * phpBB Gallery - ACP Cleanup Extension
 *
 * @package	phpBB Gallery
 * @copyright (c) 2012 nickvergessen | 2025 Leinad4Mind
 * @license	GNU General Public License v2
 */

namespace phpbbgallery\acpcleanup;

class ext extends \phpbb\extension\base
{
	/**
	 * Check whether or not the extension can be enabled.
	 * Checks dependencies and requirements.
	 *
	 * @return bool
	 */
	public function is_enableable()
	{
		$manager = $this->container->get('ext.manager');

		// Check if phpbbgallery/core is enabled
		if (!$manager->is_enabled('phpbbgallery/core'))
		{
			$this->container->get('user')->add_lang_ext('phpbbgallery/acpcleanup', 'info_acp_gallery_cleanup');
			trigger_error($this->container->get('user')->lang('GALLERY_CORE_NOT_FOUND'), E_USER_WARNING);
			return false;
		}

		return true;
	}

	/**
	 * Perform additional tasks on extension enable
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 */
	public function enable_step($old_state)
	{
		if (empty($old_state))
		{
			$this->container->get('user')->add_lang_ext('phpbbgallery/acpcleanup', 'info_acp_gallery_cleanup');
			$this->container->get('template')->assign_var('L_EXTENSION_ENABLE_SUCCESS', $this->container->get('user')->lang['EXTENSION_ENABLE_SUCCESS']);
		}

		return parent::enable_step($old_state);
	}
}
