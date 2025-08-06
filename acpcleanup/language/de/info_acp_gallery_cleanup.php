<?php
/**
 * phpBB Gallery - ACP CleanUp Extension [German Translation]
 *
 * @package   phpbbgallery/acpcleanup
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
	'ACP_GALLERY_CLEANUP'			=> 'Galerie bereinigen',

	'ACP_GALLERY_CLEANUP_EXPLAIN'	=> 'Hier kannst Du einige Reste löschen.',

	'CLEAN_AUTHORS_DONE'			=> 'Bilder ohne gültigen Autor gelöscht.',
	'CLEAN_CHANGED'					=> 'Autor in "Gast" geändert.',
	'CLEAN_COMMENTS_DONE'			=> 'Kommentare ohne gültigen Autor gelöscht.',
	'CLEAN_ENTRIES_DONE'			=> 'Dateien ohne Datenbank-Eintrag gelöscht.',
	'CLEAN_GALLERY'					=> 'Säubere Galerie',
	'CLEAN_GALLERY_ABORT'			=> 'Bereinigung abbrechen!',
	'CLEAN_NO_ACTION'				=> 'Keine Aktion abgeschlossen. Etwas ist schiefgelaufen!',
	'CLEAN_PERSONALS_DONE'			=> 'Persönliche Alben ohne gültige Eigentümer gelöscht.',
	'CLEAN_PERSONALS_BAD_DONE'		=> 'Persönliche Alben von ausgewählten Benutzer gelöscht.',
	'CLEAN_PRUNE_DONE'				=> 'Bilder erfolgreich bereinigt.',
	'CLEAN_PRUNE_NO_PATTERN'		=> 'Kein Suchmuster.',
	'CLEAN_SOURCES_DONE'			=> 'Bilder ohne Datei gelöscht.',

	'CONFIRM_CLEAN'					=> 'Dieser Schritt kann nicht rückgängig gemacht werden!',
	'CONFIRM_CLEAN_AUTHORS'			=> 'Lösche Bilder ohne gültigen Autor?',
	'CONFIRM_CLEAN_COMMENTS'		=> 'Kommentare ohne gültigen Autor löschen?',
	'CONFIRM_CLEAN_ENTRIES'			=> 'Dateien löschen ohne Datenbank-Eintrag?',
	'CONFIRM_CLEAN_PERSONALS'		=> 'Persönlichen Alben löschen ohne gültige Inhaber?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_PERSONALS_BAD'	=> 'Lösche persönlichen Alben von ausgewählten Benutzer?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_SOURCES'			=> 'Bilder löschen ohne Datei?',
	'CONFIRM_PRUNE'					=> 'Alle Bilder, die die folgenden Bedingungen haben, Löschen:<br /><br />%s<br />',

	'PRUNE'							=> 'Bereinigen',
	'PRUNE_ALBUMS'					=> 'Alben bereinigen',
	'PRUNE_CHECK_OPTION'			=> 'Aktiviere diese Option, wenn Du die Bilder bereinigen möchtest.',
	'PRUNE_COMMENTS'				=> 'Weniger als x Kommentare',
	'PRUNE_PATTERN_ALBUM_ID'		=> 'Das Bild ist in einem der folgenden Alben:<br />&raquo; <strong>%s</strong>',
	'PRUNE_PATTERN_COMMENTS'		=> 'Das Bild hat weniger als <strong>%d</strong> Kommentare.',
	'PRUNE_PATTERN_RATES'			=> 'Das Bild hat weniger als <strong>%d</strong> Bewertungen.',
	'PRUNE_PATTERN_RATE_AVG'		=> 'Das Bild hat eine durchschnittliche Bewertung, weniger als <strong>%s</strong>.',
	'PRUNE_PATTERN_TIME'			=> 'Das Bild wurde vor dem “<strong>%s</strong>“ hochgeladen.',
	'PRUNE_PATTERN_USER_ID'			=> 'Das Bild wurde durch eine der folgenden Benutzern hochgeladen:<br />&raquo; <strong>%s</strong>',
	'PRUNE_RATINGS'					=> 'Weniger als x Bewertungen',
	'PRUNE_RATING_AVG'				=> 'Durchschnittliche Bewertung niedriger als',
	'PRUNE_RATING_AVG_EXP'			=> 'Es werden nur Bilder bereinigt mit einer durchschnittlichen Bewertung von weniger als “<samp>x.yz</samp>“.',
	'PRUNE_TIME'					=> 'Hochgeladen vor',
	'PRUNE_TIME_EXP'				=> 'Nur Bilder bereinigen die vor dem “<samp>YYYY-MM-DD</samp>“ hochgeladen wurden.',
	'PRUNE_USERNAME'				=> 'Hochgeladen von',
	'PRUNE_USERNAME_EXP'			=> 'Nur Bilder von folgenden Benutzern bereinigen. Um Bilder von "Gäste" zu bereinigen, markiere das Kontrollkästchen über dem Benutzernamen-Feld.',

	//Log
	'LOG_CLEANUP_DELETE_FILES'		=> '%s Bilder ohne DB-Einträge wurden gelöscht.',
	'LOG_CLEANUP_DELETE_ENTRIES'		=> '%s Bilder ohne Dateien wurden gelöscht.',
	'LOG_CLEANUP_DELETE_NO_AUTHOR'		=> '%s Bilder ohne gültigem Autor wurden gelöscht.',
	'LOG_CLEANUP_COMMENT_DELETE_NO_AUTHOR'	=> '%s Kommentare ohne gültigem Autor wurden gelöscht.',

	'MOVE_TO_IMPORT'			=> 'Verschieben von Bildern ins Import Verzeichnis',
	'MOVE_TO_USER'				=> 'Wechseln zu Benutzer',
	'MOVE_TO_USER_EXP'			=> 'Bilder und Kommentare werden als diejenigen des Benutzers verschoben werden, die Du definiert hast. Wenn Keine ausgewählt wird - wird Gast verwendet.',
	'CLEAN_USER_NOT_FOUND'		=> 'Der von Ihnen ausgewählte Benutzer existiert nicht!',

	'GALLERY_CORE_NOT_FOUND'		=> 'Die phpBB Gallery Core-Erweiterung muss zuerst installiert und aktiviert werden.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'Die Erweiterung wurde erfolgreich aktiviert.',
]);
