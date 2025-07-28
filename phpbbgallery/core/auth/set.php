<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\auth;

class set
{
	protected $bits = 0;

	protected $counts = array(
		'i_count'	=> 0,
		'a_count'	=> 0,
	);

	public function __construct($bits = 0, $i_count = 0, $a_count = 0)
	{
		$this->bits = $bits;

		$this->counts = array(
			'i_count'	=> $i_count,
			'a_count'	=> $a_count,
		);
	}

	public function set_bit($bit, $set)
	{
		$this->bits = phpbb_optionset($bit, $set, $this->bits);
	}

	public function get_bit($bit)
	{
		return phpbb_optionget($bit, $this->bits);
	}

	public function get_bits()
	{
		return $this->bits;
	}

	public function set_count($data, $set)
	{
		$this->counts[$data] = (int) $set;
	}

	public function get_count($data)
	{
		return (int) $this->counts[$data];
	}
}
