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

class permissions_info
{
	function module()
	{
		return array(
			'title'		=> 'PHPBB_GALLERY',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'manage'	=> array(
					'title' => 'ACP_GALLERY_ALBUM_PERMISSIONS',
					'auth' => 'ext_phpbbgallery/core && acl_a_gallery_albums',
					'cat' => array('PHPBB_GALLERY')
				),
				'copy'		=> array(
					'title' => 'ACP_GALLERY_ALBUM_PERMISSIONS_COPY',
					'auth' => 'ext_phpbbgallery/core && acl_a_gallery_albums',
					'cat' => array('PHPBB_GALLERY')
				),
			),
		);
	}
}
