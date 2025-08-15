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
	'BBCODES_NEEDS_REPARSE'		=> 'Il BBCode deve essere ricostruito.',

	'CAT_CONVERT'				=> 'converti phpBB2',
	'CAT_CONVERT_TS'			=> 'converti TS Gallery',
	'CAT_UNINSTALL'				=> 'disinstalla phpBB Gallery',

	'CHECK_TABLES'				=> 'Controlla tabelle',
	'CHECK_TABLES_EXPLAIN'		=> 'Le seguenti tabelle necessitano di conversione.',

	'CONVERT_SMARTOR_INTRO'			=> 'Converti da “Album-MOD“ di smartor a “phpBB Gallery“',
	'CONVERT_SMARTOR_INTRO_BODY'	=> 'Con questo convertitore, puoi convertire i tuoi albums, immagini, voti e commenti da <a href="http://www.phpbb.com/community/viewtopic.php?f=16&t=74772">Album-MOD</a> di Smartor (testato v2.0.56) e <a href="http://www.phpbbhacks.com/download/5028">Full Album Pack</a> (testato v1.4.1) a phpBB Gallery.<br /><br /><strong>Nota:</strong> I <strong>permessi</strong> non saranno <strong>copiati</strong>.',
	'CONVERT_TS_INTRO'				=> 'Converti da “TS Gallery“ a “phpBB Gallery“',
	'CONVERT_TS_INTRO_BODY'			=> 'Con questo convertitore, puoi convertire i tuoi albums, immagini, voti e commenti da <a href="http://www.phpbb.com/community/viewtopic.php?f=70&t=610509">TS Gallery</a> (testato v0.2.1) a phpBB Gallery.<br /><br /><strong>Nota:</strong> I <strong>permessi</strong> non saranno <strong>copiati</strong>.',
	'CONVERT_COMPLETE_EXPLAIN'		=> 'La conversione della tua gallery a phpBB Gallery v%s è stata completata con successo.<br />Assicurati di aver trasferito tutti i file prima di attivare il tuo forum e di cancellare la cartella install.<br /><br /><strong>I permessi non saranno copiati.</strong><br /><br />Devi anche pulire il tuo database da vecchie voci, relative a immagini non più esistenti. Questo può essere fatto in ".MODs > phpBB Gallery > Pulisci gallery".',

	'CONVERTED_ALBUMS'			=> 'Gli albums sono stati copiati con successo.',
	'CONVERTED_COMMENTS'		=> 'I commenti sono stati copiati con successo.',
	'CONVERTED_IMAGES'			=> 'Le immagini sono stati copiati con successo.',
	'CONVERTED_MISC'			=> 'Convertite altre funzioni.',
	'CONVERTED_PERSONALS'		=> 'Gli album personali sono stati copiati con successo.',
	'CONVERTED_RATES'			=> 'I voti sono stati copiati con successo.',
	'CONVERTED_RESYNC_ALBUMS'	=> 'Sincronizzazione statistiche album.',
	'CONVERTED_RESYNC_COMMENTS'	=> 'Sincronizzazione commenti.',
	'CONVERTED_RESYNC_COUNTS'	=> 'Sincronizzazione contatore immagini.',
	'CONVERTED_RESYNC_RATES'	=> 'Sincronizzazione voti.',

	'FILE_DELETE_FAIL'				=> 'Il file non può essere eliminato, occorre eliminarlo manualmente',
	'FILE_STILL_EXISTS'				=> 'Il file cercato non esiste',
	'FILES_REQUIRED_EXPLAIN'		=> '<strong>Richiesto</strong> - Per poter funzionare correttamente phpBB Gallery deve essere in grado di accedere o scrivere ad alcuni file o directory. Se vedi “ Non scrivibile ” è necessario modificare le autorizzazioni su file o directory per consentire a phpBB di scrivere in esso.',
	'FILES_DELETE_OUTDATED'			=> 'Elimina i file obsoleti',
	'FILES_DELETE_OUTDATED_EXPLAIN'	=> 'Se sceglii di eliminare i file, essi saranno completamente eliminati e non potranno essere ripristinati!<br /><br />Nota:<br />Se hai stili e linguaggi installati, è necessario cancellarli manualmente.',
	'FILES_OUTDATED'				=> 'Files obsoleti',
	'FILES_OUTDATED_EXPLAIN'		=> '<strong>Obsoleti</strong> - Per eliminare il rischio di attacchi hackers, elimina definitivamente i seguenti files.',
	'FOUND_INSTALL'					=> 'Doppia installazione',
	'FOUND_INSTALL_EXPLAIN'			=> '<strong>Doppia installazione</strong> - E’ stata trovata già una galeria installata! Se continui, tutti i dati esistenti saranno sovrascritti. Tutti gli albums, immagini e commenti saranno cancellati! <strong>Per questo un %1$saggiornamento%2$s è raccomandato.</strong>',
	'FOUND_VERSION'					=> 'E’ stata trovata la seguente versione',
	'FOUNDER_CHECK'					=> 'Sei un "Founder" di questa board',
	'FOUNDER_NEEDED'				=> 'Devi essere un “Founder“ di questa board!',

	'INSTALL_CONGRATS_EXPLAIN'	=> '<p>Hai correttamente installato phpBB Gallery v%s.<br/><br/><strong>Ora cancella, sposta o rinomina la cartella install prima di usare il tuo forum. Se questa directory è ancora presente, sarà consentito solo l’accesso in amministrazione (ACP).</strong></p>',
	'INSTALL_INTRO_BODY'		=> 'Con questa opzione, è possibile installare phpBB Gallery sul tuo forum.',

	'GOTO_GALLERY'				=> 'Vai su phpBB Gallery',
	'GOTO_INDEX'				=> 'Vai all’indice del forum',

	'MISSING_CONSTANTS'			=> 'Prima di eseguire lo script di installazione, devi caricare i tuoi files modificati, in particolare il file includes/constants.php.',
	'MODULES_CREATE_PARENT'		=> 'Crea modulo standard padre',
	'MODULES_PARENT_SELECT'		=> 'Scegli il modulo padre',
	'MODULES_SELECT_4ACP'		=> 'Scegli il modulo padre per “pannello di controllo amministrativo“',
	'MODULES_SELECT_4LOG'		=> 'Scegli il modulo padre per “log gallery“',
	'MODULES_SELECT_4MCP'		=> 'Scegli il modulo padre per “pannelo di controllo moderatore“',
	'MODULES_SELECT_4UCP'		=> 'Scegli il modulo padre per “pannello di controllo utente“',
	'MODULES_SELECT_NONE'		=> 'nessun modulo padre',

	'NO_INSTALL_FOUND'			=> 'Nessuna installazione trovata!',

	'OPTIONAL_EXIFDATA'				=> 'La funzione “exif_read_data“ esiste',
	'OPTIONAL_EXIFDATA_EXP'			=> 'Il modulo exif-module non è installato o non esiste.',
	'OPTIONAL_EXIFDATA_EXPLAIN'		=> 'Se la funzione esiste, i dati exif delle immagini sono visualizzate nella pagina immagini.',
	'OPTIONAL_IMAGEROTATE'			=> 'La funzione “imagerotate“ esiste',
	'OPTIONAL_IMAGEROTATE_EXP'		=> 'Devi aggiornare la tua libreria GD, quella attuale è "%s".',
	'OPTIONAL_IMAGEROTATE_EXPLAIN'	=> 'Se la funzione esiste, puoi ruotare le immagini quando le carichi o le modifichi.',

	'PAYPAL_DEV_SUPPORT'				=> '</p><div class="errorbox">
	<h3>Note autore</h3>
	<p>La creazione, il mantenimento e gli aggiornamenti per questa MOD richiedono molto tempo e fatica, se ti piace questa MOD e vuoi aiutarmi con una donazione puoi usare la mia ID Paypal <strong>nickvergessen@gmx.de</strong>, o contattarmi al mio indirizzo email.<br /><br />La donazione suggerita è di 25,00€ (ma qualunque importo potrà aiutarmi).</p><br />
	<a href="http://www.flying-bits.org/go/paypal"><input type="submit" value="Make PayPal-Donation" name="paypal" id="paypal" class="button1" /></a>
