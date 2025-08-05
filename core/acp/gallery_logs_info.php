<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\acp;

class gallery_logs_info
{
	function module()
	{
		return array(
			'title'		=> 'ACP_GALLERY_LOGS',
			'version'	=> '2.0.0',
			'modes'		=> array(
				'main'			=> array(
					'title' => 'ACP_GALLERY_LOGS',
					'auth' => 'ext_phpbbgallery/core && acl_a_gallery_manage',
					'cat' => array('PHPBB_GALLERY')
				),
			),
		);
	}
}
