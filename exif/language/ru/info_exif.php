<?php
/**
 * phpBB Gallery - ACP Exif Extension [Russian Translation]
 *
 * @package   phpbbgallery/exif
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 * @translator Eduard Schlak <https://translations.schlak.info/>
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
	'EXIF_DATA'					=> 'EXIF-Данные',
	'EXIF_APERTURE'				=> 'Диафрагма',
	'EXIF_CAM_MODEL'			=> 'Модель камеры',
	'EXIF_DATE'					=> 'Изображение заснято',
	'EXIF_EXPOSURE'				=> 'Выдержка',
	'EXIF_EXPOSURE_EXP'			=> '%s сек',// 'EXIF_EXPOSURE' unit
	'EXIF_EXPOSURE_BIAS'		=> 'Корректировка выдержки',
	'EXIF_EXPOSURE_BIAS_EXP'	=> '%s LW',// 'EXIF_EXPOSURE_BIAS' unit
	'EXIF_EXPOSURE_PROG'		=> 'Программа экспозиции',
	'EXIF_EXPOSURE_PROG_0'		=> 'Неопределенно',
	'EXIF_EXPOSURE_PROG_1'		=> 'Вручную',
	'EXIF_EXPOSURE_PROG_2'		=> 'Нормальная программа',
	'EXIF_EXPOSURE_PROG_3'		=> 'Приоритет диафрагмы',
	'EXIF_EXPOSURE_PROG_4'		=> 'Приоритет выдержки',
	'EXIF_EXPOSURE_PROG_5'		=> 'Художественная программа (настроено на глубину резкости)',
	'EXIF_EXPOSURE_PROG_6'		=> 'Программа действий (настроено на быструю скорость затвора)',
	'EXIF_EXPOSURE_PROG_7'		=> 'Портретный режим (для снимков на близком расстоянии с размытым фоном)',
	'EXIF_EXPOSURE_PROG_8'		=> 'Ландшафтный режим (для пейзажных снимков, с резким фоне)',
	'EXIF_FLASH'				=> 'Вспышка',
	'EXIF_FLASH_CASE_0'			=> 'Вспышка не сработала',
	'EXIF_FLASH_CASE_1'			=> 'Вспышка сработала',
	'EXIF_FLASH_CASE_5'			=> 'Нет отражателя вспышки',
	'EXIF_FLASH_CASE_7'			=> 'Отражатель вспышки сработал',
	'EXIF_FLASH_CASE_8'			=> 'Включено, вспышка не сработала',
	'EXIF_FLASH_CASE_9'			=> 'Вспышка сработала, вспышка в вынужденном режиме',
	'EXIF_FLASH_CASE_13'		=> 'Вспышка сработала, вспышка в вынужденном режиме, нет отражателя вспышки',
	'EXIF_FLASH_CASE_15'		=> 'Вспышка сработала, вспышка в вынужденном режиме, отражатель вспышки сработал',
	'EXIF_FLASH_CASE_16'		=> 'Вспышка не сработала, вспышка в скрытом режиме',
	'EXIF_FLASH_CASE_20'		=> 'Отключено вспышка не сработала, нет отражателя вспышки',
	'EXIF_FLASH_CASE_24'		=> 'Вспышка не сработала, авторежим',
	'EXIF_FLASH_CASE_25'		=> 'Вспышка сработала, авторежим',
	'EXIF_FLASH_CASE_29'		=> 'Вспышка сработала, авторежим, нет отражателя вспышки',
	'EXIF_FLASH_CASE_31'		=> 'Вспышка сработала, авторежим, отражатель вспышки сработал',
	'EXIF_FLASH_CASE_32'		=> 'Нет вспышки',
	'EXIF_FLASH_CASE_48'		=> 'Отключено, нет вспышки',
	'EXIF_FLASH_CASE_65'		=> 'Вспышка сработала, подавление эффекта красных глаз',
	'EXIF_FLASH_CASE_69'		=> 'Вспышка сработала, подавление эффекта красных глаз, нет отражателя вспышки',
	'EXIF_FLASH_CASE_71'		=> 'Вспышка сработала, подавление эффекта красных глаз, отражатель вспышки сработал',
	'EXIF_FLASH_CASE_73'		=> 'Вспышка сработала, вспышка в вынужденном режиме, подавление эффекта красных глаз',
	'EXIF_FLASH_CASE_77'		=> 'Вспышка сработала, вспышка в вынужденном режиме, подавление эффекта красных глаз, нет отражателя вспышки',
	'EXIF_FLASH_CASE_79'		=> 'Вспышка сработала, вспышка в вынужденном режиме, подавление эффекта красных глаз, отражатель вспышки сработал',
	'EXIF_FLASH_CASE_80'		=> 'Отключено, подавление эффекта красных глаз',
	'EXIF_FLASH_CASE_88'		=> 'Вспышка не сработала, подавление эффекта красных глаз',
	'EXIF_FLASH_CASE_89'		=> 'Вспышка сработала, авторежим, подавление эффекта красных глаз',
	'EXIF_FLASH_CASE_93'		=> 'Вспышка сработала, авторежим, нет отражателя вспышки, подавление эффекта красных глаз',
	'EXIF_FLASH_CASE_95'		=> 'Вспышка сработала, авторежим, отражатель вспышки сработал, подавление эффекта красных глаз',
	'EXIF_FOCAL'				=> 'Фокусное расстояние',
	'EXIF_FOCAL_EXP'			=> '%s mm',// 'EXIF_FOCAL' unit
	'EXIF_ISO'					=> 'ISO-чувствительность',
	'EXIF_METERING_MODE'		=> 'Метод выдержки и измерения',
	'EXIF_METERING_MODE_0'		=> 'Неизвестно',
	'EXIF_METERING_MODE_1'		=> 'Среднее',
	'EXIF_METERING_MODE_2'		=> 'Центрально-взвешенный',
	'EXIF_METERING_MODE_3'		=> 'Пятно',
	'EXIF_METERING_MODE_4'		=> 'Multi-пятно',
	'EXIF_METERING_MODE_5'		=> 'Многосегментный',
	'EXIF_METERING_MODE_6'		=> 'Поле',
	'EXIF_METERING_MODE_255'	=> 'Другое',
	'EXIF_NOT_AVAILABLE'		=> 'Недоступно',
	'EXIF_WHITEB'				=> 'Баланс белого света',
	'EXIF_WHITEB_AUTO'			=> 'Автоматически',
	'EXIF_WHITEB_MANU'			=> 'Вручную',

	'DISP_EXIF_DATA'			=> 'Просмотр EXIF-Данных',
	'DISP_EXIF_DATA_EXP'		=> 'Эта функция не может использоваться на данный момент, т.к. функция "exif_read_data" не входит в установке PHP',
	'SHOW_EXIF'					=> 'Показать / Скрыть',
	'VIEWEXIFS_DEFAULT'			=> 'Просмотр EXIF-Данных по умолчанию',

	'GALLERY_CORE_NOT_FOUND'		=> 'Сначала необходимо установить и включить расширение phpBB Gallery Core.',
	'EXTENSION_ENABLE_SUCCESS'		=> 'Расширение успешно включено.',
]);
