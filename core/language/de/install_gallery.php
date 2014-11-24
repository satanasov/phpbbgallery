<?php
/**
*
* install_gallery [Deutsch]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Übersetzt von franki (http://motorradforum-niederrhein.de/downloads/)
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
	'BBCODES_NEEDS_REPARSE'			=> 'Der BBCode muss aktualisiert werden.',

	'CAT_CONVERT'					=> 'phpBB2 konvertieren',
	'CAT_CONVERT_TS'				=> 'TS Gallery konvertieren',
	'CAT_UNINSTALL'					=> 'phpBB Gallery deinstallieren',

	'CHECK_TABLES'					=> 'Tabellen überprüfung',
	'CHECK_TABLES_EXPLAIN'			=> 'Die folgenden Tabellen müssten existieren, damit sie auch übernommen werden können.',

	'CONVERT_SMARTOR_INTRO'			=> 'Konverter vom „Album-MOD“ von smartor zur „phpBB Gallery“',
	'CONVERT_SMARTOR_INTRO_BODY'	=> 'Mit diesem Konverter, kannst du deine Alben, Bilder, Bewertungen und Kommentare aus dem <a href="http://www.phpbb.com/community/viewtopic.php?f=16&t=74772">Album-MOD</a> von Smartor (getestet mit v2.0.56) und <a href="http://www.phpbbhacks.com/download/5028">Full Album Pack</a> (getestet mit v1.4.1) auslesen und dann in die phpBB Gallery einfügen lassen.<br /><br /><strong>Achtung:</strong> Die <strong>Berechtigungen</strong> werden dabei <strong>nicht übernommen</strong>.',
	'CONVERT_TS_INTRO'				=> 'Konverter von der „TS Gallery“ zur „phpBB Gallery“',
	'CONVERT_TS_INTRO_BODY'			=> 'Mit diesem Konverter, kannst du deine Alben, Bilder, Bewertungen und Kommentare aus der <a href="http://www.phpbb.com/community/viewtopic.php?f=70&t=610509">TS Gallery</a> (getestet mit v0.2.1) auslesen und dann in die phpBB Gallery einfügen lassen.<br /><br /><strong>Achtung:</strong> Die <strong>Berechtigungen</strong> werden dabei <strong>nicht übernommen</strong>.',
	'CONVERT_COMPLETE_EXPLAIN'		=> 'Du hast nun deine Gallery erfolgreich auf die phpBB Gallery v%s konvertiert.<br />Bitte prüfe, ob alle Einträge richtig übernommen wurden, bevor du dein Board durch Löschen des „install“-Verzeichnisses freigibst.<br /><br /><strong>Bitte beachte, dass die Berechtigungen nicht übernohmen wurden. Du musst diese also neu vergeben.</strong><br /><br />Es wird außerdem empfohlen die Datenbank von Einträgen zu befreien, bei denen die Bilder nicht mehr existieren. Dies kann im Administrations-Bereich unter „MODs > phpBB Galerie > Galerie reinigen“ getan werden.',

	'CONVERTED_ALBUMS'				=> 'Die Alben wurden erfolgreich kopiert.',
	'CONVERTED_COMMENTS'			=> 'Die Kommentare wurden erfolgreich kopiert.',
	'CONVERTED_IMAGES'				=> 'Die Bilder wurden erfolgreich kopiert.',
	'CONVERTED_MISC'				=> 'Verschiedene Daten wurden konvertiert.',
	'CONVERTED_PERSONALS'			=> 'Die persönlichen Alben wurden erfolgreich erstellt.',
	'CONVERTED_RATES'				=> 'Die Bewertungen wurden erfolgreich kopiert.',
	'CONVERTED_RESYNC_ALBUMS'		=> 'Die Alben-Statistiken wurden erfolgreich resyncronisiert.',
	'CONVERTED_RESYNC_COMMENTS'		=> 'Die Kommentare wurden erfolgreich resyncronisiert.',
	'CONVERTED_RESYNC_COUNTS'		=> 'Die Zähler-Statistiken wurden erfolgreich resyncronisiert.',
	'CONVERTED_RESYNC_RATES'		=> 'Die Bewertungen wurden erfolgreich resyncronisiert.',

	'FILE_DELETE_FAIL'				=> 'Datei konnte nicht gelöscht werden, du musst sie manuell löschen',
	'FILE_STILL_EXISTS'				=> 'Datei existiert noch',
	'FILES_REQUIRED_EXPLAIN'		=> '<strong>Voraussetzung</strong> - die phpBB Gallery muss auf diverse Verzeichnisse zugreifen oder diese beschreiben können, um reibungslos zu funktionieren. Wenn „Nicht beschreibbar“ angezeigt wird, musst du die Befugnisse für die Datei oder das Verzeichnis so ändern, dass phpBB darauf schreiben kann.',
	'FILES_DELETE_OUTDATED'			=> 'Veraltete Dateien löschen',
	'FILES_DELETE_OUTDATED_EXPLAIN'	=> 'Wenn du die Dateien löscht, werden sie entgülig gelöscht und können nicht wiederhergestellt werden!<br /><br />Hinweis:<br />Wenn du weitere Styles und Sprachpakete installiert hast, musst du die Dateien dort von Hand löschen.',
	'FILES_OUTDATED'				=> 'Veraltete Dateien',
	'FILES_OUTDATED_EXPLAIN'		=> '<strong>Veraltete Dateien</strong> - bitte entferne die folgenden Dateien um mögliche Sicherheitslücken zu entfernen.',
	'FOUND_INSTALL'					=> 'Doppelte Installation',
	'FOUND_INSTALL_EXPLAIN'			=> '<strong>Doppelte Installation</strong> - Es wurde eine Gallery Installation gefunden! Wenn du hier fortfährst, überschreibst du die bereits vorhandenen Daten. Alle Alben, Bilder und Kommentare werden dabei gelöscht! <strong>Es wird daher empfohlen nur ein %1$sUpdate%2$s zu machen.</strong>',
	'FOUND_VERSION'					=> 'Es wurde folgende Version gefunden',
	'FOUNDER_CHECK'					=> 'Du bist Gründer des Forums',
	'FOUNDER_NEEDED'				=> 'Du musst Gründer des Forums sein!',

	'INSTALL_CONGRATS_EXPLAIN'		=> 'Du hast die phpBB Gallery v%s nun erfolgreich installiert.<br/><br/><strong>Bitte lösche oder verschiebe nun das Installations-Verzeichnis „install“ oder nenne es nun um, bevor du dein Board benutzt. Solange dieses Verzeichnis existiert, ist nur der Administrations-Bereich zugänglich.</strong>',
	'INSTALL_INTRO_BODY'			=> 'Dieser Assistent ermöglicht dir die Installation der phpBB Gallery in deinem phpBB-Board.',

	'GOTO_GALLERY'					=> 'Gehe zur phpBB Gallery',
	'GOTO_INDEX'					=> 'Gehe zum Forum-Index',

	'MISSING_CONSTANTS'				=> 'Bevor du das Installations-Skript aufrufen kannst, musst du deine geänderten Dateien hochladen, insbesondere die includes/constants.php.',
	'MODULES_CREATE_PARENT'			=> 'Übergeordnetes Standard-Modul erstellen',
	'MODULES_PARENT_SELECT'			=> 'Übergeordnetes Modul auswählen',
	'MODULES_SELECT_4ACP'			=> 'Übergeordnetes Modul für den „Administrations-Bereich“',
	'MODULES_SELECT_4LOG'			=> 'Übergeordnetes Modul für das „Gallery-Protokoll“',
	'MODULES_SELECT_4MCP'			=> 'Übergeordnetes Modul für den „Moderations-Bereich“',
	'MODULES_SELECT_4UCP'			=> 'Übergeordnetes Modul für den „Persönlichen Bereich“',
	'MODULES_SELECT_NONE'			=> 'kein übergeordnetes Modul',

	'NO_INSTALL_FOUND'				=> 'Es wurde keine installierte Version gefunden!',

	'OPTIONAL_EXIFDATA'				=> 'Funktion „exif_read_data“ existiert',
	'OPTIONAL_EXIFDATA_EXP'			=> 'Das Exif-Modul ist nicht installiert oder geladen.',
	'OPTIONAL_EXIFDATA_EXPLAIN'		=> 'Wenn die Funktion existiert, werden auf der imagepage die Exif Daten zu den Bildern mit angezeigt.',
	'OPTIONAL_IMAGEROTATE'			=> 'Funktion „imagerotate“ existiert',
	'OPTIONAL_IMAGEROTATE_EXP'		=> 'Du musst deine GD Version, derzeit „%s“, aktualisieren.',
	'OPTIONAL_IMAGEROTATE_EXPLAIN'	=> 'Wenn die Funktion existiert, können Bilder während dem Hochladen und Bearbeiten gedreht werden.',

	'PAYPAL_DEV_SUPPORT'			=> '</p><div class="errorbox">
	<h3>Hinweise des Autors</h3>
	<p>Das Erstellen, Warten und Aktualisieren dieser MOD hat viel Zeit und Mühe in Anspruch genommen und das wird auch in Zukunft so bleiben. Wenn dir die MOD gefällt und du dies gerne mit einer Spende zeigen möchtest, würde mich das sehr freuen. Meine Paypal ID ist nickvergessen@gmx.de, oder kontaktiere mich per Email.<br /><br />Die empfohlene Spendenhöhe für diese MOD beträgt 25,00€ (aber jeder Betrag hilft).</p><br />
	<a href="http://www.flying-bits.org/go/paypal"><input type="submit" value="Make PayPal-Donation" name="paypal" id="paypal" class="button1" /></a>
</div><p>',

	'PHP_SETTINGS'					=> 'PHP Einstellungen',
	'PHP_SETTINGS_EXP'				=> 'Diese PHP Einstellungen und Konfigurationen werden benötigt um die Gallery zu installieren und korrekt benutzen zu können.',
	'PHP_SETTINGS_OPTIONAL'			=> 'Optionale PHP Einstellungen',
	'PHP_SETTINGS_OPTIONAL_EXP'		=> 'Diese PHP Einstellungen werden <strong>NICHT</strong> zwingend benötigt, aber ermöglichen einige Extra-Features.',

	'REQ_GD_LIBRARY'				=> 'GD Library ist installiert',
	'REQ_PHP_VERSION'				=> 'php version >= %s',
	'REQ_PHPBB_VERSION'				=> 'phpBB version >= %s',
	'REQUIREMENTS_EXPLAIN'			=> 'Bevor die Installation fortgesetzt werden kann, wird phpBB einige Tests zu deiner Server-Konfiguration und deinen Dateien durchführen, um sicherzustellen, dass du die phpBB Gallery installieren und benutzen kannst. Bitte lies die Ergebnisse aufmerksam durch und fahre nicht weiter fort, bevor alle erforderlichen Tests bestanden sind.',

	'STAGE_ADVANCED_EXPLAIN'		=> 'Bitte wähle die übergeordneten Module für die Module der phpBB Gallery aus. Im Normalfall solltest du diese Einstellungen nicht verändern.',
	'STAGE_COPY_TABLE'				=> 'Datenbank-Tabellen kopieren',
	'STAGE_COPY_TABLE_EXPLAIN'		=> 'Die Datenbank-Tabellen mit den Alben und Benutzer-Informationen der TS Gallery haben die gleichen Namen wie die der phpBB Gallery. Es wurde daher eine Kopie angelegt, um die Daten trotzdem importieren zu können.',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'Die von der phpBB Gallery genutzten Datenbank-Tabellen wurden nun erstellt und mit einigen Ausgangswerten gefüllt. Geh weiter zum nächsten Schritt, um die Installation der phpBB Gallery abzuschließen.',
	'STAGE_DELETE_TABLES'			=> 'Datenbank reinigen',
	'STAGE_DELETE_TABLES_EXPLAIN'	=> 'Die von der phpBB Gallery genutzten Datenbank-Inhalte wurden gelöscht. Geh weiter zum nächsten Schritt, um die Deinstallation der phpBB Gallery abzuschließen.',
	'SUPPORT_BODY'					=> 'Für die aktuelle, stabile Version der „phpBB Gallery“ wird kostenloser Support gewährt. Dies umfasst:</p><ul><li>Installation</li><li>Konfiguration</li><li>Technische Fragen</li><li>Probleme durch eventuelle Fehler in der Software</li><li>Aktualisierung von Release Candidates (RC) oder stabilen Versionen zur aktuellen stabilen Version</li><li>Konvertierungen von smartor\'s Album MOD für phpBB 2.0.x zur phpBB Gallery für phpBB3</li><li>Konvertierungen von der TS Gallery zur phpBB Gallery</li></ul><p>Die Verwendung der Beta-Versionen wird nur beschränkt empfohlen. Sollte ein neues Update erscheinen, wird empfohlen das Update schnell durchzuführen.</p><p>Support gibt es in folgenden Foren:</p><ul><li><a href="http://www.flying-bits.org/">flying-bits.org - Homepage des MOD-Autor\'s nickvergessen</a></li><li><a href="http://www.phpbb.de/">phpbb.de</a></li><li><a href="http://www.phpbb.com/">phpbb.com</a></li></ul><p>',

	'TABLE_ALBUM'					=> 'Tabelle mit den Bildern',
	'TABLE_ALBUM_CAT'				=> 'Tabelle mit den Alben',
	'TABLE_ALBUM_COMMENT'			=> 'Tabelle mit den Kommentaren',
	'TABLE_ALBUM_CONFIG'			=> 'Tabelle mit den Konfigurationswerten',
	'TABLE_ALBUM_RATE'				=> 'Tabelle mit den Bewertungen',
	'TABLE_EXISTS'					=> 'vorhanden',
	'TABLE_MISSING'					=> 'fehlt',
	'TABLE_PREFIX_EXPLAIN'			=> 'Präfix der phpBB2-Installation',

	'UNINSTALL_INTRO'					=> 'Willkommen bei der Deinstallation',
	'UNINSTALL_INTRO_BODY'				=> 'Dieser Assistent ermöglicht dir die Deinstallation der phpBB Gallery aus deinem phpBB-Board.<br /><br /><strong>ACHTUNG: Dabei werden alle Alben, Bilder und Kommentare unwiderruflich gelöscht!</strong>',
	'UNINSTALL_REQUIREMENTS'			=> 'Vorraussetzungen',
	'UNINSTALL_REQUIREMENTS_EXPLAIN'	=> 'Bevor die Deinstallation fortgesetzt werden kann, wird phpBB ein paar Tests durchgeführt, um sicherzustellen, dass du die phpBB Gallery deinstallieren darfst.',
	'UNINSTALL_START'					=> 'Deinstallieren',
	'UNINSTALL_FINISHED'				=> 'Deinstallation fast abgeschlossen',
	'UNINSTALL_FINISHED_EXPLAIN'		=> 'Du hast die phpBB Gallery erfolgreich deinstalliert.<br/><br/><strong>Du musst nun nur noch die Schritte aus der install.xml rückgäng machen und die Dateien der Gallery löschen, danach ist dein Forum komplett von der Gallery bereinigt.</strong>',

	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Mit dieser Option kannst du deine phpBB Gallery-Version auf den neuesten Stand bringen.',

	'VERSION_NOT_SUPPORTED'			=> 'Leider konnte das Update-Schema für Versionen < 1.0.6 nicht übernommen werden.',
));
