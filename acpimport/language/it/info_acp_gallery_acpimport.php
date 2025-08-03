<?php
/**
*
* @package phpBB Gallery - ACP Import Extension [English]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
	'ACP_IMPORT_ALBUMS'				=> 'Importa Immagini',
	'ACP_IMPORT_ALBUMS_EXPLAIN'		=> 'Da qui puoi importare in massa immagini dal file system. Prima di importare le immagini assicurati di ridimensionarle manualmente.',

	'IMPORT_ALBUM'					=> 'Album in cui importare immagini:',
	'IMPORT_DEBUG_MES'				=> '%1$s immagini importate. Rimangono ancora %2$s immagini.',
	'IMPORT_DIR_EMPTY'				=> 'La cartella %s e\' vuota. Devi caricarci le immagini per importarle.',
	'IMPORT_FINISHED'				=> 'Tutte le %1$s immagini importate con successo.',
	'IMPORT_FINISHED_ERRORS'		=> '%1$s immagini sono state importate con successo, ma sono stati riscontrati i seguenti errori:<br /><br />',
	'IMPORT_MISSING_ALBUM'			=> 'Seleziona un album in cui importare le immagini.',
	'IMPORT_SELECT'					=> 'Scegli le immagini che vuoi importare. Le immagini importate con successo vengono cancellate. Tutte le altri immagini restano disponibili.',
	'IMPORT_SCHEMA_CREATED'			=> 'Lo schema di importazione e\' stato creato con successo, attendi mentre le immagini vengono importate.',
	'IMPORT_USER'					=> 'Caricate da',
	'IMPORT_USER_EXP'				=> 'Puoi aggiungere le immagini a un altro utente da qui.',
	'IMPORT_USERS_PEGA'				=> 'Carica alla galleria personale dell\'utente.',

	'MISSING_IMPORT_SCHEMA'			=> 'Lo schema di importazione specificato (%s) non e\' stato trovato.',

	'NO_FILE_SELECTED'				=> 'Devi selezionare almeno un file.',
));
