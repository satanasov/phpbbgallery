<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @author    DreadDendy
 * @copyright 2018- Leinad4Mind, 2022 DreadDendy
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\cron;

/**
 * phpbbgallery cron task.
 */
class cron_cleaner extends \phpbb\cron\task\base
{
	/** @var \phpbbgallery\core\config  */
	protected $gallery_config;

	/** @var \phpbbgallery\core\upload  */
	protected $gallery_upload;

	/**
	 * Constructor
	 *
	 * @param \phpbbgallery\core\config $gallery_config
	 * @param \phpbbgallery\core\upload $gallery_upload
	 * @access public
	 */
	public function __construct(\phpbbgallery\core\config $gallery_config, \phpbbgallery\core\upload $gallery_upload)
	{
		$this->gallery_config = $gallery_config;
		$this->gallery_upload = $gallery_upload;
	}

	/**
	 * {@inheritDoc}
	 */
	public function run()
	{
		$this->gallery_upload->prune_orphan();
		$this->gallery_config->set('prune_orphan_time', time());
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_run()
	{
		return $this->gallery_config->get('prune_orphan_time') < strtotime('24 hours ago');
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_runnable()
	{
		return true;
	}
}
