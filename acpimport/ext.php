<?php
/**
 * phpBB Gallery - ACP Import Extension
 *
 * @package   phpbbgallery/acpimport
 * @author    Leinad4Mind
 * @copyright 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\acpimport;

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
		$user = $this->container->get('user');

		$core_ext = 'phpbbgallery/core';

		// Check if core is installed (enabled or disabled)
		$is_enabled = $manager->is_enabled($core_ext);
		$is_disabled = $manager->is_disabled($core_ext);

		if (!$is_enabled && !$is_disabled)
		{
			// Core not installed at all
			$user->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_import');
			trigger_error($user->lang('GALLERY_CORE_NOT_FOUND'), E_USER_WARNING);
			return false;
		}

		if ($is_disabled)
		{
			// Core installed but disabled — enable it automatically
			$manager->enable($core_ext);
		}

		// If here, core is either enabled or just enabled now
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
			$this->container->get('user')->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_import');
			$this->container->get('template')->assign_var('L_EXTENSION_ENABLE_SUCCESS', $this->container->get('user')->lang['EXTENSION_ENABLE_SUCCESS']);
		}

		return parent::enable_step($old_state);
	}
}
