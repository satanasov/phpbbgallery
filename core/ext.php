<?php

// this file is not really needed, when empty it can be ommitted
// however you can override the default methods and add custom
// installation logic

namespace phpbbgallery\core;

class ext extends \phpbb\extension\base
{
	protected $add_ons = array(
		'phpbbgallery/acpimport',
		'phpbbgallery/exif',
	);
	/**
	* Single disable step that does nothing
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				// Disable list of official extensions
				$extensions = $this->container->get('ext.manager');
				foreach ($this->add_ons as $var)
				{
					$extensions->disable($var);
				}
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}

}
