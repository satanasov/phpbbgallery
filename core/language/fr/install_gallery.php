<?php
/**
*
* @package Gallery -  Install Extension [French]
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* @translator fr (c) pokyto aka le.poke http://www.lestontonsfraggers.com inspired by darky - http://www.foruminfopc.fr/ and Team http://www.phpbb-fr.com/
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
	'BBCODES_NEEDS_REPARSE'		=> 'Le BBCode a besoin d’être reconstruit.',

	'CAT_CONVERT'				=> 'Convertir phpBB2',
	'CAT_CONVERT_TS'			=> 'Convertir TS Gallery',
	'CAT_UNINSTALL'				=> 'Désinstaller Galerie phpBB',

	'CHECK_TABLES'				=> 'Vérifier les tables',
	'CHECK_TABLES_EXPLAIN'		=> 'Les tables suivantes doivent exister, pour qu’elles puissent être converties.',

	'CONVERT_SMARTOR_INTRO'			=> 'Convertir « Album-MOD » par Smartor en « Galerie phpBB »',
	'CONVERT_SMARTOR_INTRO_BODY'	=> 'Avec ce convertisseur, vous pouvez convertir les albums, images, notes et commentaires du <a href="http://www.phpbb.com/community/viewtopic.php?f=16&t=74772">Album-MOD</a> par Smartor (v2.0.56 testée) et du <a href="http://www.phpbbhacks.com/download/5028">Full Album Pack</a> (v1.4.1 testée) en Galerie phpBB.<br /><br /><strong>Remarque :</strong> Les <strong>permissions ne seront pas copiées</strong>.',
	'CONVERT_TS_INTRO'				=> 'Convertir « TS Gallery » en « Galerie phpBB »',
	'CONVERT_TS_INTRO_BODY'			=> 'Avec ce convertisseur, vous pouvez convertir les albums, images, notes et commentaires du <a href="http://www.phpbb.com/community/viewtopic.php?f=70&t=610509">TS Gallery</a> (v0.2.1 testée) en Galerie phpBB.<br /><br /><strong>Remarque :</strong> Les <strong>permissions ne seront pas copiées</strong>.',
	'CONVERT_COMPLETE_EXPLAIN'		=> 'La conversion de votre galerie en Galerie phpBB v%s a été réalisée avec succès.<br />Merci de vérifier que les paramètres ont bien été transférés avant d’activer votre forum, en supprimant le répertoire d’installation.<br /><br /><strong>Merci de noter que les permissions n’ont pas été copiées.</strong><br /><br />Vous devez aussi nettoyer votre base de données des anciennes entrées, ainsi que les images manquantes. Cela peut être fait dans « .MODs > Galerie phpBB > Nettoyage de la Galerie ».',

	'CONVERTED_ALBUMS'			=> 'Les albums ont été copiés avec succès.',
	'CONVERTED_COMMENTS'		=> 'Les commentaires ont été copiés avec succès.',
	'CONVERTED_IMAGES'			=> 'Les images ont été copiées avec succès.',
	'CONVERTED_MISC'			=> 'Autres paramètres convertis.',
	'CONVERTED_PERSONALS'		=> 'Les albums personnels ont été copiés avec succès.',
	'CONVERTED_RATES'			=> 'Les notes ont été copiées avec succès.',
	'CONVERTED_RESYNC_ALBUMS'	=> 'Resynchroniser les statistiques des albums.',
	'CONVERTED_RESYNC_COMMENTS'	=> 'Resynchroniser les commentaires.',
	'CONVERTED_RESYNC_COUNTS'	=> 'Resynchroniser les compteurs d’images.',
	'CONVERTED_RESYNC_RATES'	=> 'Resynchroniser les notes.',

	'FILE_DELETE_FAIL'				=> 'Le fichier ne peut être supprimé. Vous devez le supprimer manuellement.',
	'FILE_STILL_EXISTS'				=> 'Le fichier existe encore',
	'FILES_REQUIRED_EXPLAIN'		=> '<strong>Requis</strong> - Afin de fonctionner correctement, la Galerie phpBB doit pouvoir accéder ou écrire certains fichiers ou répertoires. Si vous voyez « Non accessible en écriture », vous devez modifier les permissions du fichier ou du répertoire, afin de permettre à phpBB de l’écrire.',
	'FILES_DELETE_OUTDATED'			=> 'Supprimez les fichiers obsolètes',
	'FILES_DELETE_OUTDATED_EXPLAIN'	=> 'Lorsque vous cliquez sur « Supprimer les fichiers », ils seront complètement supprimés et il ne sera pas possible de les restaurer !<br /><br />Remarque :<br />Si vous avez d’autres styles ou d’autres packs de langues, veuillez les supprimer manuellement.',
	'FILES_OUTDATED'				=> 'Fichiers obsolètes',
	'FILES_OUTDATED_EXPLAIN'		=> '<strong>Obsolète</strong> - Afin d’éviter les tentatives de piratage, merci de supprimer les fichiers suivants.',
	'FOUND_INSTALL'					=> 'Installation en double',
	'FOUND_INSTALL_EXPLAIN'			=> '<strong>Installation en double</strong> - Une autre installation de la galerie a été trouvée ! Si vous continuez, toutes les données existantes seront écrasées. Tous les albums, toutes les images et tous les commentaires seront supprimés ! <strong>C’est pourquoi une %1$smise à jour%2$s est recommandée.</strong>',
	'FOUND_VERSION'					=> 'La version suivante a été trouvée.',
	'FOUNDER_CHECK'					=> 'Vous êtes le/la « Fondateur(trice) » de ce forum.',
	'FOUNDER_NEEDED'				=> 'Vous devez être le/la « Fondateur(trice) » de ce forum!',

	'INSTALL_CONGRATS_EXPLAIN'	=> 'Vous avez installé la Galerie phpBB v%s avec succès.<br/><br/><strong>Merci de supprimer, déplacer ou renommer le répertoire « install » avant d’utiliser votre Forum. Si ce répertoire est toujours présent, seul le Panneau d’administration sera accessible.</strong>',
	'INSTALL_INTRO_BODY'		=> 'Avec cette option, il est possible d’installer la Galerie phpBB sur votre Forum.',

	'GOTO_GALLERY'				=> 'Aller à la Galerie phpBB',
	'GOTO_INDEX'				=> 'Aller à l’index du Forum',

	'MISSING_CONSTANTS'			=> 'Avant de pouvoir exécuter le script d’installation, vous devez charger les fichiers modifiés, surtout le fichier « includes/constants.php ».',
	'MODULES_CREATE_PARENT'		=> 'Créer un module parent standard',
	'MODULES_PARENT_SELECT'		=> 'Choisir le module parent',
	'MODULES_SELECT_4ACP'		=> 'Choisir le module parent pour le « Panneau d’administration »',
	'MODULES_SELECT_4LOG'		=> 'Choisir le module parent pour le « Journal de la Galerie »',
	'MODULES_SELECT_4MCP'		=> 'Choisir le module parent pour le « Panneau de modération »',
	'MODULES_SELECT_4UCP'		=> 'Choisir le module parent pour le « Panneau de l’utilisateur »',
	'MODULES_SELECT_NONE'		=> 'Aucun module parent',

	'NO_INSTALL_FOUND'			=> 'Aucune installation n’a été trouvée!',

	'OPTIONAL_EXIFDATA'				=> 'La fonction « exif_read_data » existe.',
	'OPTIONAL_EXIFDATA_EXP'			=> 'Le module « Informations sur les images » n’est pas chargé ou n’est pas installé.',
	'OPTIONAL_EXIFDATA_EXPLAIN'		=> 'Si la fonction existe, les informations des images sont affichées sur la page des images.',
	'OPTIONAL_IMAGEROTATE'			=> 'La fonction « imagerotate » existe.',
	'OPTIONAL_IMAGEROTATE_EXP'		=> 'Vous devriez mettre à jour votre version de GD, qui est actuellement à la version “%s“.',
	'OPTIONAL_IMAGEROTATE_EXPLAIN'	=> 'Si la fonction existe, vous pourrez faire pivoter les images qui ont été chargées et éditées.',

	'PAYPAL_DEV_SUPPORT'				=> '</p><div class="errorbox">
	<h3>Notes de l’auteur</h3>
	<p>Créer, maintenir et mettre à jour ce MOD, nécessite beaucoup de temps et d’efforts, donc si vous aimez ce MOD et que vous avez envie d’exprimer vos remerciements, vous pouvez effectuer un don, ce qui sera grandement apprécié. Mon ID Paypal est <strong>nickvergessen@gmx.de</strong>, ou contactez-moi pour avoir mon adresse e-mail.<br /><br />Le montant du don suggéré pour ce MOD est de 25,00€ (mais tout montant aidera).</p><br />
	<a href="http://www.flying-bits.org/go/paypal"><input type="submit" value="Faire un don Paypal" name="paypal" id="paypal" class="button1" /></a>
</div><p>',

	'PHP_SETTINGS'				=> 'Paramètres PHP',
	'PHP_SETTINGS_EXP'			=> 'Ces paramètres et configurations PHP sont requis pour l’installation et l’exécution de la Galerie.',
	'PHP_SETTINGS_OPTIONAL'		=> 'Paramètres PHP optionnels',
	'PHP_SETTINGS_OPTIONAL_EXP'	=> 'Ces paramètres PHP <strong>ne sont pas</strong> requis pour un usage normal, mais ils permettent d’exécuter plusieurs autres fonctions.',

	'REQ_GD_LIBRARY'			=> 'La librairie GD est installée.',
	'REQ_PHP_VERSION'			=> 'Version de PHP >= %s',
	'REQ_PHPBB_VERSION'			=> 'Version de phpBB >= %s',
	'REQUIREMENTS_EXPLAIN'		=> 'Avant de procéder à l’installation complète, effectuez quelques tests sur la configuration du serveur et des fichiers, afin de vous assurer que l’installation de la Galerie phpBB se passe correctement. Merci de lire tous les résultats et de ne pas continuer tant que tous les tests ne se sont pas bien passés.',

	'STAGE_ADVANCED_EXPLAIN'		=> 'Merci de choisir le module parent pour les modules de la Galerie. Dans un cas normal, les valeurs n’ont pas besoin d’être modifiées.',
	'STAGE_COPY_TABLE'				=> 'Copier les tables de la base de données',
	'STAGE_COPY_TABLE_EXPLAIN'		=> 'Les tables de la base de données, pour les données des albums et des images, ont les même noms dans TS Gallery et Galerie phpBB. Nous avons donc créé une copie pour être en mesure de convertir les données.',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'Les tables de la base de données, utilisées par la Galerie phpBB, ont été créées, puis les données initiales ont été insérées. Passez à l’étape suivante pour terminer l’installation de la Galerie phpBB.',
	'STAGE_DELETE_TABLES'			=> 'Vider la base de données',
	'STAGE_DELETE_TABLES_EXPLAIN'	=> 'Le contenu de la base de données de la Galerie a été supprimé. Passez à l’étape suivante pour terminer la désinstallation de la Galerie phpBB.',
	'SUPPORT_BODY'					=> 'Le support complet de la version stable actuelle de phpBB Gallery sera fait gratuitement. Cela inclut :</p><ul><li>L’installation</li><li>La configuration</li><li>Les questions techniques</li><li>Les problèmes liés à des bugs du logiciel</li><li>La mise à jour de la Release Candidate (RC) à la dernière version stable</li><li>La conversion du Album-MOD par Smartor pour phpBB 2.0.x à phpBB Gallery pour phpBB3</li><li>La convertion du TS Gallery à phpBB Gallery</li></ul><p>L’utilisation limitée des versions Beta est recommandée. S’il y a des mises à jour, il est recommandé de mettre à jour le MOD rapidement.</p><p>Le support est apporté sur les forums suivants :</p><ul><li><a href="http://www.flying-bits.org/">flying-bits.org - nickvergessen auteur du MOD</a></li><li><a href="http://www.phpbb.de/">phpbb.de</a></li><li><a href="http://www.phpbb.com/">phpbb.com</a></li></ul><p>',

	'TABLE_ALBUM'				=> 'Table incluant les images',
	'TABLE_ALBUM_CAT'			=> 'Table incluant les albums',
	'TABLE_ALBUM_COMMENT'		=> 'Table incluant les commentaires',
	'TABLE_ALBUM_CONFIG'		=> 'Table incluant la configuration',
	'TABLE_ALBUM_RATE'			=> 'Table incluant les notes',
	'TABLE_EXISTS'				=> 'existe',
	'TABLE_MISSING'				=> 'manquant',
	'TABLE_PREFIX_EXPLAIN'		=> 'Préfixe de l’installation phpBB2',

	'UNINSTALL_INTRO'					=> 'Bienvenue dans la désinstallation',
	'UNINSTALL_INTRO_BODY'				=> 'Avec cette option, il est possible de désinstaller la Galerie phpBB de votre forum phpBB.<br /><br /><strong>ATTENTION : Tous les albums, les images et les commentaires seront supprimés définitivement !</strong>',
	'UNINSTALL_REQUIREMENTS'			=> 'Nécessite',
	'UNINSTALL_REQUIREMENTS_EXPLAIN'	=> 'Avant de procéder à la désinstallation complète, effectuez quelques tests sur phpBB pour vous assurer que vous avez les autorisations nécessaires afin de désinstaller la Galerie phpBB.',
	'UNINSTALL_START'					=> 'Désinstaller',
	'UNINSTALL_FINISHED'				=> 'Désinstallation presque terminée',
	'UNINSTALL_FINISHED_EXPLAIN'		=> 'Vous avez désinstallé la Galerie phpBB avec succès.<br/><br/><strong>Maintenant, vous devez annuler toutes les étapes du fichier « install.xml » et supprimez les fichiers de la galerie.</strong>',

	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Ici, vous pouvez mettre à jour la Galerie phpBB.',

	'VERSION_NOT_SUPPORTED'		=> 'Désolé, mais les mises à jour inférieures à la version 1.0.6 ne sont pas supportées par cette version d’installation.',
));
