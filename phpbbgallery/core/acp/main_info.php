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

class main_info
{
	function module()
	{
		return array(
			'title'		=> 'PHPBB_GALLERY',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'overview'			=> array(
					'title' => 'ACP_GALLERY_OVERVIEW',
					'auth' => 'ext_phpbbgallery/core && acl_a_gallery_manage',
					'cat' => array('PHPBB_GALLERY')
				),
			),
		);
	}
}
