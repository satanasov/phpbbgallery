<?php
/**
*
* gallery_ucp [Dutch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* [Dutch] translated by Dutch Translators (https://github.com/dutch-translators)
*
**/

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
	'ACCESS_CONTROL_ALL'			=> 'Iedereen',
	'ACCESS_CONTROL_REGISTERED'		=> 'Geregistreerde gebruikers',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Geregistreerde gebruikers, behalve je vijanden',
	'ACCESS_CONTROL_FRIENDS'		=> 'Alleen je vrienden',
	'ACCESS_CONTROL_SPECIAL_FRIENDS'		=> 'Alleen je speciale vrienden',
	'ALBUMS'						=> 'Albums',
	'ALBUM_ACCESS'					=> 'Sta toegang toe voor',
	'ALBUM_ACCESS_EXPLAIN'			=> 'Je kan je %1$sVrienden en Vijanden lijst%2$s gebruiken om de toegang tot je album te regelen. Echter, <strong>moderators</strong> hebben <strong>altijd/strong> toegang tot je album.',
	'ALBUM_DESC'					=> 'Album Beschrijving',
	'ALBUM_NAME'					=> 'Album Naam',
	'ALBUM_PARENT'					=> 'Hoofd Album',
	'ATTACHED_SUBALBUMS'			=> 'Bijgevoegde subalbums',

	'CREATE_PERSONAL_ALBUM'			=> 'Creëer  persoonlijk album',
	'CREATE_SUBALBUM'				=> 'Creëer  subalbum',
	'CREATE_SUBALBUM_EXP'			=> 'Je kan en nieuw subalbum toevoegen aan je persoonlijke galerij.',
	'CREATED_SUBALBUM'				=> 'Subalbum succesvol aangemaakt',

	'DELETE_ALBUM'					=> 'Verwijder Album',
	'DELETE_ALBUM_CONFIRM'			=> 'Verwijder Album, met alle bijgevoegde subalbums en afbeeldingen?',
	'DELETED_ALBUMS'				=> 'Albums succesvol verwijderd',

	'EDIT'							=> 'Wijzig',
	'EDIT_ALBUM'					=> 'Wijzig album',
	'EDIT_SUBALBUM'					=> 'Wijzig Subalbum',
	'EDIT_SUBALBUM_EXP'				=> 'Je kan je album hier wijzigen.',
	'EDITED_SUBALBUM'				=> 'Album succesvol gewijzigd',

	'GOTO'							=> 'Ga naar',

	'MANAGE_SUBALBUMS'				=> 'Beheer je subalbums',
	'MISSING_ALBUM_NAME'			=> 'Voer een naam in voor het album',

	'NEED_INITIALISE'				=> 'Je hebt nog geen persoonlijk album.',
	'NO_ALBUM_STEALING'				=> 'Je bent niet bevoegd om albums van andere te beheren.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'Je hebt het maximum aantal subalbums voor je persoonlijke album bereikt',
	'NO_PARENT_ALBUM'				=> '&laquo;-- geen hoofd',
	'NO_PERSALBUM_ALLOWED'			=> 'Je hebt geen permissies om een persoonlijk album te creëren',
	'NO_PERSONAL_ALBUM'				=> 'Je hebt nog geen persoonlijk album. Hier kan je persoonlijke albums creëren, met een aantal subalbums.<br />In persoonlijke albums kan alleen de eigenaar afbeeldingen uploaden.',
	'NO_SUBALBUMS'					=> 'Geen albums toegevoegd',
	'NO_SUBSCRIPTIONS'				=> 'Je bent niet geabonneerd op een afbeelding.',

	'PARSE_BBCODE'					=> 'BBCode gebruiken',
	'PARSE_SMILIES'					=> 'Smilies gebruiken',
	'PARSE_URLS'					=> 'Links gebruiken',
	'PERSONAL_ALBUM'				=> 'Persoonlijk album',

	'UNSUBSCRIBE'					=> 'Uitschrijven',
	'USER_ALLOW_COMMENTS'			=> 'Sta gebruikers toe om op je afbeeldingen te reageren',

	'YOUR_SUBSCRIPTIONS'			=> 'Hier kan je albums en afbeeldingen zien waar je notificaties van krijgt.',

	'WATCH_CHANGED'					=> 'Instellingen opgeslagen',
	'WATCH_COM'						=> 'Aboneer standaard op afbeeldingen met reacties',
	'WATCH_NOTE'					=> 'Deze optie heeft alleen effect op nieuwe afbeeldingen. Alle andere afbeeldingen moeten worden toegevoegd door de “abonneer afbeelding“ optie.',
	'WATCH_OWN'						=> 'Aboneer standaard op eigen afbeeldingen',
));
