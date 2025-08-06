<?php
/**
 * phpBB Gallery - ACP Exif Extension [French Translation]
 *
 * @package   phpbbgallery/exif
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator pokyto (aka le.poke) <https://www.lestontonsfraggers.com>, inspired by darky <https://www.foruminfopc.fr/> and the phpBB-fr.com Team
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
	'EXIF_DATA'					=> 'Informations des images',
	'EXIF_APERTURE'				=> 'Nombre-F',
	'EXIF_CAM_MODEL'			=> 'Modèle d’appareil photo',
	'EXIF_DATE'					=> 'Image prise le',
	'EXIF_EXPOSURE'				=> 'Vitesse d’obturation',
	'EXIF_EXPOSURE_EXP'			=> '%s Sec',// 'EXIF_EXPOSURE' unit
	'EXIF_EXPOSURE_BIAS'		=> 'Angle d’exposition',
	'EXIF_EXPOSURE_BIAS_EXP'	=> '%s EV',// 'EXIF_EXPOSURE_BIAS' unit
	'EXIF_EXPOSURE_PROG'		=> 'Programme d’exposition',
	'EXIF_EXPOSURE_PROG_0'		=> 'Non défini',
	'EXIF_EXPOSURE_PROG_1'		=> 'Manuel',
	'EXIF_EXPOSURE_PROG_2'		=> 'Programme normal',
	'EXIF_EXPOSURE_PROG_3'		=> 'Priorité d’ouverture',
	'EXIF_EXPOSURE_PROG_4'		=> 'Priorité d’obturation',
	'EXIF_EXPOSURE_PROG_5'		=> 'Programme créatif (de préférence à la profondeur du champ)',
	'EXIF_EXPOSURE_PROG_6'		=> 'Programme d’action (de préférence à la vitesse d’obturation)',
	'EXIF_EXPOSURE_PROG_7'		=> 'Mode portrait (pour des photos avec gros plan et l’arrière-plan flou)',
	'EXIF_EXPOSURE_PROG_8'		=> 'Mode paysage (pour les photos de paysage avec l’arrière-plan cadré)',
	'EXIF_FLASH'				=> 'Flash',
	'EXIF_FLASH_CASE_0'			=> 'Flash non déclenché',
	'EXIF_FLASH_CASE_1'			=> 'Flash déclenché',
	'EXIF_FLASH_CASE_5'			=> 'Retour de lumière non détecté',
	'EXIF_FLASH_CASE_7'			=> 'Retour de lumière détecté',
	'EXIF_FLASH_CASE_8'			=> 'Activé, le flash ne s’est pas déclenché',
	'EXIF_FLASH_CASE_9'			=> 'Flash déclenché, obligatoire',
	'EXIF_FLASH_CASE_13'		=> 'Flash déclenché, obligatoire, avec un retour de lumière non détecté',
	'EXIF_FLASH_CASE_15'		=> 'Flash déclenché, obligatoire, avec retour de lumière détecté',
	'EXIF_FLASH_CASE_16'		=> 'Flash non déclenché, obligatoire',
	'EXIF_FLASH_CASE_20'		=> 'Désactivé, flash non déclenché, avec retour de lumière non détecté',
	'EXIF_FLASH_CASE_24'		=> 'Flash non déclenché, mode automatique',
	'EXIF_FLASH_CASE_25'		=> 'Flash déclenché, mode automatique',
	'EXIF_FLASH_CASE_29'		=> 'Flash déclenché, mode automatique, avec retour de lumière non détecté',
	'EXIF_FLASH_CASE_31'		=> 'Flash déclenché, mode automatique, avec retour de lumière détecté',
	'EXIF_FLASH_CASE_32'		=> 'Aucune fonction flash',
	'EXIF_FLASH_CASE_48'		=> 'Désactivé, aucune fonction flash',
	'EXIF_FLASH_CASE_65'		=> 'Flash détecté, yeux rouges réduits',
	'EXIF_FLASH_CASE_69'		=> 'Flash déclenché, yeux rouges réduits, avec retour de lumière non détecté',
	'EXIF_FLASH_CASE_71'		=> 'Flash déclenché, yeux rouges réduits, avec retour de lumière détecté',
	'EXIF_FLASH_CASE_73'		=> 'Flash déclenché, obligatoire, yeux rouges réduits',
	'EXIF_FLASH_CASE_77'		=> 'Flash déclenché, obligatoire, yeux rouges réduits, avec retour de lumière non détecté',
	'EXIF_FLASH_CASE_79'		=> 'Flash déclenché, obligatoire, yeux rouges réduits, avec retour de lumière détecté',
	'EXIF_FLASH_CASE_80'		=> 'Désactivé, yeux rouges réduits',
	'EXIF_FLASH_CASE_88'		=> 'Automatique, non déclenché, yeux rouges réduits',
	'EXIF_FLASH_CASE_89'		=> 'Flash déclenché, mode automatique, yeux rouges réduits',
	'EXIF_FLASH_CASE_93'		=> 'Flash déclenché, mode automatique, avec retour de lumière non détecté, yeux rouges réduits',
	'EXIF_FLASH_CASE_95'		=> 'Flash déclenché, mode automatique, avec retour de lumière détecté, yeux rouges réduits',
	'EXIF_FOCAL'				=> 'Longueur de focale',
	'EXIF_FOCAL_EXP'			=> '%s mm',// 'EXIF_FOCAL' unit
	'EXIF_ISO'					=> 'Sensibilité ISO',
	'EXIF_METERING_MODE'		=> 'Mode de mesure',
	'EXIF_METERING_MODE_0'		=> 'Inconnu',
	'EXIF_METERING_MODE_1'		=> 'Moyenne',
	'EXIF_METERING_MODE_2'		=> 'Moyennement pondéré au centre',
	'EXIF_METERING_MODE_3'		=> 'Position',
	'EXIF_METERING_MODE_4'		=> 'Positions multiples',
	'EXIF_METERING_MODE_5'		=> 'Modèle',
	'EXIF_METERING_MODE_6'		=> 'Partielle',
	'EXIF_METERING_MODE_255'	=> 'Autres',
	'EXIF_NOT_AVAILABLE'		=> 'Pas disponible',
	'EXIF_WHITEB'				=> 'Balance des blancs',
	'EXIF_WHITEB_AUTO'			=> 'Automatique',
	'EXIF_WHITEB_MANU'			=> 'Manuel',

	'DISP_EXIF_DATA'			=> 'Afficher les informations des images',
	'DISP_EXIF_DATA_EXP'		=> 'Cette fonctionnalité ne peut pas être utilisée pour le moment, car la fonction « exif_read_data » n’est pas incluse dans l’installation de votre PHP.',
	'SHOW_EXIF'					=> 'Afficher/Cacher',
	'VIEWEXIFS_DEFAULT'			=> 'Voir les informations des images par défaut',

	'GALLERY_CORE_NOT_FOUND'		=> 'L’extension phpBB Gallery Core doit d’abord être installée et activée.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'L’extension a été activée avec succès.',
]);
