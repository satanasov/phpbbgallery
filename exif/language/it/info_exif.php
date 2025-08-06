<?php
/**
 * phpBB Gallery - ACP Exif Extension [Italian Translation]
 *
 * @package   phpbbgallery/exif
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator
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
	'EXIF_DATA'					=> 'Dati Exif',
	'EXIF_APERTURE'				=> 'F-number',
	'EXIF_CAM_MODEL'			=> 'Modello Camera',
	'EXIF_DATE'					=> 'Immagine scattata il',
	'EXIF_EXPOSURE'				=> 'Velocita\' otturatore',
	'EXIF_EXPOSURE_EXP'			=> '%s Sec',// 'EXIF_EXPOSURE' unit
	'EXIF_EXPOSURE_BIAS'		=> 'Inclinazione di esposizione',
	'EXIF_EXPOSURE_BIAS_EXP'	=> '%s EV',// 'EXIF_EXPOSURE_BIAS' unit
	'EXIF_EXPOSURE_PROG'		=> 'Programma di esposizione',
	'EXIF_EXPOSURE_PROG_0'		=> 'Non definito',
	'EXIF_EXPOSURE_PROG_1'		=> 'Manuale',
	'EXIF_EXPOSURE_PROG_2'		=> 'Programma normale',
	'EXIF_EXPOSURE_PROG_3'		=> 'Priorita\' d\'apertura',
	'EXIF_EXPOSURE_PROG_4'		=> 'Priorita\' otturatore',
	'EXIF_EXPOSURE_PROG_5'		=> 'Programma creativo (inclinato verso la profondita\' di campo)',
	'EXIF_EXPOSURE_PROG_6'		=> 'Programma d\'azione (inclinato verso la velocita\' dell\'otturatore)',
	'EXIF_EXPOSURE_PROG_7'		=> 'Modalita\' ritratto (per foto ravvicinate con lo sfondo fuori fuoco)',
	'EXIF_EXPOSURE_PROG_8'		=> 'Modalita\' panorama (per foto del panorama con lo sfondo a fuoco)',
	'EXIF_FLASH'				=> 'Flash',
	'EXIF_FLASH_CASE_0'			=> 'Il Flash non e\' scattato',
	'EXIF_FLASH_CASE_1'			=> 'Flash scattato',
	'EXIF_FLASH_CASE_5'			=> 'luce di ritorno non rilevata',
	'EXIF_FLASH_CASE_7'			=> 'luce di ritorno rilevata',
	'EXIF_FLASH_CASE_8'			=> 'Acceso, il flash non e\' scattato',
	'EXIF_FLASH_CASE_9'			=> 'Il flash e\' scattato, modalita\' flash obbligatoria',
	'EXIF_FLASH_CASE_13'		=> 'Il flash e\' scattato, modalita\' flash obbligatoria, luce di ritorno non rilevata',
	'EXIF_FLASH_CASE_15'		=> 'Il flash e\' scattato, modalita\' flash obbligatoria, luce di ritorno rilevata',
	'EXIF_FLASH_CASE_16'		=> 'Il flash non e\' scattato, modalita\' flash obbligatoria',
	'EXIF_FLASH_CASE_20'		=> 'Spento, il flash non e\' scattato, luce di ritorno non rilevata',
	'EXIF_FLASH_CASE_24'		=> 'Il flash non e\' scattato, modalita\' automatica',
	'EXIF_FLASH_CASE_25'		=> 'Il flash e\' scattato, modalita\' automatica',
	'EXIF_FLASH_CASE_29'		=> 'Il flash e\' scattato, modalita\' automatica, luce di ritorno non rilevata',
	'EXIF_FLASH_CASE_31'		=> 'Il flash e\' scattato, modalita\' automatica, luce di ritorno rilevata',
	'EXIF_FLASH_CASE_32'		=> 'Funzione flash assente',
	'EXIF_FLASH_CASE_48'		=> 'Spento, funzione flash assente',
	'EXIF_FLASH_CASE_65'		=> 'Il flash e\' scattato, modalita\' riduzione occhi rossi',
	'EXIF_FLASH_CASE_69'		=> 'Il flash e\' scattato,modalita\' riduzione occhi rossi, luce di ritorno non rilevata',
	'EXIF_FLASH_CASE_71'		=> 'Il flash e\' scattato, modalita\' riduzione occhi rossi, luce di ritorno rilevata',
	'EXIF_FLASH_CASE_73'		=> 'Il flash e\' scattato, modalita\' flash obbligatoria, modalita\' riduzione occhi rossi',
	'EXIF_FLASH_CASE_77'		=> 'Il flash e\' scattato, modalita\' flash obbligatoria, modalita\' riduzione occhi rossi, luce di ritorno non rilevata',
	'EXIF_FLASH_CASE_79'		=> 'Il flash e\' scattato, modalita\' flash obbligatoria, modalita\' riduzione occhi rossi, luce di ritorno rilevata',
	'EXIF_FLASH_CASE_80'		=> 'Spento, Modalita\' riduzione occhi rossi',
	'EXIF_FLASH_CASE_88'		=> 'Auto, Non e\' scattato, Modalita\' riduzione occhi rossi',
	'EXIF_FLASH_CASE_89'		=> 'Il flash e\' scattato, modalita\' automatica, modalita\' riduzione occhi rossi',
	'EXIF_FLASH_CASE_93'		=> 'Il flash e\' scattato, modalita\' automatica, luce di ritorno non rilevata, modalita\' riduzione occhi rossi',
	'EXIF_FLASH_CASE_95'		=> 'Il flash e\' scattato, modalita\' automatica, luce di ritorno rilevata, modalita\' riduzione occhi rossi',
	'EXIF_FOCAL'				=> 'Lunghezza di Focus',
	'EXIF_FOCAL_EXP'			=> '%s mm',// 'EXIF_FOCAL' unit
	'EXIF_ISO'					=> 'Valore di velocita\' ISO',
	'EXIF_METERING_MODE'		=> 'Modalita\' di metratura',
	'EXIF_METERING_MODE_0'		=> 'Sconosciuta',
	'EXIF_METERING_MODE_1'		=> 'Media',
	'EXIF_METERING_MODE_2'		=> 'Media spostata al centro',
	'EXIF_METERING_MODE_3'		=> 'Spot',
	'EXIF_METERING_MODE_4'		=> 'Multi-Spot',
	'EXIF_METERING_MODE_5'		=> 'Modello',
	'EXIF_METERING_MODE_6'		=> 'Parziale',
	'EXIF_METERING_MODE_255'	=> 'Altra',
	'EXIF_NOT_AVAILABLE'		=> 'non disponibile',
	'EXIF_WHITEB'				=> 'Bilanciamento del bianco',
	'EXIF_WHITEB_AUTO'			=> 'Automatico',
	'EXIF_WHITEB_MANU'			=> 'Manuale',

	'DISP_EXIF_DATA'			=> 'Mostra dati Exif',
	'DISP_EXIF_DATA_EXP'		=> 'Questa caratteristica non puo\' essere utilizzatal momento, dato che la funzione “exif_read_data“ non e\' inclusa nella tua installazione di PHP.',
	'SHOW_EXIF'					=> 'mostra/nascondi',
	'VIEWEXIFS_DEFAULT'			=> 'Visualizza Dati-Exif in modo predefinito',

	'GALLERY_CORE_NOT_FOUND'		=> 'L\'estensione phpBB Gallery Core deve essere prima installata e abilitata.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'L\'estensione è stata abilitata con successo.',
]);
