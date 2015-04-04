<?php
/**
*
* gallery [Bulgarian]
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2015 Lucifer lucifer@anavaro.com http://www.anavaro.com
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
	'ADD_UPLOAD_FIELD'				=> 'Добавете още файлове за качване',
	'ALBUM'							=> 'Албум',
	'ALBUM_IS_CATEGORY'				=> 'Албума до който излъгахте е Албум категория.<br />Не можете да качвате в категория.',
	'ALBUM_LOCKED'					=> 'Заключен',
	'ALBUM_NAME'					=> 'Име на албум',
	'ALBUM_NOT_EXIST'				=> 'Албума не съществува',
	'ALBUM_PERMISSIONS'				=> 'Права на албума',
	'ALBUM_REACHED_QUOTA'			=> 'Албума е достигнал квотата от изображения. Вече не мжоете да качвате.<br />Моле свържете се с Администратор за повече информация.',
	'ALBUM_UPLOAD_NEED_APPROVAL'		=> 'Изображенията ви са качени успешно.<br /><br />Но изображенията трябва да се одобрят от екипа, преди да станат публично видими.',
	'ALBUM_UPLOAD_NEED_APPROVAL_ERROR'	=> 'Някои от изображенията ви са качени успешно.<br /><br />Но изображенията трябва да се одобрят от екипа, преди да станат публично видими.<br /><br /><p class="error">%s</p>',
	'ALBUM_UPLOAD_SUCCESSFUL'		=> 'Изображенията ви са качени успешно.',
	'ALBUM_UPLOAD_SUCCESSFUL_ERROR'	=> 'Някои от изображенията ви са качени успешно.<br /><br /><span class="error">%s</span>',
	'ALBUMS_MARKED'					=> 'Всички албуми са маркирани като прочетени.',
	'ALL'							=> 'Всички',
	'ALL_IMAGES'					=> 'Всички изображения',
	'ALLOW_COMMENTS'				=> 'Позволи коментарите за това изображение.',
	'ALLOW_COMMENTS_ARY'			=> array(
		0	=> 'Позволи коментарите за това изображение.',
		2	=> 'Позволи коментарите за тези изображения.',
	),
	'ALLOWED_FILETYPES'				=> 'Позволени типове',
	'APPROVE'						=> 'Одобри',
	'DISAPPROVE'					=> 'Отхвърли',
	'APPROVE_IMAGE'					=> 'Одобри изображение',

	//@todo
	'ALBUM_COMMENT_CAN'			=> '<strong>Можете</strong> да поствате коментари на изображенията в този албум',
	'ALBUM_COMMENT_CANNOT'		=> '<strong>Не можете</strong> да поствате коментари на изображенията в този албум',
	'ALBUM_DELETE_CAN'			=> '<strong>Можете</strong> да триете собствените си изображения от този албум',
	'ALBUM_DELETE_CANNOT'		=> '<strong>Не можете</strong> да триете собствените си изображения от този албум',
	'ALBUM_EDIT_CAN'			=> '<strong>Можете</strong> да променяте собствените си изборажения в този албум',
	'ALBUM_EDIT_CANNOT'			=> '<strong>Не можете</strong> да променяте собствените си изборажения в този албум',
	'ALBUM_RATE_CAN'			=> '<strong>Можете</strong> да оценявате изображения в този албум',
	'ALBUM_RATE_CANNOT'			=> '<strong>Не можете</strong> да оценявате изображения в този албум',
	'ALBUM_UPLOAD_CAN'			=> '<strong>Можете</strong> да качвате нови изборажения в този албум',
	'ALBUM_UPLOAD_CANNOT'		=> '<strong>Не можете</strong> да качвате нови изборажения в този албум',
	'ALBUM_VIEW_CAN'			=> '<strong>Можете</strong> да виждате изображенията в този албум',
	'ALBUM_VIEW_CANNOT'			=> '<strong>Не можете</strong> да виждате изображенията в този албум',

	'BAD_UPLOAD_FILE_SIZE'			=> 'Качения от вас фаил е твърде голям',
	'BBCODES'						=> 'BBCodes',
	'BROWSING_ALBUM'				=> 'Потребители разглеждащи този албум: %1$s',
	'BROWSING_ALBUM_GUEST'			=> 'Потребители разглеждащи този албум: %1$s и %2$d гост',
	'BROWSING_ALBUM_GUESTS'			=> 'Потребители разглеждащи този албум: %1$s и %2$d гости',

	'CHANGE_AUTHOR'					=> 'Смени автора',
	'CHANGE_IMAGE_STATUS'			=> 'Смени състоянието на изображението',
	'CHARACTERS'					=> 'Знаци',
	'CLICK_RETURN_ALBUM'			=> 'Натисни %sтук%s за да се върнеш в албума',
	'CLICK_RETURN_IMAGE'			=> 'Натисни %sтук%s за да се върнеш на изображението',
	'CLICK_RETURN_INDEX'			=> 'Натисни %sтук%s за да отидеш в началото',
	'COMMENT'						=> 'Коментар',
	'COMMENT_IMAGE'					=> 'Публикува коментар на изображение в албум %s',
	'COMMENT_LENGTH'				=> 'Въведете вашия коментар тук, може да садържа не повече от <strong>%d</strong> символа.',
	'COMMENT_ON'					=> 'Коментира върху',
	'COMMENT_STORED'				=> 'Вашият коментар беше записан успешно.',
	'COMMENT_TOO_LONG'				=> 'Вашият коментар беше твърде дълъг.',
	'COMMENTS'						=> 'Коментари',
	//'CONTEST_COMMENTS_STARTS'		=> 'Comments on images in this contest are allowed from %s on.',
	//'CONTEST_ENDED'					=> 'This contest ended on %s.',
	//'CONTEST_ENDS'					=> 'This contest ends on %s.',
	//'CONTEST_RATING_STARTED'		=> 'The rating for this contest started on %s.',
	//'CONTEST_RATING_STARTS'			=> 'The rating for this contest starts on %s.',
	//'CONTEST_RATING_ENDED'			=> 'The rating for this contest ended on %s.',
	//'CONTEST_RATING_HIDDEN'			=> 'hidden',
	//'CONTEST_RESULT'				=> 'Contest',
	//'CONTEST_RESULT_1'				=> 'Winner',
	//'CONTEST_RESULT_2'				=> 'Second',
	//'CONTEST_RESULT_3'				=> 'Third',
//	'CONTEST_RESULT_HIDDEN'			=> 'The rating for this images is hidden, until the end of the contest on %s.',
	//'CONTEST_STARTED'				=> 'The contest started on %s.',
	//'CONTEST_STARTS'				=> 'The contest starts on %s.',
	//'CONTEST_USERNAME'				=> '<strong>Contest</strong>',
	//'CONTEST_USERNAME_LONG'			=> '<strong>Contest</strong> » The username is hidden, until the end of the contest on %s.',
	//'CONTEST_IMAGE_DESC'			=> '<strong>Contest</strong> » The image-description is hidden, until the end of the contest on %s.',
	//'CONTEST_WINNERS_OF'			=> 'Contest winner of “%s“',
	'CONTINUE'						=> 'Продължи',

	'DATABASE_NOT_UPTODATE'			=> 'Базта данни не е същата версия като файловте ви. Моля, обновете базата си.',
	'DELETE_COMMENT'				=> 'Изтрий коментар',
	'DELETE_COMMENT2'				=> 'Изтрий коментар?',
	'DELETE_COMMENT2_CONFIRM'		=> 'Сигурни ли сте, че желаете да изтриете коментара?',
	'DELETE_IMAGE'					=> 'Изтрий',
	'DELETE_IMAGE2'					=> 'Изтрий изображение?',
	'DELETE_IMAGE2_CONFIRM'			=> 'Сигурни ли сте, че желаете да изтриете изображението?',
	'DELETED_COMMENT'				=> 'Коментара изтрит',
	'DELETED_COMMENT_NOT'			=> 'Коментара не е изтрит',
	'DELETED_IMAGE'					=> 'Изображението изтрито',
	'DELETED_IMAGE_NOT'				=> 'Изображението не е изтрито',
	'DESC_TOO_LONG'					=> 'Your description is too long',
	'DESCRIPTION_LENGTH'			=> 'Enter your descriptions here, it may contain no more than <strong>%d</strong> characters.',
	'DETAILS'						=> 'Details',
	'DONT_RATE_IMAGE'				=> 'Don’t rate image',

	'EDIT_COMMENT'					=> 'Edit comment',
	'EDIT_IMAGE'					=> 'Edit',
	'EDITED_TIME_TOTAL'				=> 'Last edited by %s on %s; edited %d time in total',
	'EDITED_TIMES_TOTAL'			=> 'Last edited by %s on %s; edited %d times in total',

	'FILE'							=> 'File',
	'FILE_SIZE'						=> 'File size',
	'FILETYPE_MIMETYPE_MISMATCH'	=> 'The file-type of “<strong>%1$s</strong>“ does not match the mime-type (%2$s).',
	'FILETYPES_GIF'					=> 'gif',
	'FILETYPES_JPG'					=> 'jpg',
	'FILETYPES_PNG'					=> 'png',
	'FILETYPES_ZIP'					=> 'zip',

	'GALLERY_IMAGE'					=> 'Изображение',
	'GALLERY_IMAGES'					=> 'Изображения',
	'GALLERY_VIEWS'					=> 'Прегледи',

	'IGNORE_NOTUPTODATE_MESSAGE'		=> 'Напомни ми след 7 дни',
	'IMAGE_ALREADY_REPORTED'			=> 'Това изображение вече е докладвано.',
	'IMAGE_BBCODE'						=> 'Image BBCode',
	'IMAGE_COMMENTS_DISABLED'			=> 'Коментарите са изключени за това изборажение.',
	'IMAGE_DAY'							=> '%.2f изображения на ден',
	'IMAGE_DESC'						=> 'Описание на изображението',
	'IMAGE_HEIGHT'						=> 'Височина на изображението',
	'IMAGE_INSERTED'					=> 'Изображението добавено',
	'IMAGE_LOCKED'						=> 'Съжаляваме, но това изображение е заключено. Вече не можете да го коментирате.',
	'IMAGE_NAME'						=> 'Име на изображение',
	'IMAGE_NOT_EXIST'					=> 'Това изображение не съществува.',
	'IMAGE_PCT'							=> '%.2f%% от всички изображения',
	'IMAGE_STATUS'						=> 'Статус',
	'IMAGE_URL'							=> 'Image-URL',
	'IMAGE_VIEWS'							=> 'Прегледи',
	'IMAGE_WIDTH'						=> 'Ширина на изображението',
	'IMAGES_REPORTED_SUCCESSFULLY'		=> 'Изображението беше успешно докладвано',
	'IMAGES_UPDATED_SUCCESSFULLY'		=> 'Информацията за това изображение беше успешно обновена',
	'INSERT_IMAGE_POST'					=> 'Вкарай изображение в пост',
	'INVALID_USERNAME'					=> 'Потребителското ви име е невалидно',

	'LAST_COMMENT'					=> 'Последен коментар',
	'LAST_IMAGE'					=> 'Последно изображение',
	'LAST_IMAGE_BY'					=> 'Последно изображение от',
	'LOGIN_EXPLAIN_UPLOAD'			=> 'Трябва да сте регистриран и да сте влезли в профила си за да качвате изборажения в тази галерия.',

	'MARK_ALBUMS_READ'				=> 'Отбележи албумите като прочетени',
	'MAX_DIMENSIONS'				=> 'Максимална височина и ширина',
	'MAX_FILE_SIZE'					=> 'Максимална галемина на изображението',
	'MAX_HEIGHT'					=> 'Максимална височина на изображението',
	'MAX_WIDTH'						=> 'Максимална ширина на изображението',
	'MISSING_COMMENT'				=> 'Не е въвденео съобщение',
	'MISSING_IMAGE_NAME'			=> 'Трябва да окажете име когато редактирате изображение.',
	'MISSING_MODE'					=> 'Не е избрано състояние',
	'MISSING_REPORT_REASON'			=> 'Трябва да имате причина за да докладвате изображението.',
	'MISSING_SLIDESHOW_PLUGIN'		=> 'Не е намерен plugin за slideshow. Свържете се с администратора.',
	'MISSING_SUBMODE'				=> 'Не е избрано под-състояние',
	'MISSING_USERNAME'				=> 'Не е въведено потрбителско име',
	'MOVE_TO_ALBUM'					=> 'Премести в албум',
	'MOVE_TO_PERSONAL'				=> 'Премести в личен албум',
	'MOVE_TO_PERSONAL_MOD'			=> 'Когато изберете тази опция, изображението се мести в личния албум на потребителя. Ако потрбителя все още няма такъв, то той се създава автоматично.',
	'MOVE_TO_PERSONAL_EXPLAIN'		=> 'Когато изберете тази опция, изборажението се место във вашия личен албум. Ако нямате все още такъв, то той се създава автоматично.',

	'NEW_COMMENT'					=> 'Нов коментар',
	'NEW_IMAGES'					=> 'Нови изображения',
	'NEWEST_PGALLERY'				=> 'Последната ни лична галерия е %s',
	'NO_ALBUMS'						=> 'В тази галерия няма албуми.',
	'NO_COMMENTS'					=> 'Все още няма коментари',
	'NO_IMAGES'						=> 'Няма изображения',
	'NO_IMAGES_FOUND'				=> 'Не са намерени изображения.',
	'NO_NEW_IMAGES'					=> 'Няма нови изображения',
	'NO_IMAGES_LONG'				=> 'В този албум няма изображения.',
	'NOT_ALLOWED_FILE_TYPE'			=> 'Този тип файлове не е позволен',
	'NOT_RATED'						=> 'не е оценено',

	'ORDER'							=> 'Ред',
	'ORIG_FILENAME'					=> 'Приеми името на файла за име на изображението (полето за въвеждане няма никаква роля)',
	'OUT_OF_RANGE_VALUE'			=> 'Стойността е извън зададените граници',
	'OWN_IMAGES'					=> 'Вашите изображения',

	'PERCENT'						=> '%',
	'PERSONAL_ALBUMS'				=> 'Лични албуми',
	'PIXELS'						=> 'пиксела',
	'PLUGIN_CLASS_MISSING'			=> 'Gallery Plugin Error: Class “%s“ could not be found!',
	'POST_COMMENT'					=> 'Публикувай коментар',
	'POST_COMMENT_RATE_IMAGE'		=> 'Публикувай коментар и оцени изображението',
	'POSTER'						=> 'Автор',

	'QUOTA_REACHED'					=> 'Достигнахте броя изображения, които ви е позволено да качвате.',
	'QUOTE_COMMENT'					=> 'Цитирай коментара',

	'RANDOM_IMAGES'					=> 'Случайни изображения',
	'RATE_IMAGE'					=> 'Оцени изображението',
	'RATES_COUNT'					=> 'Брой оценки',
	'RATING'						=> 'Оценка',
	'RATING_STRINGS'				=> array(
		0	=> 'няма оценки',
		1	=> '%2$s (1 оценка)',
		2	=> '%2$s (%1$s оценки)',
	),
	'RATING_STRINGS_USER'			=> array(
		1	=> '%2$s (1 оценка, вашата оценка: %3$s)',
		2	=> '%2$s (%1$s оценки, вашата оценка: %3$s)',
	),
	'RATING_SUCCESSFUL'				=> 'Изображението беше успешно оценено.',
	'READ_REPORT'					=> 'Прегледай доклада',
	'RECENT_COMMENTS'				=> 'Скорошни коментари',
	'RECENT_IMAGES'					=> 'Скорошни изображения',
	'REPORT_IMAGE'					=> 'Докладвай изображение',
	'RETURN_ALBUM'					=> '%sВърни се в последно поетения албум%s',
	'ROTATE_IMAGE'					=> 'Завърти изображението',
	'ROTATE_LEFT'					=> '90° ляво',
	'ROTATE_NONE'					=> 'без',
	'ROTATE_RIGHT'					=> '90° дясно',
	'ROTATE_UPSIDEDOWN'				=> '180° на обратно',
	'RETURN_TO_GALLERY'				=> 'Обратно към Галерията',

	'SEARCH_ALBUM'					=> 'Търси в този албум ...',
	'SEARCH_ALBUMS'					=> 'Търси в албуми',
	'SEARCH_ALBUMS_EXPLAIN'			=> 'Изберете албума или подалбума в които искате да търсите. Подалбумите се проверяват автоматично, ако не изключите опцията "търси в подалбуми" по-долу.',
	'SEARCH_COMMENTS'				=> 'Само коментари',
	'SEARCH_CONTEST'				=> 'Победители в конкурси',
	'SEARCH_IMAGE_COMMENTS'			=> 'Имена, описания и коментари',
	'SEARCH_IMAGE_VALUES'			=> 'Само в имена и описания',
	'SEARCH_IMAGENAME'				=> 'Imagenames only',
	'SEARCH_RANDOM'					=> 'Случайни изображения',
	'NO_SEARCH_RESULTS_RANDOM'		=> 'Няма качени изображения или нямате права да ги видите!',
	'SEARCH_RECENT'					=> 'Скорошни изображения',
	'NO_SEARCH_RESULTS_RECENT'		=> 'Няма качени изображения или нямате права да ги видите!',
	'SEARCH_RECENT_COMMENTS'		=> 'Скорошни коментари',
	'SEARCH_SUBALBUMS'				=> 'Търси в подалбуми',
	'SEARCH_TOPRATED'				=> 'Най-оценявани',
	'SEARCH_USER_IMAGES'			=> 'Търси изображения на потребител',
	'SEARCH_USER_IMAGES_OF'			=> 'Изображения на %s',
	'SELECT_ALBUM'					=> 'Избери албум',
	'SHOW_PERSONAL_ALBUM_OF'		=> 'Покажи лични албум на %s',
	'SLIDE_SHOW'					=> 'Slideshow',
	'SLIDE_SHOW_HIGHSLIDE'			=> 'За да започнете slideshow, натиснете едно от имената на изображенията и натиснете "play" иконката:',
	'SLIDE_SHOW_LYTEBOX'			=> 'За да започнете slideshow, натиснете едно от имената на изображенията:',
	'SLIDE_SHOW_SHADOWBOX'			=> 'За да започнете slideshow, натиснете едно от имената на изображенията:',
	'SORT_ASCENDING'				=> 'Нарастващ',
	'SORT_DEFAULT'					=> 'По подразиране',
	'SORT_DESCENDING'				=> 'Намаляващ',
	'STATUS'						=> 'Състояние',
	'SUBALBUMS'						=> 'Подалбуми',
	'SUBALBUM'						=> 'Подалбум',

	'THUMBNAIL_SIZE'				=> 'Thumbnail size (pixels)',
	'TOTAL_COMMENTS_SPRINTF'		=> array(
		0	=> '<strong>Няма</strong> коментари',
		1	=> 'Общо <strong>%d</strong> коментар',
		2	=> 'Общо <strong>%d</strong> коментара',
	),
	'TOTAL_IMAGES'					=> 'Общо изображения',
	'TOTAL_IMAGES_SPRINTF'			=> array(
		0	=> 'Няма изображения',
		1	=> '%d изображение',
		2	=> '%d изображения',
	),
	'TOTAL_PEGAS_SHORT_SPRINTF'		=> array(
		0	=> '0 лични галерии',
		1	=> '%d лична галерия',
		2	=> '%d лични галерии',
	),
	'TOTAL_PEGAS_SPRINTF'		=> array(
		0	=> '<strong>Няма</strong> лични галерии',
		1	=> 'Общо <strong>%d</strong> лична галерия',
		2	=> 'Общо <strong>%d</strong> лични галерии',
	),

	'UNLOCK_IMAGE'					=> 'Отключи изображение',
	'UNWATCH_ALBUM'					=> 'Махни абонамента за албум',
	'UNWATCH_IMAGE'					=> 'Махни абонамента за изображения',
	'UNWATCH_PEGAS'					=> 'Не се абонирай за нови лични галерии',
	'UNWATCHED_ALBUM'				=> 'Вече няма да ви информират за нови изображения в този албум.',
	'UNWATCHED_ALBUMS'				=> 'Вече няма да ви информират за нови изображения в тези албуми.',
	'UNWATCHED_IMAGE'				=> 'Вече няма да ви информират за нови коментари към това изображение.',
	'UNWATCHED_IMAGES'				=> 'Вече няма да ви информират за нови коментари към тези изображения.',
	'UNWATCHED_PEGAS'				=> 'Вече не сте автоматично абониранн за нови лични галерии.',
	'UPLOAD_ERROR'					=> 'Докато качвах “%1$s“ възникна следната грешка:<br />&raquo; %2$s',
	'UPLOAD_IMAGE'					=> 'Качи Изображение',
	'UPLOAD_IMAGE_SIZE_TOO_BIG'		=> 'Размерите на вашето изображения са прекалено големи.',
	'UPLOAD_NO_FILE'				=> 'Трябва да въведете пътя и името на фаила.',
	'UPLOADED_BY_USER'				=> 'Качена от',
	'UPLOADED_ON_DATE'				=> 'Качена на',
	'USE_SAME_NAME'					=> 'Използвай същото име и описание за всички изображения.',
	'USE_NUM'						=> 'Добавете {NUM} за номера. Започни броенето от:',
	'USER_REACHED_QUOTA'			=> array(
		0	=> 'Не ви е позволено да качвате <strong>никакви</strong> изображения.<br />Моля свържете се с администратор за повече информация.',
		1	=> 'Не ви е позволено да качвате повече от <strong>1</strong> изображения.<br />Моля свържете се с администратор за повече информация.',
		2	=> 'Не ви е позволено да качвате повече от <strong>%s</strong> изображения.<br />Моля свържете се с администратор за повече информация.',
	),
	'USER_REACHED_QUOTA_SHORT'		=> array(
		0	=> 'Не ви е позволено да качвате <strong>никакви</strong> изображения.',
		1	=> 'Не ви е позволено да качвате повече от <strong>1</strong> изображения.',
		2	=> 'Не ви е позволено да качвате повече от <strong>%s</strong> изображения.',
	),
	'USERNAME_BEGINS_WITH'			=> 'Потребителското име започва с ',
	'USERS_PERSONAL_ALBUMS'			=> 'Лични потребителски албуми',

	'VIEW_ALBUM'					=> 'Виж албум',
	'VIEW_ALBUM_IMAGES'				=> array(
		1	=> '1 изображения',
		2	=> '%s изображения',
	),
	'VIEW_IMAGE'					=> 'Виж изображение',
	'VIEW_IMAGE_COMMENTS'			=> array(
		1	=> '1 коментар',
		2	=> '%s коментара',
	),
	'VIEW_LATEST_IMAGE'				=> 'Виж най-новото изображение',
	'VIEW_SEARCH_RECENT'			=> 'Виж скорошни изображения',
	'VIEW_SEARCH_RANDOM'			=> 'Виж случайни изображения',
	'VIEW_SEARCH_COMMENTED'			=> 'Виж последни коментари',
	'VIEW_SEARCH_CONTESTS'			=> 'Виж изображеняита спечелили конкурси',
	'VIEW_SEARCH_TOPRATED'			=> 'Виж най-оценените изображия',
	'VIEW_SEARCH_SELF'				=> 'Виж своите изображения',
	'VIEWING_ALBUM'					=> 'Разглежда албум %s',
	'VIEWING_IMAGE'					=> 'Разглежда изображения в албум %s',

	'VISIT_GALLERY'					=> 'Посети потребителската галерия',

	'WATCH_ALBUM'					=> 'Абонирай се за албум',
	'WATCH_IMAGE'					=> 'Абонирай се за изображение',
	'WATCH_PEGAS'					=> 'Абонирай се за нови лични галерии',
	'WATCHING_ALBUM'				=> 'Вече ще сте информиран за нови изображения в този албум.',
	'WATCHING_IMAGE'				=> 'Вече ще сте информиран за нови коментари по това изображение.',
	'WATCHING_PEGAS'				=> 'Автоматично сте записан за нови лични галерии.',

	'YOUR_COMMENT'					=> 'Вашият коментар',
	'YOUR_PERSONAL_ALBUM'			=> 'Вашият личен албум',
	'YOUR_RATING'					=> 'Вашата оценка',

	'IMAGES_MOVED'					=> array(
		1	=>	'Изожението е преместено',
		2	=> 	'%s изображения са преместени',
	),

	'QUICK_MOD'	=> 'Изберете модераторско действие',
));
