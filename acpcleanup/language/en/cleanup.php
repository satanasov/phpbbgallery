<?php
/**
*
* @package Gallery - ACP CleanUp Extension [English]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_GALLERY_CLEANUP_EXPLAIN'	=> 'Here you can delete some remains.',

	'CLEAN_AUTHORS_DONE'			=> 'Images without valid author deleted.',
	'CLEAN_CHANGED'					=> 'Author changed to “Guest“.',
	'CLEAN_COMMENTS_DONE'			=> 'Comments without valid author deleted.',
	'CLEAN_ENTRIES_DONE'			=> 'Files without database-entry deleted.',
	'CLEAN_GALLERY'					=> 'Clean gallery',
	'CLEAN_GALLERY_ABORT'			=> 'Cleanup abort!',
	'CLEAN_NO_ACTION'				=> 'No action completed. Something went wrong!',
	'CLEAN_PERSONALS_DONE'			=> 'Personal albums without valid owner deleted.',
	'CLEAN_PERSONALS_BAD_DONE'		=> 'Personal albums from selected users deleted.',
	'CLEAN_PRUNE_DONE'				=> 'Successfully pruned images.',
	'CLEAN_PRUNE_NO_PATTERN'		=> 'No search pattern.',
	'CLEAN_SOURCES_DONE'			=> 'Images without file deleted.',

	'CONFIRM_CLEAN'					=> 'This step can not be undone!',
	'CONFIRM_CLEAN_AUTHORS'			=> 'Delete images without valid author?',
	'CONFIRM_CLEAN_COMMENTS'		=> 'Delete comments without valid author?',
	'CONFIRM_CLEAN_ENTRIES'			=> 'Delete files without database-entry?',
	'CONFIRM_CLEAN_PERSONALS'		=> 'Delete personal albums without valid owner?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_PERSONALS_BAD'	=> 'Delete personal albums from selected users?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_SOURCES'			=> 'Delete images without file?',
	'CONFIRM_PRUNE'					=> 'Delete all images, that have the following conditions:<br /><br />%s<br />',

	'PRUNE'							=> 'Prune',
	'PRUNE_ALBUMS'					=> 'Prune albums',
	'PRUNE_CHECK_OPTION'			=> 'Check this option, while pruning images.',
	'PRUNE_COMMENTS'				=> 'Less than x comments',
	'PRUNE_PATTERN_ALBUM_ID'		=> 'The image is in one of the following albums:<br />&raquo; <strong>%s</strong>',
	'PRUNE_PATTERN_COMMENTS'		=> 'The image has less than <strong>%d</strong> comments.',
	'PRUNE_PATTERN_RATES'			=> 'The image has less than <strong>%d</strong> ratings.',
	'PRUNE_PATTERN_RATE_AVG'		=> 'The image has a rating average, lower than <strong>%s</strong>.',
	'PRUNE_PATTERN_TIME'			=> 'The image was uploaded before “<strong>%s</strong>“.',
	'PRUNE_PATTERN_USER_ID'			=> 'The image was uploaded by one of the following users:<br />&raquo; <strong>%s</strong>',
	'PRUNE_RATINGS'					=> 'Less than x ratings',
	'PRUNE_RATING_AVG'				=> 'Average rating lower than',
	'PRUNE_RATING_AVG_EXP'			=> 'Only prune images, with an average rating lower than “<samp>x.yz</samp>“.',
	'PRUNE_TIME'					=> 'Uploaded before',
	'PRUNE_TIME_EXP'				=> 'Only prune images, that where uploaded before “<samp>YYYY-MM-DD</samp>“.',
	'PRUNE_USERNAME'				=> 'Uploaded by',
	'PRUNE_USERNAME_EXP'			=> 'Only prune images from certain users. To prune images from “guests“ select the checkbox beyond the username-box.',
));
