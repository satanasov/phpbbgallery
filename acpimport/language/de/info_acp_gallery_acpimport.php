<?php
/**
*
* @package Gallery - ACP Import Extension [English]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
* Übersetzt von franki (http://dieahnen.de/ahnenforum/)
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
	'ACP_IMPORT_ALBUMS'				=> 'Neue Bilder importieren',
	'ACP_IMPORT_ALBUMS_EXPLAIN'		=> 'Hier kannst Du die Anzahl von Bilder eingeben, die importiert werden sollen. Bevor Du die Bilder importierst, ändere die Größe von Hand mit einer Bildbearbeitungssoftware.',

	'IMPORT_ALBUM'					=> 'Zielalbum:',
	'IMPORT_DEBUG_MES'				=> '%1$s Bilder importiert. Es sind noch %2$s Bilder zu importieren.',
	'IMPORT_DIR_EMPTY'				=> 'Das Verzeichnis %s ist leer. Du musst die Bilder erst hochladen, bevor du sie importieren kannst.',
	'IMPORT_FINISHED'				=> 'Alle %1$s Bilder erfolgreich importiert.',
	'IMPORT_FINISHED_ERRORS'		=> '%1$s Bilder wurden erfolgreich importiert, aber die folgenden Fehler sind aufgetreten:<br /><br />',
	'IMPORT_MISSING_ALBUM'			=> 'Wähle bitte ein Album aus, in das die Bilder importiert werden sollen.',
	'IMPORT_SELECT'					=> 'Wähle die Bilder aus, die importiert werden sollen. Bilder die erfolgreich importiert wurden, werden aus der Auswahl gelöscht. Die anderen Bilder stehen dir danach noch zur Verfügung.',
	'IMPORT_SCHEMA_CREATED'			=> 'Das Import-Schema wurde erstellt, warte bitte während die Bilder importiert werden.',
	'IMPORT_USER'					=> 'Hochgeladen durch',
	'IMPORT_USER_EXP'				=> 'Du kannst die Bilder auch einem anderem Mitglied zuordnen lassen.',
	'IMPORT_USERS_PEGA'				=> 'In das persönliche Album des Benutzers laden.',

	'MISSING_IMPORT_SCHEMA'			=> 'Das Import-Schema (%s) konnte nicht gefunden werden.',

	'NO_FILE_SELECTED'				=> 'Du musst mindestens eine Datei auswählen.',
));
