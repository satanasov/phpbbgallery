<?php
/**
 * phpBB Gallery - ACP Exif Extension
 *
 * @package   phpbbgallery/exif
 * @author    Leinad4Mind
 * @copyright 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\exif;

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
			$this->container->get('user')->add_lang_ext('phpbbgallery/exif', 'info_exif');
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
			$this->container->get('user')->add_lang_ext('phpbbgallery/exif', 'info_exif');
			$this->container->get('template')->assign_var('L_EXTENSION_ENABLE_SUCCESS', $this->container->get('user')->lang['EXTENSION_ENABLE_SUCCESS']);
		}

		return parent::enable_step($old_state);
	}
}
