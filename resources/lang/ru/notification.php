<?php

return [
	'nothing_found' => 'Ни одного уведомления не найдено',
	'notification' => 'Уведомления',
	"comment_reply" => [
		"line" => "На ваш комментарий ответил пользователь :userName",
		"subject" => "Новый ответ на комментарий",
		'action' => 'Перейти к ответу'
	],
	"forum_reply" => [
		"line" => "На форуме вам ответил пользователь :userName",
		"subject" => "Новый ответ на форуме",
		'action' => 'Перейти к ответу'
	],
	"new_personal_message" => [
		"line" => "Вам написал личное сообщение пользователь :userName",
		"subject" => "Новое личное сообщение",
		'action' => 'Перейти к диалогу'
	],
	"new_wall_message" => [
		"line" => "На вашей стене написал пользователь :userName",
		"subject" => "Новое сообщение на стене",
		'action' => 'Перейти на стену'
	],
	"wall_reply" => [
		"line" => "На ваше сообщение на стене ответил пользователь :userName",
		"subject" => "Новый ответ на ваше сообщение на стене",
		'action' => 'Перейти к ответу'
	],
	"book_finish_parse" => [
		"line" => "",
		"subject" => "Обработка книги «:title» завершилась",
		'action' => 'Перейти к книге'
	],
	"author_sale_request_accepted" => [
		"line" => "Мы рассмотрели вашу заявку. Теперь вы можете начать продавать книги на странице автора  «:author_name»",
		"subject" => "Заявка рассмотрена",
		'action' => 'Перейти к странице автора'
	],
	"author_sale_request_rejected" => [
		"line" => "Мы рассмотрели вашу заявку. К сожалению, мы не можем вам дать разрешение продавать книги для страницы автора «:author_name»",
		"subject" => "Заявка рассмотрена",
		'action' => 'Перейти к заявке'
	],
	"author_manager_request_accepted" => [
		"line" => "Мы рассмотрели вашу заявку. Теперь вы являетесь владельцем страницы автора «:author_name»",
		"subject" => "Заявка рассмотрена",
		'action' => 'Перейти к странице автора'
	],
	"author_manager_request_rejected" => [
		"line" => "Мы рассмотрели вашу заявку. К сожалению, мы не можем сделать вас владельцем страницы автора «:author_name»",
		"subject" => "Заявка рассмотрена",
		'action' => 'Перейти к заявке'
	],
	"billing_information_changed" => [
		"line" => "Сообщаем, что ваши данные были изменены на следующие:",
		"subject" => "Ваши платежные данные изменены",
		'action' => 'Просмотреть данные'
	],
	"withdrawal_ordered" => [
		"line" => "Сообщаем, что в вашем кошельке заказана выплата на сумму :sum р. по следующим реквизитам:",
		'line2' => ':payment_type :purse',
		'line3' => 'Номер выплаты #:transaction_id',
		'line4' => 'Выплата будет произведена в ближашее время.',
		"subject" => "Заказана выплата",
		'action' => 'Перейти в кошелек'
	],
	"withdrawal_success" => [
		"line" => "Выплата на сумму :sum р. (+ комиссия платежной системы :comission р.) успешно произведена по следующим реквизитам:",
		'line2' => ':payment_type :purse',
		'line3' => 'Номер выплаты #:transaction_id',
		"subject" => "Выплата успешно произведена",
		'action' => 'Перейти в кошелек'
	],
	"book_sold" => [
		"line" => "Пользователь :user_name купил у вас книгу :book_title. На кошелек зачислено :sum р.",
		"subject" => "Успешная продажа книги",
		'action' => 'Перейти в кошелек'
	],
	"book_purchased" => [
		"line" => "Благодарим за покупки книги «:book_title - :writers_names» и желаем приятного чтения!",
		"subject" => "Успешная покупка книги",
		'action' => 'Перейти на страницу книги'
	],
	"new_refferd_user" => [
		"line" => "Новый пользователь :user_name, привлеченный вами, только что прошел регистрацию. Вы можете связаться с ним и поприветствовать или возможно помочь разобраться в функционале сайта.",
		"subject" => "Новый привлеченный пользователь",
		'action' => 'Перейти к странице пользователя'
	],
	"book_published" => [
		"line" => "Сообщаем вам, что книга «:book_title - :writers_names» опубликована",
		"subject" => "Книга опубликована",
		'action' => 'Перейти на страницу книги'
	],
	"new_post_in_subscribed_topic" => [
		"line" => "Новое сообщение оставил :user_name в теме «:topic_title», на которую вы подписаны",
		"subject" => "Новое сообщение в теме «:topic_title»",
		'action' => 'Перейти к сообщению'
	],
	"book_removed_from_sale" => [
		"line" => "Сообщаем вам, что, к сожалению, автор снял книгу «:book_title - :writers_names» с продажи и через :days дней книга возможно будет удалена или заблокирована для чтения автором. В течении этого времени советуем вам дочитать текст или скачать файл книги.",
		"subject" => "Важное сообщение о купленной книге «:book_title»",
		'action' => 'Перейти на страницу книги'
	],
	"sincerely_yours" => "С уважением",
	"unsubscribe" => "от всех уведомлений на этот почтовый ящик",
	'greeting' => 'Привет',
	'subcopy' => 'Если у вас возникли проблемы с нажатием кнопки «:actionText», скопируйте и вставьте URL-адрес расположенный ниже в свой веб-браузер: :actionUrl',
	'on_site' => 'Получать уведомления на сайте:',
	'on_email' => 'Получать уведомления на электронный почтовый ящик :email:',
	"new_like_notification" => [
		'blog' => [
			"line" => "Ваше сообщение на стене понравилось пользователю :userName",
			"subject" => "",
			'action' => 'Перейти к сообщению'
		],
		'post' => [
			"line" => "Ваше сообщение на форуме понравилось пользователю :userName",
			"subject" => "",
			'action' => 'Перейти к сообщению'
		],
		'book' => [
			"line" => "Ваша добавленная книга «:book_title» понравилась пользователю :userName",
			"subject" => "",
			'action' => 'Перейти к книге'
		],
		"comment" => [
			"line" => "Ваш комментарий к книге «:book_title» понравился пользователю :userName",
			"subject" => "",
			'action' => 'Перейти к комментарию'
		],
		"collection" => [
			"line" => "Ваша подборка «:collection_title» понравилась пользователю :userName",
			"subject" => "",
			'action' => 'Перейти к подборке'
		]
	],
	"new_subscriber" => [
		"line" => "На вас подписался :userName",
		"subject" => "У вас новый подписчик",
		'action' => 'Перейти на страницу подписчика'
	],
	"invitation" => [
		"line" => "Вы получили приглашение на регистрацию. Для продолжения регистрации нажмите на кнопку ниже.",
		"subject" => "Приглашение на регистрацию",
		'action' => 'Продолжить регистрацию'
	],
	"password_reset" => [
		"subject" => "Восстановление пароля",
		"line" => "Для установки нового пароля нажмите на кнопку ниже. Для безопасности никому не пересылайте это письмо, и рекомендуем его удалить после завершения процедуры восстановления.",
		'action' => 'Установить новый пароль'
	],
	"email_confirm" => [
		"subject" => "Подтверждение почтового ящика",
		"line" => "Для подтверждения почтового ящика :email нажмите на кнопку ниже",
		'action' => 'Подтвердить почтовый ящик'
	],
	"group_assigment" => [
		"subject" => "Вам присвоена группа",
		"line" => "Вам присвоена группа пользователей :group_name",
		'action' => 'Перейти в свой аккаунт'
	],
	"new_comment_in_collection" => [
		"subject" => "Новый комментарий",
		"line" => "В подборке «:collection_title» появился новый комментарий от :create_user_name",
		'action' => 'Перейти к комментарию'
	],
	'test' => [
		"subject" => "Заголовок уведомления",
		"line" => "Текст уведомления",
		'action' => 'Текст кнопки уведомления'
	],
	'user_has_registered' => [
		"subject" => "Вы успешно зарегистрировались",
		"line" => "Поздравляем с успешной регистрацией на сайте Литлайф!",
		'line2' => 'Для входа на сайт используйте следующие данные:',
		"line3" => "Email: **:email**",
		"line4" => "Пароль: **:password**",
		'action' => 'Перейти к своему профилю'
	],
	'invoice_was_successfully_paid' => [
		"subject" => "Кошелек успешно пополнен",
		"line" => "Ваш кошелек успешно пополнен на сумму :sum р.",
		'action' => 'Перейти в кошелек'
	],
	'invitation_to_sell_books' => [
		"greeting" => 'Уважаемый автор',
		"subject" => "Предлагаем вам взаимовыгодное сотрудничество",
		'action' => 'Начать продавать книги'
	],
	'book_text_processing_complete' => [
		"line" => "Обработка текста книги «:book_title» завершилась",
		"subject" => "",
		'action' => 'Перейти к книге'
	],
	'book_removed_from_publication' => [
		"subject" => "",
		"line" => "Ваша опубликованная книга «:book_title - :writers_names» снята с публикации по причине: :reason",
		'action' => 'Перейти к книге'
	],
	'book_deleted' => [
		"subject" => "",
		"line" => "Ваша опубликованная книга «:book_title - :writers_names» удалена.",
		"reason" => "Причина: :reason",
		'action' => 'Перейти к книге'
	],
	'sending_invitation_to_take_survey' => [
		"subject" => "Как вам наш сайт ЛитЛайф?",
		"line" => "Пожалуйста, оцените работу нашего сайта. Заранее спасибо!",
		'action' => 'Нажмите, чтобы начать опрос'
	],
	'purchase_canceled' => [
		"subject" => "Покупка отменена",
		"line" => "Покупка книги «:book_title - :writers_names» отменена. Сумма потраченная на покупку книги возвращена на ваш внутренний кошелек. Приносим извинения за неудобства.",
		'action' => 'Перейти в кошелек'
	],
	'sale_canceled' => [
		"subject" => "Продажа отменена",
		"line" => "Продажа книги «:book_title - :writers_names» отменена. Сумма полученная с продажи книги возвращена покупателю.",
		'action' => 'Перейти в кошелек'
	]
];
