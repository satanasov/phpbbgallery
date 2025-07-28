<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
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
