<?php
/**
*
* install_gallery [Dutch]
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
	'BBCODES_NEEDS_REPARSE'		=> 'De BBCode moet opnieuw opgebouwd worden.',

	'CAT_CONVERT'				=> 'converteer phpBB2',
	'CAT_CONVERT_TS'			=> 'converteer TS Gallery',
	'CAT_UNINSTALL'				=> 'phpBB Galerij deïnstalleren',

	'CHECK_TABLES'				=> 'Controleer tabellen',
	'CHECK_TABLES_EXPLAIN'		=> 'De volgende tabellen moeten bestaan zodat ze geconverteerd kunnen worden.',

	'CONVERT_SMARTOR_INTRO'			=> 'Converteer van “Album-MOD“ van Smartor naar “phpBB Galerij“',
	'CONVERT_SMARTOR_INTRO_BODY'	=> 'Met dit conversieprogramma kan je je albums, afbeeldingen, beoordelingen en commentaren van de <a href="http://www.phpbb.com/community/viewtopic.php?f=16&t=74772">Album-MOD</a> van Smartor (getest v2.0.56) en <a href="http://www.phpbbhacks.com/download/5028">Full Album Pack</a> (getest v1.4.1) naar phpBB Galerij omzetten.<br /><br /><strong>Let op:</strong> De <strong>permissies</strong> worden <strong>niet meegenomen</strong>.',
	'CONVERT_TS_INTRO'				=> 'Converteer van “TS Gallery“ naar “phpBB Galerij“',
	'CONVERT_TS_INTRO_BODY'			=> 'Met dit conversieprogramma kan je je albums, afbeeldingen, beoordelingen en commentaren van de <a href="http://www.phpbb.com/community/viewtopic.php?f=70&t=610509">TS Gallery</a> (getest v0.2.1) naar phpBB Galerij omzetten.<br /><br /><strong>Let op:</strong> De <strong>permissies</strong> worden <strong>niet meegenomen</strong>.',
	'CONVERT_COMPLETE_EXPLAIN'		=> 'Conversion from your gallery to phpBB Gallery v%s was successful.<br />Please ensure that the settings were transferred correctly before enabling your board by deleting the install directory.<br /><br /><strong>Please note that the permissions were not copied.</strong><br /><br />You should also clean your database from old entries, where the images are missing. This can be done in “.MODs > phpBB Gallery > Cleanup gallery“.',

	'CONVERTED_ALBUMS'			=> 'De albums zijn succesvol gekopieeerd.',
	'CONVERTED_COMMENTS'		=> 'De reacties zijn succesvol gekopieerd.',
	'CONVERTED_IMAGES'			=> 'De afbeeldingen zijn succesvol gekopieerd.',
	'CONVERTED_MISC'			=> 'Diverse onderdelen geconverteerd.',
	'CONVERTED_PERSONALS'		=> 'De persoonlijke albums zijn succesvol gekopieerd.',
	'CONVERTED_RATES'			=> 'De beoordelingen zijn succesvol gekopieerd.',
	'CONVERTED_RESYNC_ALBUMS'	=> 'Album statestieken synchroniseren.',
	'CONVERTED_RESYNC_COMMENTS'	=> 'Reacties synchroniseren.',
	'CONVERTED_RESYNC_COUNTS'	=> 'Afbeeldingstellers synchroniseren.',
	'CONVERTED_RESYNC_RATES'	=> 'Beoordelingen synchroniseren.',

	'FILE_DELETE_FAIL'				=> 'Het bestand kan niet verwijderd worden, je zult het bestand handmatig moeten verwijderen',
	'FILE_STILL_EXISTS'				=> 'Het bestand bestaat nog steeds',
	'FILES_REQUIRED_EXPLAIN'		=> '<strong>Verplicht</strong> - In order to function correctly phpBB Gallery needs to be able to access or write to certain files or directories. If you see “Unwritable” you need to change the permissions on the file or directory to allow phpBB to write to it.',
	'FILES_DELETE_OUTDATED'			=> 'Verwijder verouderde bestanden',
	'FILES_DELETE_OUTDATED_EXPLAIN'	=> 'Als je op verouderde bestanden verwijderen klikt, dan worden de bestanden volledig verwijderd. Dit kan niet terugedraaid worden!<br /><br />LET OP:<br />Als je meer stijlen en talen hebt geïnstalleerd, dan moet je deze met de hand verwijderen.',
	'FILES_OUTDATED'				=> 'Verouderde bestanden',
	'FILES_OUTDATED_EXPLAIN'		=> '<strong>Verouderd</strong> - In order to deny hacking attempts, please remove the following files.',
	'FOUND_INSTALL'					=> 'Dubbele Installatie',
	'FOUND_INSTALL_EXPLAIN'			=> '<strong>Dubbele Installatie</strong> - An Installation of the gallery was found! If you continue here, you overwrite all existing data. All albums, images and comments will be deleted! <strong>That´s why an %1$supdate%2$s recommanded.</strong>',
	'FOUND_VERSION'					=> 'De volgende versie is gevonden',
	'FOUNDER_CHECK'					=> 'Je bent een “Oprichter“ van dit forum',
	'FOUNDER_NEEDED'				=> 'Je moet een “Oprichter“ van dit forum zijn!',

	'INSTALL_CONGRATS_EXPLAIN'	=> 'Je hebt phpBB Galerij v%s succesvol geïnstalleerd.<br/><br/><strong>Verwijder, verplaats of hernoem install map voordat je het forum weer gaat gebruiken. If this directory is still present, only the Administration Control Panel (ACP) will be accessible.</strong>',
	'INSTALL_INTRO_BODY'		=> 'Met deze optie is het mogelijk op de phpBB Galerij te installeren op je forum.',

	'GOTO_GALLERY'				=> 'Ga naar phpBB Galerij',
	'GOTO_INDEX'				=> 'Ga naar Forumoverzicht ',

	'MISSING_CONSTANTS'			=> 'Voordat je het installatiescript kan starten moet je de bijgewerkte bestanden uploaden, in het bijzonder: includes/constants.php.',
	'MODULES_CREATE_PARENT'		=> 'Kies hoofd standaard-module',
	'MODULES_PARENT_SELECT'		=> 'Kies hoofd module',
	'MODULES_SELECT_4ACP'		=> 'Kies hoofd module voor “beheerderspaneel“',
	'MODULES_SELECT_4LOG'		=> 'Kies hoofd module voor “Galerij-log“',
	'MODULES_SELECT_4MCP'		=> 'Kies hoofd module voor “moderatorpaneel“',
	'MODULES_SELECT_4UCP'		=> 'Kies hoofd module voor “gebruikerspaneel“',
	'MODULES_SELECT_NONE'		=> 'Geen hoofd module',

	'NO_INSTALL_FOUND'			=> 'Er is geen installatie gevonden!',

	'OPTIONAL_EXIFDATA'				=> 'Functie “exif_read_data“ bestaat',
	'OPTIONAL_EXIFDATA_EXP'			=> 'De exif-module wordt niet geladen of is niet geïnstalleerd.',
	'OPTIONAL_EXIFDATA_EXPLAIN'		=> 'Als de functie bestaat, worden de exif gegevens van de afbeeldingen getoond op de afbeeldingspagina.',
	'OPTIONAL_IMAGEROTATE'			=> 'Functie “imagerotate“ bestaat',
	'OPTIONAL_IMAGEROTATE_EXP'		=> 'De versie van GD Version moet worden bijgewerkt, deze is momenteel “%s“.',
	'OPTIONAL_IMAGEROTATE_EXPLAIN'	=> 'Als de functie bestaat kan je afbeeldingen draaien tijdens het uploaden en bewerken.',

	'PAYPAL_DEV_SUPPORT'				=> '</p><div class="errorbox">
	<h3>Opmerking van de auteur</h3>
	<p>Het maken, onderhouden en bijwerken van deze extensie vraagt veel tijd en moeite, als je deze extensie waardeert en die waardering ook via een donatie wilt laten blijken, dan wordt dat zeer op prijs gesteld. Mijn Paypal ID is <strong>nickvergessen@gmx.de</strong>, of neem contact met me op voor mijn postadres.<br /><br />Het voorgestelde donatiebedrag voor deze extensie is €25,00 (maar alle kleine beetjes helpen).</p><br />
	<a href="http://www.flying-bits.org/go/paypal"><input type="submit" value="Doe PayPal-Donatie" name="paypal" id="paypal" class="button1" /></a>
</div><p>',

	'PHP_SETTINGS'				=> 'PHP Instellingen',
	'PHP_SETTINGS_EXP'			=> 'Deze PHP instellingen en configuraties zijn verplicht voor het installeren en het draaien van de galerij.',
	'PHP_SETTINGS_OPTIONAL'		=> 'Optionle PHP instelingen',
	'PHP_SETTINGS_OPTIONAL_EXP'	=> 'Deze PHP instellingen zijn <strong>NIET</strong> verplicht voor normaal gebruik, maak kunnen wel zorgen voor een aantal extra functies.',

	'REQ_GD_LIBRARY'			=> 'GD Library is geïnstalleerd',
	'REQ_PHP_VERSION'			=> 'php versie >= %s',
	'REQ_PHPBB_VERSION'			=> 'phpBB versie >= %s',
	'REQUIREMENTS_EXPLAIN'		=> 'Voordat je verder gaat met de volledige installatie, zal phpBB een aantal test uitvoeren om je serverinstellingen en bestanden te contoleren. Zodat je zeker weet dat je de phpBB galerij kan installeren en draaien. LET OP: Lees de resultaten van deze test goed door om er zeker van te zijn dat alle verplichte tests geslaagd zijn.',

	'STAGE_ADVANCED_EXPLAIN'		=> 'Kies een hoofd-module voor de Galerij-modules. Normaal gezien verander je dit niet.',
	'STAGE_COPY_TABLE'				=> 'Kopieer de database-tabellen ',
	'STAGE_COPY_TABLE_EXPLAIN'		=> 'De database-tabellen met de albums en gebruikersgegevens van TS Gallery hebben dezelfde namen als voor phpBB Galerij. Daarom is er een kopie aangemaakt om de gegevens toch te kunnen importeren.',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'De database-tabellen die phpBB Galerij gebruikt zijn aangemaakt en gevuld met de eerste gegevens. Ga verder naar het volgende scherm om de installatie van phpBB Galerij te voltooien.',
	'STAGE_DELETE_TABLES'			=> 'Database opschonen',
	'STAGE_DELETE_TABLES_EXPLAIN'	=> 'De database-inhoud van de Galerij-MOD is verwijderd. Ga verder naar het volgende scherm on de deïnstallatie van de phpBB Galerij te voltooien.',
	'SUPPORT_BODY'					=> 'Volledige ondersteunig voor de huidige stabiele versie voor de phpBB Galerij, wordt gratis aangeboden. Dit omvat:</p><ul><li>Installatie</li><li>Instellingen</li><li>Technische vragen</li><li>problemen door potentiële fouten in deze software</li><li>updaten Release Candidate (RC) versies naar de laatste stabiele versie</li><li>conversie van Smartor’s Album-MOD voor phpBB 2.0.x naar phpBB Galerij voor phpBB3</li><li>conversie van TS Gallery naar phpBB Galerij</li></ul><p>Het gebruik van Beta-versies oftewel test-vesies wordt afgeraden. Als er een nieuwe versie is, wordt het aangeraden zosnel mogelijk te updaten.</p><p>Support wordt gegeven op deze forums</p><ul><li><a href="http://www.flying-bits.org/">flying-bits.org - MOD-Autor nickvergessen’s board</a></li><li><a href="http://www.phpbb.de/">phpbb.de</a></li><li><a href="http://www.phpbb.com/">phpbb.com</a></li></ul><p>',

	'TABLE_ALBUM'				=> 'tabel met de afbeeldingen',
	'TABLE_ALBUM_CAT'			=> 'tabel met de albums',
	'TABLE_ALBUM_COMMENT'		=> 'tabel met de reacties',
	'TABLE_ALBUM_CONFIG'		=> 'tabel met de instellingen',
	'TABLE_ALBUM_RATE'			=> 'tabel met de beoordelingen',
	'TABLE_EXISTS'				=> 'bestaat',
	'TABLE_MISSING'				=> 'mist',
	'TABLE_PREFIX_EXPLAIN'		=> 'Prefix van de phpBB2-installatie',

	'UNINSTALL_INTRO'					=> 'Welkom bij deïnstalleren',
	'UNINSTALL_INTRO_BODY'				=> 'Met deze optie is het mogelijk om de phpBB galerij te verwijderen van je forum.<br /><br /><strong>WAARSCHUWING: All albums, afbeelding en reacties worden verwijderd. Dit is onomkeerbaar!</strong>',
	'UNINSTALL_REQUIREMENTS'			=> 'Benodigdheden',
	'UNINSTALL_REQUIREMENTS_EXPLAIN'	=> 'Voordat je verder gaat met het volledig verwijderen, voert phpBB een aantal tests uitvoeen om zeker te zijn dat je de phpBB Galerij mag verwijderen.',
	'UNINSTALL_START'					=> 'Deïnstalleren',
	'UNINSTALL_FINISHED'				=> 'Deïnstalleren bijna voltooid',
	'UNINSTALL_FINISHED_EXPLAIN'		=> 'Je hebt de phpBB galerij succesvol gedeïnstalleerd.<br/><br/><strong>Nu hoef je alleen nog maar de stappen uit de install.xml te volgen en alle bestanden en wijzigingen te verwijderen. Daarna is je forum volledig van de Galerij verlost.</strong>',

	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Hier kan je de phpBB galerij updaten.',

	'VERSION_NOT_SUPPORTED'		=> 'Sorry, maar updates van voor versie 1.0.6 worden door deze installatie niet ondersteund.',
));