</div><p>',

	'PHP_SETTINGS'				=> 'Configurazioni PHP',
	'PHP_SETTINGS_EXP'			=> 'Queste sono le configurazioni richieste per installare la galleria.',
	'PHP_SETTINGS_OPTIONAL'		=> 'Configurazioni PHP opzionali',
	'PHP_SETTINGS_OPTIONAL_EXP'	=> 'Queste configuraioni PHP non sono richieste per il normale uso, ma sono necessarie per utilizzare nuovi strumenti.',

	'REQ_GD_LIBRARY'			=> 'GD Library è installato',
	'REQ_PHP_VERSION'			=> 'Versione php >= %s',
	'REQ_PHPBB_VERSION'			=> 'Versione phpBB >= %s',
	'REQUIREMENTS_EXPLAIN'		=> 'Prima di procedere con l’installazione completa, phpBB effettuerà alcuni test sulla tua configurazione del server e file per garantire che possano essere installati ed eseguiti correttamente su phpBB. Assicurati di leggere attentamente attraverso i risultati e non procedere fino a quando vengono passati tutti i test necessari.',

	'STAGE_ADVANCED_EXPLAIN'		=> 'Hai scelto i moduli principali per la gallery. Per un’installazione normale non è consigliabile effettuare modifiche.',
	'STAGE_COPY_TABLE'				=> 'Copia tabelle database',
	'STAGE_COPY_TABLE_EXPLAIN'		=> 'Le tabelle del database per l’album e dati utente hanno gli stessi nomi in TS Gallery e phpBB Gallery. Puoi così copiarli e convertire i dati.',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'Le tabelle del database utilizzate da phpBB gallery sono state create e popolate con alcuni dati iniziali. Passare alla schermata successiva per completare l’installazione di phpBB gallery.',
	'STAGE_DELETE_TABLES'			=> 'Pulizia database',
	'STAGE_DELETE_TABLES_EXPLAIN'	=> 'Il database della Mod Gallery è stato cancellato. Procedi con gli step successivi per terminare la disinstallazione della phpBB Gallery.',
	'SUPPORT_BODY'					=> 'Pieno supporto è previsto per la corrente versione stabile di phpBB, gratuitamente. Questo include::</p><ul><li>installazione</li><li>configurazione</li><li>questioni tecniche</li><li>problemi relativi a bugs nel software</li><li>aggiornamenti da versioni Release Candidate (RC) a versione stabile</li><li>conversione da Smartors Album-MOD per phpBB 2.0.x a phpBB Gallery per phpBB3</li><li>conversione da TS Gallery a phpBB Gallery</li></ul><p>L’uso delle versioni Beta è raccomandato in modo limitato. Gli aggiornamenti sono consigliati.</p><p>Il supporto è dato sui seguenti forum: </p><ul><li><a href="http://www.flying-bits.org/">flying-bits.org  MOD Autor nickvergessen’s board</a></li><li><a href="http://www.phpbb.de/">phpbb.de</a></li><li><a href="http://www.phpbb.com/">phpbb.com</a></li></ul><p>',

	'TABLE_ALBUM'				=> 'la tabella include le immagini',
	'TABLE_ALBUM_CAT'			=> 'la tabella include gli albums',
	'TABLE_ALBUM_COMMENT'		=> 'la tabella include i commenti',
	'TABLE_ALBUM_CONFIG'		=> 'la tabella include la configurazione',
	'TABLE_ALBUM_RATE'			=> 'la tabella include i voti',
	'TABLE_EXISTS'				=> 'esiste',
	'TABLE_MISSING'				=> 'non esiste',
	'TABLE_PREFIX_EXPLAIN'		=> 'Prefisso di phpBB',

	'UNINSTALL_INTRO'					=> 'Benvenuto nel processo di disinstallazione',
	'UNINSTALL_INTRO_BODY'				=> 'Con questa opzione è possibile disinstallare la phpBB Gallery dalla tua board.<br /><br /><strong>ATTENZIONE: tutti gli albums, immagini e commenti saranno cancellati!</strong>',
	'UNINSTALL_REQUIREMENTS'			=> 'Requisiti',
	'UNINSTALL_REQUIREMENTS_EXPLAIN'	=> 'Prima di procedere con la disinstallazione phpBB effettuerà alcuni test per verificare che tu abbia i permessi per disinstallare phpBB Gallery.',
	'UNINSTALL_START'					=> 'Disinstallazione',
	'UNINSTALL_FINISHED'				=> 'Disinstallazione quasi terminata',
	'UNINSTALL_FINISHED_EXPLAIN'		=> 'Hai disinstallato phpBB Gallery con successo.<br/><br/><strong>Ora hai solo bisogno di annullare la procedura dell’install.xml e cancellare i files della galleria. Successivamente il tuo ACP sarà completamente libero dalla galleria.</strong>',

	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Puoi aggiornare la tua versione di phpBB Gallery.',

	'VERSION_NOT_SUPPORTED'			=> 'Spiacente, ma gli aggiornamenti dalle versioni inferiori a < 0.2.0 non sono supportate con questo processo di installazione.',

	'GALLERY_SUB_EXT_UNINSTALL' => array(
		1 => 'È necessario disinstallare l’estensione: <br /><strong>%s</strong><br /> prima di disinstallare l’estensione principale.',
		2 => 'È necessario disinstallare le estensioni: <br /><strong>%s</strong><br /> prima di disinstallare l’estensione principale.',
	),
));
