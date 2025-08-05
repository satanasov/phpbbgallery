<?php
/**
 * phpBB Gallery - ACP Import Extension
 *
 * @package   phpbbgallery/acpimport
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
  */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'ACP_IMPORT_ALBUMS'				=> 'Import Images',
	'ACP_IMPORT_ALBUMS_EXPLAIN'		=> 'Here you can bulk import images from the file system. Before importing images, please be sure to resize them by hand.',

	'IMPORT_ALBUM'					=> 'Album to import images to:',
	'IMPORT_DEBUG_MES'				=> '%1$s images imported. There are still %2$s images remaining.',
	'IMPORT_DIR_EMPTY'				=> 'The folder %s is empty. You need to upload the images, before you can import them.',
	'IMPORT_FINISHED'				=> 'All %1$s images successful imported.',
	'IMPORT_FINISHED_ERRORS'		=> '%1$s images were successful imported, but the following errors occurred:<br /><br />',
	'IMPORT_MISSING_ALBUM'			=> 'Please select an album to import the images into.',
	'IMPORT_SELECT'					=> 'Choose the images which you want to import. Successful uploaded images are deleted. All other images are still available.',
	'IMPORT_SCHEMA_CREATED'			=> 'The import-schema was successfully created, please wait while the images get imported.',
	'IMPORT_USER'					=> 'Uploaded by',
	'IMPORT_USER_EXP'				=> 'You can add the images to another user here.',
	'IMPORT_USERS_PEGA'				=> 'Upload to users personal gallery.',

	'MISSING_IMPORT_SCHEMA'			=> 'The specified import-schema (%s) could not be found.',

	'NO_FILE_SELECTED'				=> 'You need to select at least one file.',

	'GALLERY_CORE_NOT_FOUND'		=> 'phpBB Gallery Core extension must be installed and enabled first.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'The extension has been enabled successfully.',
]);
