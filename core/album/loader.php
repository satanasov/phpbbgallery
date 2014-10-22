<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\album;

class loader
{
	/* @var \phpbb\db\driver\driver */
	protected $db;

	/* @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $table_albums;

	/** @var array */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver	$db			Database object
	* @param \phpbb\user				$user		User object
	* @param string					$albums_table	Gallery albums table
	*/
	public function __construct(\phpbb\db\driver\driver $db, \phpbb\user $user, $albums_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->table_albums = $albums_table;
	}

	/**
	* Load the data of an album
	*
	* @param	int		$album_id
	* @return	bool	True if the album was loaded
	* @throws	\OutOfBoundsException	if the album does not exist
	*/
	public function load($album_id)
	{
		$album_id = (int) $album_id;
		$sql_array = array(
			'SELECT'		=> 'a.*',
			'FROM'			=> array($this->table_albums => 'a'),
			'WHERE'			=> 'a.album_id = ' . $album_id,
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			throw new \OutOfBoundsException('INVALID_ALBUM');
		}

		$this->data[$album_id] = $row;

		return true;
	}

	/**
	* Get the value of an album
	*
	* @param	int		$album_id
	* @param	mixed	$column_name	Name of the column,
	*						if null an array with all columns will be returned
	* @throws	\OutOfBoundsException	if the album does not exist
	* @throws	\OutOfRangeException	if $column_name does not exist
	*/
	public function get($album_id, $column_name = null)
	{
		$album_id = (int) $album_id;
		if (!isset($this->data[$album_id]))
		{
			$this->load($album_id);
		}

		if ($column_name === null)
		{
			return $this->data[$album_id];
		}

		if (!isset($this->data[$album_id][$column_name]))
		{
			throw new \OutOfRangeException('INVALID_ALBUM_COLUMN');
		}

		return $this->data[$album_id][$column_name];
	}

	/**
	* Check whether the album_user is the user who wants to do something
	*
	* @param	int		$album_id
	* @param	mixed	$user_id	If false the current user will be compared
	* @return	bool	True if the user is the owner of the album
	* @throws	\DomainException	if the user is not the owner of the album
	*/
	public function validate_owner($album_id, $user_id = false)
	{
		$album_id = (int) $album_id;
		$user_id = (int) ($user_id ?: $this->user->data['user_id']);

		if ($this->get($album_id, 'album_user_id') != $user_id)
		{
			throw new \DomainException('INVALID_ALBUM');
		}

		return true;
	}
}
