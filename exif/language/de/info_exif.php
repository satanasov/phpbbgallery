<?php
/**
 * phpBB Gallery - ACP Exif Extension [German Translation]
 *
 * @package   phpbbgallery/exif
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator franki <https://motorradforum-niederrhein.de/downloads>
 */

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/**
* Language for Exif data
*/
$lang = array_merge($lang, [
	'EXIF_DATA'					=> 'EXIF-Daten',
	'EXIF_APERTURE'				=> 'Blende',
	'EXIF_CAM_MODEL'			=> 'Kamera-Modell',
	'EXIF_DATE'					=> 'Bild aufgenommen am',
	'EXIF_EXPOSURE'				=> 'Belichtungszeit',
	'EXIF_EXPOSURE_EXP'			=> '%s Sek',// 'EXIF_EXPOSURE' unit
	'EXIF_EXPOSURE_BIAS'		=> 'Belichtungskorrektur',
	'EXIF_EXPOSURE_BIAS_EXP'	=> '%s LW',// 'EXIF_EXPOSURE_BIAS' unit
	'EXIF_EXPOSURE_PROG'		=> 'Belichtungsprogramm',
	'EXIF_EXPOSURE_PROG_0'		=> 'Nicht definiert',
	'EXIF_EXPOSURE_PROG_1'		=> 'Manuell',
	'EXIF_EXPOSURE_PROG_2'		=> 'Normal-Programm',
	'EXIF_EXPOSURE_PROG_3'		=> 'Blendenpriorität',
	'EXIF_EXPOSURE_PROG_4'		=> 'Verschlusspriorität',
	'EXIF_EXPOSURE_PROG_5'		=> 'Kreativ-Programm (ausgerichtet auf Schärfentiefe)',
	'EXIF_EXPOSURE_PROG_6'		=> 'Action-Programm (ausgerichtet auf schnelle Verschlussgeschwindigkeit)',
	'EXIF_EXPOSURE_PROG_7'		=> 'Portrait-Modus (für CloseUp-Fotos mit unscharfem Hintergrund)',
	'EXIF_EXPOSURE_PROG_8'		=> 'Landschaftsmodus (für Landschaftsfotos mit scharfem Hintergrund)',
	'EXIF_FLASH'				=> 'Blitz',
	'EXIF_FLASH_CASE_0'			=> 'Blitz wurde nicht ausgelöst',
	'EXIF_FLASH_CASE_1'			=> 'Blitz wurde ausgelöst',
	'EXIF_FLASH_CASE_5'			=> 'Kein Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_7'			=> 'Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_8'			=> 'Ein, Blitz wurde nicht ausgelöst',
	'EXIF_FLASH_CASE_9'			=> 'Blitz wurde ausgelöst, Blitz erzwingen-Modus',
	'EXIF_FLASH_CASE_13'		=> 'Blitz wurde ausgelöst, Blitz erzwingen-Modus, kein Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_15'		=> 'Blitz wurde ausgelöst, Blitz erzwingen-Modus, Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_16'		=> 'Blitz wurde nicht ausgelöst, Blitz unterdrücken-Modus',
	'EXIF_FLASH_CASE_20'		=> 'Deaktiviert, Blitz wurde nicht ausgelöst, kein Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_24'		=> 'Blitz wurde nicht ausgelöst, Automodus',
	'EXIF_FLASH_CASE_25'		=> 'Blitz wurde ausgelöst, Automodus',
	'EXIF_FLASH_CASE_29'		=> 'Blitz wurde ausgelöst, Automodus, kein Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_31'		=> 'Blitz wurde ausgelöst, Automodus, Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_32'		=> 'Keine Blitzfunktion',
	'EXIF_FLASH_CASE_48'		=> 'Deaktiviert, Keine Blitzfunktion',
	'EXIF_FLASH_CASE_65'		=> 'Blitz wurde ausgelöst, Rote-Augen-Reduzierung',
	'EXIF_FLASH_CASE_69'		=> 'Blitz wurde ausgelöst, Rote-Augen-Reduzierung, kein Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_71'		=> 'Blitz wurde ausgelöst, Rote-Augen-Reduzierung, Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_73'		=> 'Blitz wurde ausgelöst, Blitz erzwingen-Modus, Rote-Augen-Reduzierung',
	'EXIF_FLASH_CASE_77'		=> 'Blitz wurde ausgelöst, Blitz erzwingen-Modus, Rote-Augen-Reduzierung, kein Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_79'		=> 'Blitz wurde ausgelöst, Blitz erzwingen-Modus, Rote-Augen-Reduzierung, Messblitz-Licht zurückgeworfen',
	'EXIF_FLASH_CASE_80'		=> 'Deaktiviert, Rote-Augen-Reduzierung',
	'EXIF_FLASH_CASE_88'		=> 'Blitz wurde nicht ausgelöst, Rote-Augen-Reduzierung',
	'EXIF_FLASH_CASE_89'		=> 'Blitz wurde ausgelöst, Automodus, Rote-Augen-Reduzierung',
	'EXIF_FLASH_CASE_93'		=> 'Blitz wurde ausgelöst, Automodus, kein Messblitz-Licht zurückgeworfen, Rote-Augen-Reduzierung',
	'EXIF_FLASH_CASE_95'		=> 'Blitz wurde ausgelöst, Automodus, Messblitz-Licht zurückgeworfen, Rote-Augen-Reduzierung',
	'EXIF_FOCAL'				=> 'Brennweite',
	'EXIF_FOCAL_EXP'			=> '%s mm',// 'EXIF_FOCAL' unit
	'EXIF_ISO'					=> 'ISO-Empfindlichkeit',
	'EXIF_METERING_MODE'		=> 'Belichtungs- Messmethode',
	'EXIF_METERING_MODE_0'		=> 'Unbekannt',
	'EXIF_METERING_MODE_1'		=> 'Durchschnitt',
	'EXIF_METERING_MODE_2'		=> 'Mittenbetont',
	'EXIF_METERING_MODE_3'		=> 'Spot',
	'EXIF_METERING_MODE_4'		=> 'Multi-Spot',
	'EXIF_METERING_MODE_5'		=> 'Multi-Segment',
	'EXIF_METERING_MODE_6'		=> 'Teilbild',
	'EXIF_METERING_MODE_255'	=> 'Andere',
	'EXIF_NOT_AVAILABLE'		=> 'nicht verfügbar',
	'EXIF_WHITEB'				=> 'Weißabgleich',
	'EXIF_WHITEB_AUTO'			=> 'Automatisch',
	'EXIF_WHITEB_MANU'			=> 'Manuell',

	'DISP_EXIF_DATA'			=> 'EXIF-Daten anzeigen',
	'DISP_EXIF_DATA_EXP'		=> 'Diese Funktion kann im Moment nicht verwendet werden, da die Funktion “exif_read_data“ nicht in Deiner PHP-Installation enthalten ist.',
	'SHOW_EXIF'					=> 'ein-/ausblenden',
	'VIEWEXIFS_DEFAULT'			=> 'Ansicht Exif-Daten standardmäßig',

	'GALLERY_CORE_NOT_FOUND'		=> 'Die phpBB Gallery Core-Erweiterung muss zuerst installiert und aktiviert werden.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'Die Erweiterung wurde erfolgreich aktiviert.',
]);
