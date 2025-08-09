<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

// this file is not really needed, when empty it can be omitted
// however you can override the default methods and add custom
// installation logic

namespace phpbbgallery\core;

class ext extends \phpbb\extension\base
{
	protected $sub_extensions = [
		'phpbbgallery/acpcleanup',
		'phpbbgallery/acpimport',
		'phpbbgallery/exif',
	];

	/**
	* Single enable step that installs any included migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				// Disable list of official extensions
				$extensions = $this->container->get('ext.manager');
				$configured = $extensions->all_disabled();

				foreach ($this->sub_extensions as $sub_ext)
				{
					if (array_key_exists($sub_ext, $configured))
					{
						$extensions->enable($sub_ext);
					}
				}
				// Enable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->enable_notifications('phpbbgallery.core.notification.image_for_approval');
				$phpbb_notifications->enable_notifications('phpbbgallery.core.notification.image_approved');
				$phpbb_notifications->enable_notifications('phpbbgallery.core.notification.new_image');
				$phpbb_notifications->enable_notifications('phpbbgallery.core.notification.new_comment');
				$phpbb_notifications->enable_notifications('phpbbgallery.core.notification.new_report');
				return 'notifications';
			break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
			break;
		}
	}

	/**
	* Single disable step that does nothing
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function disable_step($old_state)
	{
		$extensions = $this->container->get('ext.manager');

		// Check if any sub-extension is enabled - if yes, block disabling core
		foreach ($this->$sub_extensions as $sub_ext)
		{
			if ($extensions->is_enabled($sub_ext))
			{
				$this->container->get('user')->add_lang_ext('phpbbgallery/core', 'install_gallery');
				$error_msg = sprintf(
					$this->container->get('user')->lang('GALLERY_SUB_EXT_FOUND'), 
					$sub_ext
				);
				trigger_error($error_msg, E_USER_WARNING);
			}
		}
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				// Disable list of official extensions
				$extensions = $this->container->get('ext.manager');
				foreach ($this->add_ons as $var)
				{
					$extensions->disable($var);
				}

				// Disable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->disable_notifications('phpbbgallery.core.notification.image_for_approval');
				$phpbb_notifications->disable_notifications('phpbbgallery.core.notification.image_approved');
				$phpbb_notifications->disable_notifications('phpbbgallery.core.notification.new_image');
				$phpbb_notifications->disable_notifications('phpbbgallery.core.notification.new_comment');
				$phpbb_notifications->disable_notifications('phpbbgallery.core.notification.new_report');
				return 'notifications';

			break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}

	/**
	* Single purge step that reverts any included and installed migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/**
				* @todo Remove this try/catch condition once purge_notifications is fixed
				* in the core to work with disabled extensions without fatal errors.
				* https://tracker.phpbb.com/browse/PHPBB3-12435
				*/
				try
				{
					// Purge board rules notifications
					$phpbb_notifications = $this->container->get('notification_manager');
					$phpbb_notifications->purge_notifications('phpbbgallery.core.notification.image_for_approval');
					$phpbb_notifications->purge_notifications('phpbbgallery.core.notification.image_approved');
					$phpbb_notifications->purge_notifications('phpbbgallery.core.notification.new_image');
					$phpbb_notifications->purge_notifications('phpbbgallery.core.notification.new_comment');
					$phpbb_notifications->purge_notifications('phpbbgallery.core.notification.new_report');
				}
				catch (\phpbb\notification\exception $e)
				{
					// continue
				}
				return 'notifications';
			break;
			default:
				// Run parent purge step method
				return parent::purge_step($old_state);
			break;
		}
	}
}
