<?php
/**
 * phpBB Gallery - ACP CleanUp Extension [Italian Translation]
 *
 * @package   phpbbgallery/acpcleanup
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator
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
	'ACP_GALLERY_CLEANUP'				=> 'Pulisci galleria',

	'ACP_GALLERY_CLEANUP_EXPLAIN'	=> 'Puoi cancellare alcuni rimasugli, da qui.',

	'CLEAN_AUTHORS_DONE'			=> 'Immagini senza un autore valido cancellate.',
	'CLEAN_CHANGED'					=> 'Modificato autore a "Ospite“.',
	'CLEAN_COMMENTS_DONE'			=> 'Commenti senza un autore valido cancellati.',
	'CLEAN_ENTRIES_DONE'			=> 'File privi di voce in database cancellati.',
	'CLEAN_GALLERY'					=> 'Pulisci galleria',
	'CLEAN_GALLERY_ABORT'			=> 'Ferma pulizia!',
	'CLEAN_NO_ACTION'				=> 'Nessuna azione completata. Qualcosa e\' andato storto!',
	'CLEAN_PERSONALS_DONE'			=> 'Album personali senza valido proprietario cancellati.',
	'CLEAN_PERSONALS_BAD_DONE'		=> 'Album personali degli utenti selezionati cancellati.',
	'CLEAN_PRUNE_DONE'				=> 'Immagini cancellate automaticamente con successo.',
	'CLEAN_PRUNE_NO_PATTERN'		=> 'Nessun modello di ricerca.',
	'CLEAN_SOURCES_DONE'			=> 'Immagini senza file cancellate.',

	'CONFIRM_CLEAN'					=> 'Questo passo non puo\' essere annullato!',
	'CONFIRM_CLEAN_AUTHORS'			=> 'Cancellare immagini senza autore valido?',
	'CONFIRM_CLEAN_COMMENTS'		=> 'Cancellare commenti senza autore valido?',
	'CONFIRM_CLEAN_ENTRIES'			=> 'Cancellare file senza voce in database?',
	'CONFIRM_CLEAN_PERSONALS'		=> 'Cancellare album personali senza un valido proprietario?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_PERSONALS_BAD'	=> 'Cancellare album personali degli utenti selezionati?<br /><strong>» %s</strong>',
	'CONFIRM_CLEAN_SOURCES'			=> 'Cancellare immagini senza file?',
	'CONFIRM_PRUNE'					=> 'Cancellare tutte le immagini che hanno le seguenti condizioni:<br /><br />%s<br />',

	'PRUNE'							=> 'Cancellazione automatica',
	'PRUNE_ALBUMS'					=> 'Cancellazione automatica degli album',
	'PRUNE_CHECK_OPTION'			=> 'Spunta questa opzione quando cancelli automaticamente le immagini.',
	'PRUNE_COMMENTS'				=> 'Meno di x commenti',
	'PRUNE_PATTERN_ALBUM_ID'		=> 'L\'immagine e\' in uno dei seguenti album:<br />&raquo; <strong>%s</strong>',
	'PRUNE_PATTERN_COMMENTS'		=> 'L\'immagine ha meno di <strong>%d</strong> commenti.',
	'PRUNE_PATTERN_RATES'			=> 'L\'immagine ha meno di <strong>%d</strong> valutazioni.',
	'PRUNE_PATTERN_RATE_AVG'		=> 'L\'immagine ha una media di valutazioni inferiore di <strong>%s</strong>.',
	'PRUNE_PATTERN_TIME'			=> 'L\'immagine e\' stata caricata prima di “<strong>%s</strong>“.',
	'PRUNE_PATTERN_USER_ID'			=> 'L\'immagine e\' stata caricata da uno dei seguenti utenti:<br />&raquo; <strong>%s</strong>',
	'PRUNE_RATINGS'					=> 'Meno di x valutazioni',
	'PRUNE_RATING_AVG'				=> 'Media valutazioni inferiore di',
	'PRUNE_RATING_AVG_EXP'			=> 'Cancella automaticamente soltanto le immagini con una media di valutazioni inferiore di “<samp>x.yz</samp>“.',
	'PRUNE_TIME'					=> 'Caricate prima',
	'PRUNE_TIME_EXP'				=> 'Cancella automaticamente soltanto le immagini che sono state caricate prima “<samp>YYYY-MM-DD</samp>“.',
	'PRUNE_USERNAME'				=> 'Caricate da',
	'PRUNE_USERNAME_EXP'			=> 'Cancella automaticamente soltanto le immagini da certi utenti. Per cancellare automaticamente le immagini degli "ospiti" seleziona la spunta oltre il box del nome utente.',

	//Log
	'LOG_CLEANUP_DELETE_FILES'		=> 'Cancellate %s immagini senza voci nel DB.',
	'LOG_CLEANUP_DELETE_ENTRIES'	=> 'Cancellate %s immagini senza file.',
	'LOG_CLEANUP_DELETE_NO_AUTHOR'	=> 'Cancellate %s immagini senza autore valido.',
	'LOG_CLEANUP_COMMENT_DELETE_NO_AUTHOR'	=> 'Cancellati %s commenti senza autore valido.',

	'MOVE_TO_IMPORT'	=> 'Sposta immagini alla cartella Import',
	'MOVE_TO_USER'		=> 'Sposta all\'utente',
	'MOVE_TO_USER_EXP'	=> 'Immagini e commenti verranno spostati come fossero dell\'utente che hai definito. Se non ne vengono selezionati verra\' usato l\'utente anonimo.',
	'CLEAN_USER_NOT_FOUND'	=> 'L\'utente selezionato non esiste!',

	'GALLERY_CORE_NOT_FOUND'		=> 'L\'estensione phpBB Gallery Core deve essere prima installata e abilitata.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'L\'estensione è stata abilitata con successo.',
]);
