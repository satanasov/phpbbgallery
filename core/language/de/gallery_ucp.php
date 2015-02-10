<?php
/**
*
* gallery_ucp [Deutsch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Übersetzt von franki (http://dieahnen.de/ahnenforum/)
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
	'ACCESS_CONTROL_ALL'			=> 'Jeden',
	'ACCESS_CONTROL_REGISTERED'		=> 'Registrierte Benutzer',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Registrierte Benutzer, außer ignorierte Mitglieder',
	'ACCESS_CONTROL_FRIENDS'		=> 'Nur deine Freunde',
	'ACCESS_CONTROL_SPECIAL_FRIENDS'	=> 'Nur Deine speziellen Freunde',
	'ALBUMS'						=> 'Alben',
	'ALBUM_ACCESS'					=> 'Zugriff erlauben für',
	'ALBUM_ACCESS_EXPLAIN'			=> 'Du kannst deine %1$sListen der Freunde und ignorierten Mitglieder%2$s benutzen, um den Zugriff einzuschränken. <strong>Moderatoren</strong> können jedoch <strong>immer</strong> auf das Album zugreifen.',
	'ALBUM_DESC'					=> 'Beschreibung',
	'ALBUM_NAME'					=> 'Name',
	'ALBUM_PARENT'					=> 'Übergeordnetes Album',
	'ATTACHED_SUBALBUMS'			=> 'Verknüpfte Subalben',

	'CREATE_PERSONAL_ALBUM'			=> 'Erstelle dein persönliches Album',
	'CREATE_SUBALBUM'				=> 'Erstelle ein Subalbum',
	'CREATE_SUBALBUM_EXP'			=> 'Du kannst ein Subalbum zu Deinem persönlichem Album hinzufügen.',
	'CREATED_SUBALBUM'				=> 'Subalbum erfolgreich bearbeitet',

	'DELETE_ALBUM'					=> 'Lösche Album',
	'DELETE_ALBUM_CONFIRM'			=> 'Album mit allen Bildern und Subalben löschen?',
	'DELETED_ALBUMS'				=> 'Album erfolgreich gelöscht',

	'EDIT'							=> 'Bearbeiten',
	'EDIT_ALBUM'					=> 'Dieses Album bearbeiten',
	'EDIT_SUBALBUM'					=> 'Bearbeite Subalben',
	'EDIT_SUBALBUM_EXP'				=> 'Du kannst hier Deine Alben bearbeiten.',
	'EDITED_SUBALBUM'				=> 'Album erfolgreich bearbeitet',

	'GOTO'							=> 'Gehe zu',

	'MANAGE_SUBALBUMS'				=> 'Verwalte Deine Subalben',
	'MISSING_ALBUM_NAME'			=> 'Gib bitte einen Namen für das Album an',

	'NEED_INITIALISE'				=> 'Du hast bisher noch kein Subalbum.',
	'NO_ALBUM_STEALING'				=> 'Du bist nicht berechtigt Alben von anderen Benutzern zu verwalten.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'Du hast bereits die maximale Anzahl von Subalben zu Deinem persönlichem Album hinzugefügt.',
	'NO_PARENT_ALBUM'				=> '&laquo;-- kein übergeordnetes Album',
	'NO_PERSALBUM_ALLOWED'			=> 'Du bist nicht berechtigt ein persönliches Album zu erstellen.',
	'NO_PERSONAL_ALBUM'				=> 'Dein persönliches Album existiert noch nicht. Du kannst Dir hier ein privates Album und weitere Subalben erstellen.<br />Nur der Album Besitzer kann in diese persönlichen Alben Bilder hochladen.',
	'NO_SUBALBUMS'					=> 'Keine Subalben',
	'NO_SUBSCRIPTIONS'				=> 'Du beobachtest keine Bilder.',

	'PARSE_BBCODE'					=> 'BBCodes erkennen',
	'PARSE_SMILIES'					=> 'Smilies erkennen',
	'PARSE_URLS'					=> 'Links erkennen',
	'PERSONAL_ALBUM'				=> 'Persönliches Album',

	'UNSUBSCRIBE'					=> 'nicht mehr beobachten',
	'USER_ALLOW_COMMENTS'			=> 'Benutzern erlauben deine Bilder zu kommentieren',

	'YOUR_SUBSCRIPTIONS'			=> 'Hier siehst du die Bilder und Alben, bei denen du benachrichtigt wirst.',

	'WATCH_CHANGED'					=> 'Einstellungen gespeichert',
	'WATCH_COM'						=> 'Kommentierte Bilder standardmässig beobachten',
	'WATCH_NOTE'					=> 'Die Einstellung wirkt sich nur auf neue Bilder aus. Andere Bilder musst du über die Option „Bild beobachten“ hinzufügen',
	'WATCH_OWN'						=> 'Eigene Bilder standardmässig beobachten',
));
