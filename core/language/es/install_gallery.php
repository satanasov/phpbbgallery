<?php
/**
 * phpBB Gallery - ACP Core Extension [Spanish Translation]
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
	'BBCODES_NEEDS_REPARSE'			=>	'El BBCode necesita ser reconstruido.',

	'CAT_CONVERT' 					=>	'convertir phpBB2',
	'CAT_CONVERT_TS'				=>	'Convertir Galería de TS',
	'CAT_UNINSTALL' 				=>	'Desinstalación de la galería phpBB',

	'CHECK_TABLES' 					=>	'Comprobar tablas',
	'CHECK_TABLES_EXPLAIN' 			=>	'Las siguientes tablas deben existir, para que puedan ser convertidas.',

	'CONVERT_SMARTOR_INTRO' 		=>	'Convertidor de "Album-MOD" por smartor a "phpBB Gallery"',
	'CONVERT_SMARTOR_INTRO_BODY' 	=>	'Con este convertidor, puede convertir sus álbumes, imágenes, tarifas y comentarios de la <a href = "http://www.phpbb.com/community/viewtopic.php?f=16&t=74772" > Album-MOD </a> por Smartor (probado v2.0.56) y <a href="http://www.phpbbhacks.com/download/5028"> Paquete completo de álbumes </a> (probado v1.4.1) A la galería phpBB. <br /> <br /> <strong> Nota: </strong> los <strong> permisos </strong> no se copiarán </strong>.',
	'CONVERT_TS_INTRO' 				=>	'Convertidor desde "TS Gallery" a "phpBB Gallery"',
	'CONVERT_TS_INTRO_BODY' 		=>	'Con este convertidor, puede convertir sus álbumes, imágenes, tarifas y comentarios de la <a href = "http://www.phpbb.com/community/viewtopic.php?f=70&t=610509" > Galería TS </a> (probado v0.2.1) en la galería phpBB. <br /> <br /> <strong> Nota: </strong> los <strong> permisos </strong> no serán <strong> Copiado </strong>. ',
	'CONVERT_COMPLETE_EXPLAIN' 		=>	'La conversión de tu galería a phpBB Gallery v%s tuvo éxito. <br /> Por favor, asegúrate de que los ajustes se transfirieron correctamente antes de habilitar tu directorio borrando el directorio de instalación. <br /> <br /> < Strong> Tenga en cuenta que los permisos no se copiaron. </strong> <br /> <br /> También debe limpiar su base de datos de entradas antiguas, donde faltan las imágenes. Esto se puede hacer en ".MODs> phpBB Gallery> Galería de limpieza". ',

	'CONVERTED_ALBUMS' 				=>	'Los álbumes se copiaron con éxito.',
	'CONVERTED_COMMENTS' 			=>	'Los comentarios se copiaron correctamente.',
	'CONVERTED_IMAGES' 				=>	'Las imágenes se copiaron con éxito.',
	'CONVERTED_MISC' 				=>	'Convertido cosas misceláneas.',
	'CONVERTED_PERSONALS' 			=>	'Los álbumes personales se copiaron con éxito.',
	'CONVERTED_RATES' 				=>	'Las tasas se copiaron con éxito.',
	'CONVERTED_RESYNC_ALBUMS' 		=>	'Resincronizar las estadísticas del álbum.',
	'CONVERTED_RESYNC_COMMENTS' 	=>	'Resincronizar comentarios.',
	'CONVERTED_RESYNC_COUNTS' 		=>	'Resincronizar imagecounters.',
	'CONVERTED_RESYNC_RATES' 		=>	'Resincronizar las tasas.',

	'FILE_DELETE_FAIL' 				=>	'No se pudo eliminar el archivo, es necesario eliminarlo manualmente',
	'FILE_STILL_EXISTS' 			=>	'El archivo aún existe',
	'FILES_REQUIRED_EXPLAIN' 		=>	'<strong>Obligatorio</strong> - Para funcionar correctamente, phpBB Gallery debe poder acceder o escribir en ciertos archivos o directorios. Si ve "No escritura" necesita cambiar los permisos en el archivo o directorio para permitir que phpBB le escriba. ',
	'FILES_DELETE_OUTDATED' 		=>	'Eliminar archivos obsoletos',
	'FILES_DELETE_OUTDATED_EXPLAIN' =>	'Cuando haga clic para borrar los archivos, se eliminan completamente y no se pueden restaurar! <br /><br /> Tenga en cuenta: <br /> Si tiene más estilos e idiomas instalados, necesita Para borrar los archivos a mano. ',
	'FILES_OUTDATED' 				=>	'Archivos obsoletos',
	'FILES_OUTDATED_EXPLAIN' 		=>	'<strong>Desactualizado</strong> - Para negar los intentos de piratería, elimine los siguientes archivos.',
	'FOUND_INSTALL' 				=>	'Doble instalación',
	'FOUND_INSTALL_EXPLAIN' 		=>	'<strong>Doble instalación</strong> - Se encontró una instalación de la galería. Si continúa aquí, sobrescribirá todos los datos existentes. ¡Todos los álbumes, imágenes y comentarios serán borrados! <Strong>Por eso se recomienda una %1$sactualización%2$s</strong>,',
	'FOUND_VERSION' 				=>	'Se encontró la siguiente versión',
	'FOUNDER_CHECK' 				=>	'Usted es un "Fundador" de este foro',
	'FOUNDER_NEEDED' 				=>	'Debes ser un "Fundador" de este foro!',

	'INSTALL_CONGRATS_EXPLAIN' 		=>	'Ahora has instalado correctamente phpBB Gallery v%s. <br/><br/> <strong>Ahora debes borrar, mover o renombrar el directorio de instalación antes de usar tu tabla. Si este directorio aún está presente, sólo se podrá acceder al panel de control de administración (ACP).</strong>',
	'INSTALL_INTRO_BODY' 			=>	'Con esta opción, es posible instalar phpBB Gallery en tu directorio.',

	'GOTO_GALLERY' 					=>	'Ir a la galería phpBB',
	'GOTO_INDEX' 					=>	'Ir al Índice del Principal',

	'MISSING_CONSTANTS' 			=>	'Antes de poder ejecutar el script de instalación, debe cargar sus archivos editados, especialmente el archivo includes/constants.php.',
	'MODULES_CREATE_PARENT' 		=>	'Crear padre-módulo estándar',
	'MODULES_PARENT_SELECT' 		=>	'Elija el módulo padre',
	'MODULES_SELECT_4ACP' 			=>	'Elija el módulo principal para "panel de control admin"',
	'MODULES_SELECT_4LOG' 			=>	'Elija el módulo principal para "Galería de registro"',
	'MODULES_SELECT_4MCP' 			=>	'Elija el módulo principal para "panel de control de moderación"',
	'MODULES_SELECT_4UCP' 			=>	'Elija el módulo principal para "panel de control del usuario"',
	'MODULES_SELECT_NONE' 			=>	'sin módulo padre',

	'NO_INSTALL_FOUND' 				=>	'No se encontró ninguna instalación!',

	'OPTIONAL_EXIFDATA' 			=>	'Función "exif_read_data" existe',
	'OPTIONAL_EXIFDATA_EXP' 		=>	'El módulo exif no está cargado o instalado.',
	'OPTIONAL_EXIFDATA_EXPLAIN' 	=>	'Si la función existe, los datos exif de las imágenes se muestran en la página de imagen.',
	'OPTIONAL_IMAGEROTATE' 			=>	'Función "imagerotate" existe',
	'OPTIONAL_IMAGEROTATE_EXP' 		=>	'Debes actualizar tu versión de GD, que es actualmente "%s".',
	'OPTIONAL_IMAGEROTATE_EXPLAIN' 	=>	'Si la función existe, puede girar las imágenes mientras las sube y edita.',

	'PAYPAL_DEV_SUPPORT' 			=>	'</p> <div class = "errorbox">
	<h3> Notas de autor </h3>
	<p>Crear, mantener y actualizar este MOD requiere/requiere mucho tiempo y esfuerzo, así que si te gusta este MOD y tiene el deseo de expresar tus gracias a través de una donación, eso sería muy apreciado. Mi ID de Paypal es <strong>nickvergessen@gmx.de</strong>, o póngase en contacto conmigo para mi dirección de correo. <br /><br />La cantidad de donación sugerida para este MOD es de 25.00 € (pero cualquier cantidad ayudará). </p><br />
	<a href="http://www.flying-bits.org/go/paypal"><input type="submit" value="Hacer PayPal-Donación" name="paypal" id="paypal" class=Button1"/></a>
</div><p>',

	'PHP_SETTINGS' 					=>	'Ajustes de PHP',
	'PHP_SETTINGS_EXP' 				=>	'Estos ajustes y configuraciones de PHP son necesarios para instalar y ejecutar la galería.',
	'PHP_SETTINGS_OPTIONAL' 		=>	'Ajustes opcionales de PHP',
	'PHP_SETTINGS_OPTIONAL_EXP' 	=>	'Estas configuraciones de PHP son <strong> NOT </strong> necesarias para el uso normal, pero darán algunas características adicionales.',

	'REQ_GD_LIBRARY' 				=>	'Biblioteca de GD está instalada',
	'REQ_PHP_VERSION' 				=>	'versión de php >= %s',
	'REQ_PHPBB_VERSION' 			=>	'versión de phpBB >= %s',
	'REQUIREMENTS_EXPLAIN' 			=>	'Antes de continuar con la instalación completa phpBB realizará algunas pruebas en la configuración y los archivos del servidor para asegurarse de que es capaz de instalar y ejecutar phpBB Gallery. Asegúrese de leer detenidamente los resultados y no proceda hasta que se hayan superado todas las pruebas necesarias. ',

	'STAGE_ADVANCED_EXPLAIN' 		=>	'Por favor elija el módulo padre para los módulos de la galería. En el caso normal no debe cambiarlos. ',
	'STAGE_COPY_TABLE' 				=>	'Copiar tablas de base de datos',
	'STAGE_COPY_TABLE_EXPLAIN' 		=>	'Las tablas de base de datos para el álbum y el usuario tienen los mismos nombres en la Galería de TS y en la Galería de phpBB. Así que creamos una copia para poder convertir los datos. ',
	'STAGE_CREATE_TABLE_EXPLAIN' 	=>	'Las tablas de base de datos utilizadas por phpBB Gallery han sido creadas y rellenadas con algunos datos iniciales. Vaya a la siguiente pantalla para terminar de instalar phpBB Gallery. ',
	'STAGE_DELETE_TABLES' 			=>	'Limpiar base de datos',
	'STAGE_DELETE_TABLES_EXPLAIN' 	=>	'Se eliminó el contenido de la base de datos de Gallery-MOD. Vaya a la siguiente pantalla para terminar de desinstalar phpBB Gallery. ',
	'SUPPORT_BODY' 					=>	'Se proporcionará soporte completo para la versión estable actual de phpBB Gallery, de forma gratuita. Esto incluye:</li><li>problemas</li><li>problemas relacionados con posibles errores en el software</li><ul><li>instalación</li><li> configuración</li><li>actualizando desde las versiones Release Candidate (RC) a la última versión estable</li><li>convirtiendo desde el MOD de álbum de Smartor para phpBB 2.0.x a phpBB Gallery para phpBB3</li><li>convirtiendo desde TS Gallery A la galería phpBB</li></ul><p>Se recomienda limitar el uso de versiones Beta. Si hay actualizaciones, se recomienda actualizar rápidamente.</p><p>Se ofrece soporte en las siguientes placas</p><ul><li><a href="http://www.flying-bits. Org / "> flying-bits.org - Junta de MOD-Autor nickvergessen</a></li><li><a href="http://www.phpbb.de/"> phpbb.de</a></Li></li><li><a href="http://www.phpbb.com/"> phpbb.com</a></li></ul><p>',

	'TABLE_ALBUM' 					=>	'tabla que incluye las imágenes',
	'TABLE_ALBUM_CAT' 				=>	'tabla incluyendo los álbumes',
	'TABLE_ALBUM_COMMENT' 			=>	'tabla incluyendo los comentarios',
	'TABLE_ALBUM_CONFIG' 			=>	'tabla incluyendo la configuración',
	'TABLE_ALBUM_RATE' 				=>	'tabla incluyendo las tarifas',
	'TABLE_EXISTS' 					=>	'existe',
	'TABLE_MISSING' 				=>	'faltantes',
	'TABLE_PREFIX_EXPLAIN' 			=>	'Prefijo de phpBB2-instalación',

	'UNINSTALL_INTRO' 				=>	'Bienvenido a Desinstalar',
	'UNINSTALL_INTRO_BODY' 			=>	'Con esta opción, es posible desinstalar phpBB Gallery de tu foro. <br/><br /><strong> ADVERTENCIA: ¡Todos los álbumes, imágenes y comentarios serán eliminados e irrecuperables!</strong> ,',
	'UNINSTALL_REQUIREMENTS' 		=>	'Requerimiento',
	'UNINSTALL_REQUIREMENTS_EXPLAIN' =>	'Antes de proceder con la desinstalación completa phpBB realizará algunas pruebas para asegurarse de que se le permite desinstalar phpBB Gallery.',
	'UNINSTALL_START' 				=>	'Desinstalar',
	'UNINSTALL_FINISHED' 			=>	'Desinstalar casi terminado',
	'UNINSTALL_FINISHED_EXPLAIN' 	=>	'Desinstalaste la galería phpBB con éxito. <br/><br/><strong>Ahora solo necesitas deshacer los pasos de install.xml y borrar los archivos de la galería. Después su tablero está completamente libre de la galería.</strong> ',

	'UPDATE_INSTALLATION_EXPLAIN' 	=>	'Aquí puedes actualizar tu versión de la galería de phpBB.',

	'VERSION_NOT_SUPPORTED' 		=>	'Lo sentimos, pero sus actualizaciones anteriores a 1.0.6 no son compatibles con este sistema de instalación / actualización.',

	'GALLERY_SUB_EXT_UNINSTALL' => array(
		1 => 'Debe desinstalar la extensión: <br /><strong>%s</strong><br /> antes de desinstalar la extensión principal.',
		2 => 'Debe desinstalar las extensiones: <br /><strong>%s</strong><br /> antes de desinstalar la extensión principal.',
	),
));
