<?php
/**
*
* install_gallery [Russian]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'BBCODES_NEEDS_REPARSE'		=> 'BBCode нуждается в переобработке.',
	'CAT_CONVERT'				=> 'Конвертация phpBB2',
	'CAT_CONVERT_TS'			=> 'Конвертация TS Gallery',
	'CAT_UNINSTALL'				=> 'Удаление phpBB Gallery',
	'CHECK_TABLES'				=> 'Проверка таблиц',
	'CHECK_TABLES_EXPLAIN'		=> 'Следующие таблицы должны существовать, чтобы была возможность конвертирования.',
	'CONVERT_SMARTOR_INTRO'			=> 'Конвертер из «Smartor’s Album MOD» в «phpBB Gallery»',
	'CONVERT_SMARTOR_INTRO_BODY'	=> 'Можно ковертировать альбомы, фотографии, оценки и комментарии из <a href="http://www.phpbb.com/community/viewtopic.php?f=16&t=74772">Smartor’s Album MOD</a> (тестировано на версии 2.0.56) и <a href="http://www.phpbbhacks.com/download/5028">Full Album Pack</a> (тестировано на версии 1.4.1) в phpBB Gallery.<br /><br /><strong>Примечание:</strong> <strong>права доступа</strong> <strong>не будут скопированы</strong>.',
	'CONVERT_TS_INTRO'				=> 'Конвертер из «TS Gallery» в «phpBB Gallery»',
	'CONVERT_TS_INTRO_BODY'			=> 'Можно ковертировать альбомы, фотографии, оценки и комментарии из <a href="http://www.phpbb.com/community/viewtopic.php?f=70&t=610509">TS Gallery</a> (тестировано на версии 0.2.1) в phpBB Gallery.<br /><br /><strong>Примечание:</strong> <strong>права доступа</strong> <strong>не будут скопированы</strong>.',
	'CONVERT_COMPLETE_EXPLAIN'		=> 'Конвертация из вашей галереи в phpBB Gallery v%s прошла успешно.<br />Удостоверьтесь, что все параметры перенеслись правильно. Не забудьте удалить папку <em>install</em>.<br /><br /><strong>Не забудьте также, что права доступа не копировались и вам придётся задать их заново.</strong><br /><br />Очистить базу данных от пустых записей, для которых фотографии отсутствуют, можно в администраторском разделе: Модули → Галерея → Очистка галереи.',
	'CONVERTED_ALBUMS'			=> 'Альбомы скопированы.',
	'CONVERTED_COMMENTS'		=> 'Комментарии скопированы.',
	'CONVERTED_IMAGES'			=> 'Фотографии скопированы.',
	'CONVERTED_MISC'			=> 'Прочее скопировано.',
	'CONVERTED_PERSONALS'		=> 'Личные альбомы скопированы.',
	'CONVERTED_RATES'			=> 'Оценки скопированы.',
	'CONVERTED_RESYNC_ALBUMS'	=> 'Пересчёт статистики.',
	'CONVERTED_RESYNC_COMMENTS'	=> 'Пересчёт комментариев.',
	'CONVERTED_RESYNC_COUNTS'	=> 'Пересчёт счетчиков.',
	'CONVERTED_RESYNC_RATES'	=> 'Пересчёт рейтингов.',
	'FILE_DELETE_FAIL'				=> 'Файл не может быть удалён автоматически, вам надо это сделать вручную.',
	'FILE_STILL_EXISTS'				=> 'Файл всё ещё существует.',
	'FILES_REQUIRED_EXPLAIN'		=> 'Для корректного функционирования галерее нужен доступ на запись к некоторым файлам и папкам. Если вы видите надпись «Недоступно», то должны изменить права доступа для файла или папки так, чтоб phpBB мог записывать в них.',
	'FILES_DELETE_OUTDATED'			=> 'Удалить устаревшие файлы',
	'FILES_DELETE_OUTDATED_EXPLAIN'	=> 'Действие необратимо, файлы удаляются полностью и не могут быть восстановлены.<br /><br />Примечание:<br />если у вас несколько стилей и языков, вам придётся удалить файлы вручную.',
	'FILES_OUTDATED'				=> 'Устаревшие файлы',
	'FILES_OUTDATED_EXPLAIN'		=> '<strong>Устаревшие</strong>: для предотвращения хакерского доступа удалите эти файлы.',
	'FOUND_INSTALL'					=> 'Повторная установка',
	'FOUND_INSTALL_EXPLAIN'			=> '<strong>Повторная установка</strong>: найдена установленная галерея. Если вы продолжите установку, все данные будут перезаписаны. Все альбомы, фотографии и комментарии будут удалены. <strong>Рекомендуется %1$sобновление%2$s галереи.</strong>',
	'FOUND_VERSION'					=> 'Обнаружена следующая версия',
	'FOUNDER_CHECK'					=> 'Вы являетесь основателем этой конференции.',
	'FOUNDER_NEEDED'				=> 'Вы должны иметь статус основателя этой конференции.',
	'INSTALL_CONGRATS_EXPLAIN'	=> 'Вы успешно установили phpBB Gallery версии %s.<br/><br/><strong>Удалите, переместите или переименуйте папку <em>install</em> перед использованием конференции. Пока папка не удалена, будет доступен только администраторский раздел.</strong>',
	'INSTALL_INTRO_BODY'		=> 'Установка phpBB Gallery на вашу конференцию.',
	'GOTO_GALLERY'				=> 'Перейти в phpBB Gallery',
	'GOTO_INDEX'				=> 'Перейти на главную страницу',
	'MISSING_CONSTANTS'			=> 'Перед запуском скрипта установки вам необходимо загрузить отредактированные файлы, в первую очередь <em>includes/constants.php</em>.',
	'MODULES_CREATE_PARENT'		=> 'Создать родительский модуль',
	'MODULES_PARENT_SELECT'		=> 'Сменить родительский модуль',
	'MODULES_SELECT_4ACP'		=> 'Сменить родительский модуль в администраторском разделе',
	'MODULES_SELECT_4LOG'		=> 'Сменить родительский модуль для логов',
	'MODULES_SELECT_4MCP'		=> 'Сменить родительский модуль в модераторском разделе',
	'MODULES_SELECT_4UCP'		=> 'Сменить родительский модуль в личном разделе',
	'MODULES_SELECT_NONE'		=> 'Нет родительского модуля',
	'NO_INSTALL_FOUND'			=> 'Галерея не найдена.',
	'OPTIONAL_EXIFDATA'				=> 'Функция <em>exif_read_data</em> доступна',
	'OPTIONAL_EXIFDATA_EXP'			=> 'Exif-модуль не загружен или не установлен.',
	'OPTIONAL_EXIFDATA_EXPLAIN'		=> 'Если функция доступна, exif-данные будут отображаться в галерее.',
	'OPTIONAL_IMAGEROTATE'			=> 'Функция <em>imagerotate</em> доступна',
	'OPTIONAL_IMAGEROTATE_EXP'		=> 'Вы должны обновить версию GD, текущая версия — %s.',
	'OPTIONAL_IMAGEROTATE_EXPLAIN'	=> 'Если функция доступна, вы сможете вращать фотографии при загрузке и редактировании.',
	'PAYPAL_DEV_SUPPORT'				=> '</p><div class="errorbox">
	<h3>От автора</h3>
	<p>Создание, обслуживание и обновление этого мода требует много времени и усилий. Если вам нравится мод и если у вас появится желание подчеркнуть свою благодарность пожертвованием, я буду вам очень признателен. Мой Paypal-ID: <strong>nickvergessen@gmx.de</strong>, или же свяжитесь со мной по email.<br /><br /> Рекомендуемый взнос — 25 € (но буду благодарен за любую сумму).</p><br />
	<a href="http://www.flying-bits.org/go/paypal"><input type="submit" value="Make PayPal-Donation" name="paypal" id="paypal" class="button1" /></a>
</div><p>',
	'PHP_SETTINGS'				=> 'Параметры PHP',
	'PHP_SETTINGS_EXP'			=> 'Эти параметры PHP требуются для установки и запуска галереи.',
	'PHP_SETTINGS_OPTIONAL'		=> 'Дополнительные параметры PHP',
	'PHP_SETTINGS_OPTIONAL_EXP'	=> 'Эти параметры PHP не требуются для нормального функционирования галереи, но они дадут возможность использовать дополнительные функции.',
	'REQ_GD_LIBRARY'			=> 'GD-библиотека установлена',
	'REQ_PHP_VERSION'			=> 'Версия PHP ≥ %s',
	'REQ_PHPBB_VERSION'			=> 'Версия phpBB ≥ %s',
	'REQUIREMENTS_EXPLAIN'		=> 'Перед началом установки будет произведено тестирование параметров сервера и проверка некотрых файлов, чтобы определить возможность установки и запуска галереи. Ознакомьтесь с результатами тестирования и не принимайте никаких действий, пока тестирование не завершится.',
	'STAGE_ADVANCED_EXPLAIN'		=> 'Выберите родительский модуль для модулей галереи. Обычно не требуется менять.',
	'STAGE_COPY_TABLE'				=> 'Копирование таблиц базы данных',
	'STAGE_COPY_TABLE_EXPLAIN'		=> 'Таблицы БД, содержащие данные галереи и пользователей, имеют одинаковые названия в TS Gallery и phpBB Gallery. Мы делаем копию, чтобы иметь возможность конвертировать данные.',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'Таблицы базы данных phpBB Gallery созданы и заполнены первоначальными данными. Нажмите «Следующий шаг» для завершения установки.',
	'STAGE_DELETE_TABLES'			=> 'Очистка базы данных',
	'STAGE_DELETE_TABLES_EXPLAIN'	=> 'Содержимое базы данных галереи удалено. Нажмите «Завершить» для завершения удаления галереи.',
	'SUPPORT_BODY'					=> '<p>Полная поддержка оказывается для текущей стабильной версии phpBB Gallery бесплатно по следующим вопросам:</p><ul><li>установка</li><li>настройка</li><li>технические вопросы</li><li>проблемы, связанные с ошибками в программном коде галереи</li><li>обновление с версий-кандидатов (RC) до стабильной версии</li><li>конвертирование из Smartor’s Album MOD (phpBB2) в phpBB Gallery</li><li>конвертирование из TS Gallery в phpBB Gallery</li></ul><p>Использование бета-версий рекомендуется с осторожностью. Если выходят обновления, их рекомендуется устанавливать в кратчайшие сроки.</p><p>Поддержка оказывается на следующих конференциях:</p><ul><li><a href="http://www.flying-bits.org/">flying-bits.org — сайт автора галереи</a></li><li><a href="http://www.phpbb.de/">phpbb.de</a></li><li><a href="http://www.phpbb.com/">phpbb.com</a></li></ul></p><p>Актуальный русский перевод доступен на сайте официальной российской поддержки phpBB <a href="http://www.phpbbguru.net/">www.phpbbguru.net</a>.</p>',
	'TABLE_ALBUM'				=> 'таблица, содержащая фотографии',
	'TABLE_ALBUM_CAT'			=> 'таблица, содержащая альбомы',
	'TABLE_ALBUM_COMMENT'		=> 'таблица, содержащая комментарии',
	'TABLE_ALBUM_CONFIG'		=> 'таблица, содержащая параметры',
	'TABLE_ALBUM_RATE'			=> 'таблица, содержащая оценки',
	'TABLE_EXISTS'				=> 'найдены',
	'TABLE_MISSING'				=> 'отсутствуют',
	'TABLE_PREFIX_EXPLAIN'		=> 'Префикс таблиц БД phpBB2',
	'UNINSTALL_INTRO'					=> 'Удаление галереи',
	'UNINSTALL_INTRO_BODY'				=> 'Удаление phpBB Gallery с вашей конференции.<br /><br /><strong>Будьте осторожны: все альбомы, фотографии и комментарии будут удалены без возможности восстановления.</strong>',
	'UNINSTALL_REQUIREMENTS'			=> 'Требования',
	'UNINSTALL_REQUIREMENTS_EXPLAIN'	=> 'Перед удалением галереи будут проведены некоторые тесты, чтобы убедиться, есть ли у вас право удалять галерею.',
	'UNINSTALL_START'					=> 'Удаление',
	'UNINSTALL_FINISHED'				=> 'Удаление почти закончено',
	'UNINSTALL_FINISHED_EXPLAIN'		=> 'Вы успешно удалили phpBB Gallery.<br/><br/><strong>Теперь вам осталось откатить изменения в файлах конференции, описанные в <em>install.xml</em>, и удалить файлы галереи. После этого ваша конференция будет полностью очищена от галереи.</strong>',
	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Обновление phpBB Gallery.',
	'VERSION_NOT_SUPPORTED'		=> 'Извините, но обновление галереи с версий < 1.0.6 не поддерживаются данной версией инсталятора.',
));
