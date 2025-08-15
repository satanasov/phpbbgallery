<?php
/**
 * phpBB Gallery - ACP Import Extension [German Translation]
 *
 * @package   phpbbgallery/acpimport
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator franki <https://dieahnen.de/ahnenforum>
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

	'GALLERY_CORE_NOT_FOUND'		=> 'Die phpBB Gallery Core-Erweiterung muss zuerst installiert und aktiviert werden.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'Die Erweiterung wurde erfolgreich aktiviert.',
]);
