<?php
/**
 * phpBB Gallery - ACP Core Extension [Italian Translation]
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
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

$lang = array_merge($lang, array(
	'ACCESS_CONTROL_ALL'			=> 'Tutti',
	'ACCESS_CONTROL_REGISTERED'		=> 'Utenti registrati',
	'ACCESS_CONTROL_NOT_FOES'		=> 'Utenti registrati, eccetto i tuoi nemici',
	'ACCESS_CONTROL_FRIENDS'		=> 'Solo i tuoi amici',
	'ACCESS_CONTROL_SPECIAL_FRIENDS'		=> 'Solo i tuoi amici speciali',
	'ALBUMS'						=> 'Albums',
	'ALBUM_ACCESS'					=> 'Consenti accesso per',
	'ALBUM_ACCESS_EXPLAIN'			=> 'Puoi usare la tua %1$slista Amici e Nemici%2$s per controllare l’accesso all’album. In ogni caso i <strong>moderatori</strong> possono <strong>sempre</strong> accedere all’album.',
	'ALBUM_DESC'					=> 'Descrizione album',
	'ALBUM_NAME'					=> 'Nome album',
	'ALBUM_PARENT'					=> 'Album padre',
	'ATTACHED_SUBALBUMS'			=> 'Sotto-album allegati',

	'CREATE_PERSONAL_ALBUM'			=> 'Crea album personale',
	'CREATE_SUBALBUM'				=> 'Crea sotto-album',
	'CREATE_SUBALBUM_EXP'			=> 'È possibile allegare un nuovo sotto-album alla tua galleria personale.',
	'CREATED_SUBALBUM'				=> 'Sotto-album creato con successo',
	'DELETE_ALBUM'					=> 'Cancella album',
	'DELETE_ALBUM_CONFIRM'			=> 'Vuoi cancellare questo album con i sotto-albums e immagini collegate?',
	'DELETED_ALBUMS'				=> 'Albums cancellati con successo',

	'EDIT'							=> 'Modifica',
	'EDIT_ALBUM'					=> 'Modifica album',
	'EDIT_SUBALBUM'					=> 'Modifica sotto-album',
	'EDIT_SUBALBUM_EXP'				=> 'Puoi modificare i tuoi albums.',
	'EDITED_SUBALBUM'				=> 'Album modificato con successo',

	'GOTO'							=> 'Torna a',

	'MANAGE_SUBALBUMS'				=> 'Gestione sotto-albums personali',
	'MISSING_ALBUM_NAME'			=> 'Scrivi un nome per l’album',

	'NEED_INITIALISE'				=> 'Non hai un tuo album personale.',
	'NO_ALBUM_STEALING'				=> 'Non sei autorizzato a gestire gli albums di altri utenti.',
	'NO_MORE_SUBALBUMS_ALLOWED'		=> 'Hai raggiunto il massimo di sotto-albums per il tuo album personale',
	'NO_PARENT_ALBUM'				=> '&laquo;-- nessun album padre',
	'NO_PERSALBUM_ALLOWED'			=> 'Non hai i permessi per creare il tuo album personale',
	'NO_PERSONAL_ALBUM'				=> 'Non hai un album personale. Puoi creare il tuo album personale con alcuni aotto-albums.<br />Negli albums personali solo i proprietare possono inviare immagini.',
	'NO_SUBALBUMS'					=> 'Non ci sono albums allegati',
	'NO_SUBSCRIPTIONS'				=> 'Non hai sottoscritto nessuna immagine.',
	'NO_SUBSCRIPTIONS_ALBUM'		=> 'You are not subscribed to an album.',

	'PARSE_BBCODE'					=> 'Analizza BBCode',
	'PARSE_SMILIES'					=> 'Analizza faccine',
	'PARSE_URLS'					=> 'Analizza links',
	'PERSONAL_ALBUM'				=> 'Album personale',

	'UNSUBSCRIBE'					=> 'Elimina sottoscrizione',
	'USER_ALLOW_COMMENTS'			=> 'Permetti agli utenti di commentare le tue immagini',

	'YOUR_SUBSCRIPTIONS'			=> 'Puoi vedere gli albums e le immagini che hai sottoscritto.',

	'WATCH_CHANGED'					=> 'Configurazione salvata',
	'WATCH_COM'						=> 'Sottoscrivi immagini commentate',
	'WATCH_NOTE'					=> 'Questa opzione ha effetto solo sulle nuove immagini. Tutte le altre immagini necessitano di essere aggiunte come “sottoscritte“.',
	'WATCH_OWN'						=> 'Sottoscrivi immagini',

	'RRC_ZEBRA'						=> 'Nascondi dai nemici in RRC',
	'RRC_ZEBRA_EXPLAIN'				=> 'Nascondi le immagini negli album dai nemici nelle parti dei Commenti, Recenti e Casuali dell\'Indice.<br /><strong>ATTENZIONE!</strong> Questo non cancellera\' le immagini caricate negli album comuni e pubblici.'
));
