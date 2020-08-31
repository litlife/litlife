<?php

return [
	'admin_group_id' => '1',
	// допустимые mime типы файлов которые доступны для загрузки. Используется для валидации при загрузке
	'allowed_mime_types' => [
		'zip' => 'application/zip',
		'fb2' => ['application/xml', 'text/xml', 'application/octet-stream'],
		'epub' => 'application/epub+zip',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'mobi' => 'application/x-mobipocket-ebook',
		'odt' => 'application/vnd.oasis.opendocument.text',
		'rtf' => 'application/rtf',
		'txt' => 'text/plain',
		'doc' => ['application/msword', 'text/rtf'],
		'mp3' => 'audio/mpeg',
		'ogg' => 'audio/ogg',
		'pdf' => 'application/pdf',
		'djvu' => 'image/vnd.djvu'
	],
	// допустимые расширения файлов книг
	'book_allowed_file_extensions' => ['fb2', 'epub', 'docx', 'mobi', 'odt', 'rtf', 'txt', 'doc', 'mp3', 'ogg', 'pdf', 'djvu'],
	// допустимые расширения файлов книг которые можно загрузить
	'upload_allowed_file_extensions' => ['zip', 'fb2', 'epub', 'docx', 'mobi', 'odt', 'rtf', 'txt', 'doc', 'mp3', 'ogg', 'pdf', 'djvu'],
	// файлы которые не нужно архивировать, так как сжатие почти не влияет на размер файла
	'not_zip_extensions' => ['zip', 'epub', 'djvu', 'mp3', 'ogg', 'odt', 'docx'],
	// не нужно создавать файлы книг или обрабатывать файл
	'no_need_convert' => ['mp3', 'ogg', 'pdf', 'djvu'],
	'class_prefix' => 'u-', // префикс для классов
	'id_prefix' => 'u-', // префикс для индексов
	'fb2_prefix' => 'l',
	'smiley_path' => 'images/smiles',
	'votes' => [10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
	'max_image_size' => 10000, // максимальный размер изображения в килобайтах
	'max_file_size' => '100000',  // максимальный размер файла в килобайтах
	'comments_on_page_count' => 15,
	'forum_posts_on_page_count' => 10,
	'max_image_width' => 2000, // максимальный размер ширины изображения
	'max_image_height' => 2000, // максимальный размер высоты изображения

	'animation_max_image_width' => 1000, // максимальный размер ширины для анимированных изображений
	'animation_max_image_height' => 1000, // максимальный размер высоты для анимированных изображений

	'read_allowed_fonts' => [
		0 => 'Default',
		1 => 'Arial',
		2 => 'Arial Black',
		3 => 'Arial Narrow',
		4 => 'Book Antiqua',
		5 => 'Century Gothic',
		6 => 'Comic Sans MS',
		7 => 'Courier New',
		8 => 'Franklin Gothic Medium',
		9 => 'Garamond',
		10 => 'Georgia',
		11 => 'Impact',
		12 => 'Lucida Console',
		13 => 'Microsoft Sans Serif',
		14 => 'Palatino Linotype',
		15 => 'Tahoma',
		16 => 'Times New Roman',
		17 => 'Trebuchet MS',
		18 => 'Verdana'
	],

	'read_text_align' => [
		1 => 'left',
		2 => 'right',
		3 => 'center',
		4 => 'justify'],

	'read_font_size' => array_combine(range(9, 99), range(9, 99)),

	'read_default_font' => 'Arial',
	'read_default_align' => 'justify',
	'read_default_size' => '18',
	'read_default_background_color' => '#EEEEEE',
	'read_default_font_color' => '#000000',
	'read_default_card_color' => '#FFFFFF',

	'id_of_the_group_of_banned_users' => 6,

	'user_last_activity' => 5, // количество минут, после которых пользователь считается не онлайн

	'time_that_can_edit_message' => '60', // время в минутах после которого нельзя будет отредактировать личное сообщение

	'manager_characters' => ['author', 'editor'], // типы модераторов и заявок

	'settings_list' => [
		'hide_from_main_page_topics' // список топиков, которые нужно скрыть с главной страниы
	], // список индексов настроек

	'textarea_rows' => '12',

	// содержит все известные хосты сайта, чтобы знать с каких хостов скачивать изображения, а каких нет
	'site_hosts' => [
		preg_replace('/^(www\.)/iu', '', parse_url(env('APP_URL'), PHP_URL_HOST)),
		'www.' . preg_replace('/^(www\.)/iu', '', parse_url(env('APP_URL'), PHP_URL_HOST)),
		'litlife.co',
		'www.litlife.co',
		'litlife.club',
		'www.litlife.club',
	],

	'available_fonts' => [
		1 => 'Arial',
		2 => 'Arial Black',
		3 => 'Arial Narrow',
		4 => 'Book Antiqua',
		5 => 'Century Gothic',
		6 => 'Comic Sans MS',
		7 => 'Courier New',
		8 => 'Franklin Gothic Medium',
		9 => 'Garamond',
		10 => 'Georgia',
		11 => 'Impact',
		12 => 'Lucida Console',
		13 => 'Microsoft Sans Serif',
		14 => 'Palatino Linotype',
		15 => 'Tahoma',
		16 => 'Times New Roman',
		17 => 'Trebuchet MS',
		18 => 'Verdana'
	],

	'assets_path' => '/assets',

	'support_images_formats' => ['jpeg', 'gif', 'png'],

	'cooldown_for_create_new_book_files_after_edit' => 5,
	'noimage' => '/img/noimage.png',
	'book_noimage' => '/img/nocover3_1.jpeg',
	'font_size' => ['min' => 12, 'max' => 20],
	'min_book_price' => 10,  // минимальная цена книги
	'max_book_price' => 500, // максимальная цена книги
	'max_symbols_on_one_page' => 8000,  // максимальное количество символов на одной странице
	'comission' => 30, // комиссия в процентах по умолчанию, которую магазин забирает себе
	'book_price_update_cooldown' => 7, // Количество дней в течении которых нельзя будет изменить цену книги
	'min_outgoing_payment_sum' => 500, // минимальная сумма для вывода денег
	'max_outgoing_payment_retry_failed_count' => 3, // максимальное количество попыток отправить платеж. Если количество больше, то платеж отменяется
	'purse_id' => 1, // ID аккаунта или кошелька сайта, на который перечисляется прибыль сайта,
	'minimum_characters_count_before_book_can_be_sold' => 8000, // минимальное количество символов для того чтобы книгу можно было добавить на продажу

	'name_user_refrence_get_param' => 'litlife_ref', // название get параметра из которого будет взят refrence id
	'comission_from_refrence_buyer' => 10, // вознаграждение в процентах от купленной книги привлеченного покупателя по умолчанию. Вычитается из комисии сайта
	'comission_from_refrence_seller' => 10, // вознаграждение в процентах от проданной книги привлеченного продавца по умолчанию. Вычитается из комисии сайта
	'minimum_days_to_submit_a_new_request_for_author_sale' => 7, // минимальное время в днях сколько должно пройти до возможности подать новую заявку на возможность продавать книги у автора
	'delete_notifications_in_days' => 90,  // количество дней, через которое удалять уведомления
	'default_user_group_id' => 2, // ID группы пользователя по умолчанию
	'max_section_characters_count' => 100000, // максимальное количество символов в главе
	'max_annotation_characters_count' => 10000, // максимальное количество символов в аннотации
	'min_annotation_characters_count_for_sale' => 100, // минимальное количество символов в аннотации, чтобы можно было добавить книгу на продажу
	'book_removed_from_sale_cooldown_in_days' => 30, // сколько дней дается читателю дочитать книгу перед тем, как ее автор сможет удалить или скачать
	'disk_for_files' => 'private', // куда сохранять файлы книг

	'max_user_photo_width' => 2000, // Максимальная ширина аватара пользователя
	'max_user_photo_height' => 2000, // Максимальная высота аватара пользователя
	'number_of_days_after_which_to_delete_unused_password_recovery_tokens' => 2, // количество дней через которые удалять не использованные токены восстановления пароля
	'minimum_number_of_characters_per_page_to_display_ads' => 1000, // минимальное количетсво символов на странице для отображения рекламы
	'sitemap_dirname' => 'sitemap', // название папки в которой хранятся карты сайта
	'sitemap_storage' => 'public', // название хранилища для хранения карт сайта

	'max_number_of_capital_letters' => 70, // если процент количества символов заглавных символов больше указанного, то сообщение отправляется на проверку
	'min_password_length' => 6, // минимальная длинна пароля

	'max_number_of_outgoing_payments_per_month' => 3, // TODO Максимальное количество выплат в месяц
	'the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books' => 30000, // общее количество
	// символов книг автора для того, чтобы было разрешено отправить заявку на разрешение продавать книги
	'recommended_minimum_free_fragment_as_a_percentage' => 20, // рекомендуемое минимальный бесплатный фрагмент в процентах
	'minimum_number_of_letters_and_numbers' => 3 // минимальная длинна поисковой строки
];