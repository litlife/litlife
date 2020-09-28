@extends('layouts.app')

@push('css')
	<!--
	<link href="{{ mix('css/faq.css', config('litlife.assets_path')) }}" rel="stylesheet">
	-->
@endpush

@push('scripts')
	<script src="{{ mix('js/faq.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	<div id="faq">
		<div class="card mb-3">
			<div class="card-body" style="">

				<h4 id="books" class="mb-3">
					<a href="#books">Книги</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="how_to_add_a_book">
						<a href="#how_to_add_a_book">Как добавить книгу?</a>
					</dt>

					<dd>Чтобы добавить книгу <a href="{{ route('books.create') }}" class="text-info">перейдите в раздел</a>.
						Далее действуйте по шагам
					</dd>

					<dt id="how_to_publish_a_book">
						<a href="#how_to_publish_a_book">Как опубликовать книгу?</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу книги</li>
							<li>Откройте меню книги и нажмите "Опубликовать"</li>
						</ul>
					</dd>

					<dt id="how_to_find_out_if_a_book_is_published">
						<a href="#how_to_find_out_if_a_book_is_published">Как узнать что книга опубликована?</a>
					</dt>

					<dd>
						Если книга опубликована, то на странице книги в описании будет запись "Опубликована (Дата публикации)".
						Если присутствует надпись "Книга находится в процессе размещения", то вам необходимо дождаться публикации книги.
						По завершении публикации вам будет отправлено уведомление.
					</dd>

					<dt id="what_does_the_lock_symbol_mean_next_to_the_book_title">
						<a href="#what_does_the_lock_symbol_mean_next_to_the_book_title">
							Что означает символ "замка" <i class="fas fa-lock"></i> рядом с
							названием книги?</a>
					</dt>

					<dd>Это означает, что книга находится в вашем
						<a href="#what_is_personal_access_to_a_book" class="text-info">личном доступе</a> и доступ к ее странице имеете только вы.
						Чтобы "убрать замок" необходимо <a href="#how_to_publish_a_book" class="text-info">опубликовать книгу</a>.
					</dd>

					<dt id="where_can_i_find_my_added_books">
						<a href="#where_can_i_find_my_added_books">Где найти мои добавленные книги?</a>
					</dt>

					<dd>В левом меню нажмите на "Мои книги" и нажмите на "Созданные". Перед вами будет список ваших добавленных книг.</dd>

					<dt id="what_is_personal_access_to_a_book">
						<a href="#what_is_personal_access_to_a_book">Что такое личный доступ к книге?</a>
					</dt>

					<dd>
						Это значит, что доступ к странице и тексту книги имеете только вы.
						Рядом с названием таких книг можно увидеть значок замка <i class="fas fa-lock"></i> .
						Книги из личного доступа в поиске и прочих списках книг видите только вы.
						Чтобы книга стала видна всем ее необходимо <a href="#how_to_publish_a_book" class="text-info">опубликовать</a>.
					</dd>

					<dt id="how_to_remove_the_lock_symbol">
						<a href="#how_to_remove_the_lock_symbol">Как сделать так, чтобы книгу видели все и убрать символ замка <i class="fas fa-lock"></i> ?</a>
					</dt>

					<dd>Для этого вам нужно <a href="#how_to_publish_a_book" class="text-info">опубликовать</a> книгу</dd>

					<dt id="why_after_i_added_the_book_it_doesnt_have_access">
						<a href="#why_after_i_added_the_book_it_doesnt_have_access">
							Почему после публикации книги у нее отсутствует доступ к чтению и скачиванию?</a>
					</dt>

					<dd>В целях соблюдения авторских прав мы разрешаем отрывать доступ к чтению и скачиванию только для пользователей с
						<a href="#what_is_a_verified_page_of_the_author" class="text-info">верифицированной страницей автора</a>.
						Исключения составляют любительские переводы.
						Если вы <a href="#what_is_a_verified_page_of_the_author" class="text-info">верфицированный автор</a>, то после публикации книги вы
						можете
						<a href="#how_to_open_access_to_reading_or_downloading" class="text-info">открыть к ней доступ</a> .

					</dd>

					<dt id="what_does_the_si_label_mean">
						<a href="#what_does_the_si_label_mean">Что означает метка "СИ" рядом с названием книги?</a>
					</dt>

					<dd>
						Метка "СИ" это сокращение от "Самиздат". Такой меткой отмечаются не изданные каким-либо издательством книги.
					</dd>

					<dt id="what_does_the_lp_label_mean">
						<a href="#what_does_the_lp_label_mean">Что означает метка "ЛП" рядом с названием книги?</a>
					</dt>

					<dd>
						Метка "ЛП" это сокращение от "Любительский перевод". Такой меткой отмечаются переводы книги не изданные каким-либо издательством.
					</dd>

					<dt id="what_should_i_do_if_your_authors_rights_are_violated">
						<a href="#what_should_i_do_if_your_authors_rights_are_violated">
							Что делать, если ваши права автора нарушены?
						</a>
					</dt>

					<dd>Пожалуйста, немедленно отправьте нам жалобу. Узнать как отправить жалобу можно на
						<a class="text-info" href="{{ route('for_rights_owners') }}">этой странице</a>.
						После рассмотрения жалобы доступ к чтению и скачиванию книги будет закрыт, но страница книги останется.
						Если вы <a href="#what_is_a_verified_page_of_the_author" class="text-info">верифцированный автор</a>,
						то доступ к своим книгам вы может
						<a href="#how_do_i_block_access_to_reading_or_downloading" class="text-info">закрывать самостоятельно</a>.
					</dd>

					<dt id="why_do_you_have_books_without_access_to_reading_or_downloading">
						<a href="#why_do_you_have_books_without_access_to_reading_or_downloading">
							Почему у вас есть книги без доступа к чтению или скачиванию?
						</a>
					</dt>

					<dd>В целях соблюдения авторских прав мы закрываем доступ к книгам, которые были добавлены пользователями без разрешения автора.</dd>

					<dt id="why_dont_you_delete_books_without_reading_or_downloading_access">
						<a href="#why_dont_you_delete_books_without_reading_or_downloading_access">
							Почему вы не удаляете книги без доступа к чтению или скачиванию?
						</a>
					</dt>

					<dd>
						Мы оставляем пользователям возможность написать и прочитать отзывы о книгах,
						оценить книгу, отметить книгу как прочитанную, и т. д.
					</dd>

					<dt id="what_is_the_text_of_a_book_for_online_reading">
						<a href="#what_is_the_text_of_a_book_for_online_reading">
							Что такое текст книги для онлайн чтения?
						</a>
					</dt>

					<dd>
						Это текст книги который можно читать на сайте без скачивания файлов.
						Перейдите на страницу книги и нажмите кнопку "Читать онлайн", чтобы открыть текст онлайн чтения.
						Может быть извлечен из файла при <a href="#how_to_add_a_book" class="text-info">добавлении книги</a>,
						извлчен из <a href="#can_i_add_the_text_of_an_existing_book_page" class="text-info">прикрепленного файла</a>
						или добавлен вручную в <a href="#how_to_edit_the_text_of_a_book" class="text-info">редактировании текста книги</a>
					</dd>

					<dt id="how_attach_a_book_file_to_an_existing_book_page">
						<a href="#how_attach_a_book_file_to_an_existing_book_page">
							Как прикрепить файл книги к существующей странице книги?
						</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу книги</li>
							<li>Нажмите "Прикрепить файл книги"</li>
							<li>Выберите файл книги на вашем устройстве и нажмите "Загрузить"</li>
						</ul>
					</dd>

					<dt id="where_to_find_your_favorite_books">
						<a href="#where_to_find_your_favorite_books">Где найти мои избранные книги?</a>
					</dt>

					<dd>
						<ul>
							<li>Откройте <a href="#what_is_the_user_menu" class="text-info">меню пользователя</a></li>
							<li>Нажмите "Мои книги"</li>
							<li>В открывшемся меню нажмите "Избранное"</li>
						</ul>
					</dd>

				</div>

				<h4 id="edit_book" class="mb-3">
					<a href="#edit_book">Редактирование книги</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="how_to_edit_a_book_description">
						<a href="#how_to_edit_a_book_description">
							Как отредактировать описание книги?
						</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу книги</li>
							<li>Откройте меню книги и нажмите "редактировать"</li>
							<li>Внесите нужные изменения в описание и нажмите кнопку "Сохранить"</li>
						</ul>
					</dd>

					<dt id="how_to_edit_the_text_of_a_book">
						<a href="#how_to_edit_the_text_of_a_book">
							Как отредактировать <a href="#what_is_the_text_of_a_book_for_online_reading" class="text-info">текст книги</a> для онлайн чтения?
						</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу книги</li>
							<li>Откройте меню книги и нажмите "редактировать"</li>
							<li>Нажмите на "Главы". Перед вами появится список глав книги</li>
							<li>Здесь вы можете отредактировать текст книги: разбить текст на главы,
								добавить новые главы, добавить подглавы и изменить расположение глав.
							</li>
						</ul>
					</dd>

					<dt id="how_to_add_a_new_chapter">
						<a href="#how_to_add_a_new_chapter">Как добавить новую главу?</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу <a href="#how_to_edit_the_text_of_a_book" class="text-info">списка глав книги</a>.</li>
							<li>Нажмите на кнопку "Добавить новую главу"</li>
						</ul>
					</dd>

					<dt id="how_to_edit_a_chapter">
						<a href="#how_to_edit_a_chapter">Как отредактировать главу?</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите к <a href="#how_to_edit_the_text_of_a_book" class="text-info">списку глав книги</a>.</li>
							<li>Откройте меню главы и нажмите "редактировать"</li>
							<li>Внесите изменения в заголовок или текст главы.</li>
							<li>Нажмите "Сохранить", чтобы сохранить изменения</li>
						</ul>
					</dd>

					<dt id="how_to_divide_the_text_of_a_book_into_chapters">
						<a href="#how_to_divide_the_text_of_a_book_into_chapters">
							Как разделить текст книги на главы, если весь текст находится в одной главе?
						</a>
					</dt>

					<dd>
						Тексты книг созданные из форматов fb2 и epub могут быть уже разделены на главы (но не всегда!).
						Для остальных книжных форматов необходмо разделение вручную:
						<ul>
							<li>Перейдите к <a href="#how_to_edit_a_chapter" class="text-info">редактированию главы</a>.</li>
							<li>Установите курсор в тексте, где необходимо вставить разрез.</li>
							<li>Найдите и нажмите на кнопку
								<button class="btn btn-light">
									<img src="{{ asset('/ckeditor/plugins/sectionbreak/icons/sectionbreak.png') }}"/>
								</button>
								на панели редактирования.<br/>
								Вы увидите, что в тексте появилась пунктирная линия.
								Она показывает, что текст будет разрезан в этом месте.
							</li>
							<li>Проставьте во всем тексте все необходимые разрезы и нажмите "сохранить", чтобы завершить процесс разделения.</li>
						</ul>
						В данный момент мы тестируем функцию автоматического разделения, но она доступна только модераторам сайта.
						Если вы хотите помочь протестировать на вашей книге, то напишите в тему попросить модератора.
						Модератор может попробовать разделить только опубликованные книги.
					</dd>

					<dt id="how_to_change_the_location_of_chapters">
						<a href="#how_to_change_the_location_of_chapters">Как изменить расположение глав?</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите к <a href="#how_to_edit_the_text_of_a_book" class="text-info">списку глав книги</a>.</li>
							<li>Откройте меню главы.</li>
							<li>Нажмите на пункт "переместить". Не отпуская протащите главу в нужное место. Затем отпустите.</li>
							<li>Переместите все главы и нажмите "Сохранить расположение глав".</li>
						</ul>
					</dd>

					<dt id="what_does_the_chapter_published_and_draft_mean">
						<a href="#what_does_the_chapter_published_and_draft_mean">
							Для чего нужны опции "Глава опубликована" и "Черновик" при создании и редактировании главы?
						</a>
					</dt>

					<dd>
						В процессе написания книги вы можете сохранять не дописанные главы как "Черновик". Черновики видны только вам.
						Чтобы главу увидели читатели, поменяйте статус на "Глава опубликована".
					</dd>

					<dt id="can_i_add_the_text_of_an_existing_book_page">
						<a href="#can_i_add_the_text_of_an_existing_book_page">
							У меня есть добавленная книга, но в ней отсутствует текст.
							Можно ли из файла добавить
							<a href="#what_is_the_text_of_a_book_for_online_reading" class="text-info">текст онлайн чтения</a>
							существующей книги?
						</a>
					</dt>

					<dd>
						Да, это можно сделать.
						<ul>
							<li>Перейдите на страницу книги</li>
							<li><a href="#how_attach_a_book_file_to_an_existing_book_page" class="text-info">Прикрепите новый файл книги</a></li>
							<li>Через несколько минут обновите страницу книги</li>
							<li>У книги должны появиться главы и страницы</li>
						</ul>
					</dd>

					<dt id="i_would_like_to_replace_the_existing_text_from_another_file">
						<a href="#i_would_like_to_replace_the_existing_text_from_another_file">
							У моей добавленной книги есть страницы онлайн чтения.
							Я хотел бы заменить существующие страницы из файла.
						</a>
					</dt>

					<dd>
						Как это сделать:
						<ul>
							<li>Перейдите на страницу книги</li>
							<li>Если файл книги присутствует, то откройте его меню и нажмите "Сделать источником".
								Если файла книги нет, то
								<a href="#how_attach_a_book_file_to_an_existing_book_page" class="text-info">прикрепите новый файл книги</a>.
								Обратите внимание, что все существующие
								<a href="#what_is_the_text_of_a_book_for_online_reading" class="text-info">страницы онлайн чтения</a> будут удалены!
							</li>
							<li>Через пару минут новые страницы будут созданы из выбранного файла книги</li>
						</ul>
					</dd>

					<dt id="where_are_the_book_attachments_located">
						<a href="#where_are_the_book_attachments_located">
							Где находятся вложения книги?
						</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу книги</li>
							<li>Откройте меню книги и нажмите "редактировать"</li>
							<li>В вернем навигационном меню нажмите "Вложения"</li>
						</ul>
					</dd>

					<dt id="how_to_make_a_cover_image_from_a_book_attachment">
						<a href="#how_to_make_a_cover_image_from_a_book_attachment">
							Как сделать обложкой изображение из вложений книги?
						</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите во <a href="#where_are_the_book_attachments_located" class="text-info">вложения книги</a></li>
							<li>Надите нужное изображение</li>
							<li>Откройте меню изображение и нажмите "Сделать обложкой"</li>
						</ul>
					</dd>

				</div>

				<h4 id="authors_and_veritication" class="mb-3">
					<a href="#authors_and_veritication">Авторы и верификация</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="what_is_an_authors_page">
						<a href="#what_is_an_authors_page">Что такое страница автора?</a>
					</dt>

					<dd>Страница автора - это страница сайта на которой находится имя, фото автора и список его книг.
						Примеры страниц автора находятся <a href="{{ route('authors') }}" class="text-info">здесь</a>.
						Не нужно путать страницу автора со <a href="#what_is_a_user_account" class="text-info">страницей пользователя</a>.
					</dd>

					<dt id="how_to_create_an_author_page">
						<a href="#how_to_create_an_author_page">Как создать страницу автора?</a>
					</dt>

					<dd>Перейдите к форме <a href="{{ route('authors.create') }}" class="text-info">создания авторской страницы</a>.
						Заполните форму и нажмите "создать".
					</dd>

					<dt id="how_to_publish_an_author_or_series">
						<a href="#how_to_publish_an_author_or_series">Как опубликовать автора или серию?</a>
					</dt>

					<dd>
						Страницы автора или серии опубликуются автоматически при
						<a href="#how_to_publish_a_book" class="text-info">публикации книги</a> к которой они привязаны.
					</dd>

					<dt id="what_does_the_lock_symbol_next_to_the_authors_name_mean">
						<a href="#what_does_the_lock_symbol_next_to_the_authors_name_mean">Что означает символ "замка"
							<i class="fas fa-lock"></i> рядом с названием автора?</a>
					</dt>

					<dd>Означает, что страница автора не опубликована и находится в вашем личном доступе.</dd>

					<dt id="what_is_a_verified_page_of_the_author">
						<a href="#what_is_a_verified_page_of_the_author">Что такое верифицированная страница автора?</a>
					</dt>

					<dd>
						Верификация означает привязку
						<a href="#what_is_an_authors_page" class="text-info">страницы автора</a> к
						<a href="#what_is_a_user_account" class="text-info">странице пользователя</a>.
						Или проще говоря вы подтверждаете, что вы являетесь автором книг на странице.
						К странице пользователя можно привязать несколько страниц автора.
					</dd>

					<dt id="why_do_i_need_to_verify_the_authors_page">
						<a href="#why_do_i_need_to_verify_the_authors_page">Зачем вообще нужно верифицировать страницу автора?</a>
					</dt>

					<dd>Верфицированный автор получает права редактировать фото, описание на авторской странице и т. д.
						Получает права
						<a href="#how_to_open_access_to_reading_or_downloading" class="text-info">открывать</a>
						и
						<a href="#how_do_i_block_access_to_reading_or_downloading" class="text-info">закрывать</a>
						доступ книгам, <a href="#how_to_edit_the_text_of_a_book" class="text-info">редактировать текст</a> не изданных книг и т.д.
						Для начала продаж книг верификация страницы автора необходима.
					</dd>

					<dt id="how_to_verify_the_authors_page">
						<a href="#how_to_verify_the_authors_page">Как верифицировать страницу автора?</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу автора и нажмите на кнопку "Я автор".</li>
							<li>Заполните заявку и нажмите "Отправить".</li>
							<li>В течении нескольких дней администрация сайта проверит вашу заявку.</li>
							<li>Если заявка будет одобрена вы получите верифицированную страницу автора.</li>
						</ul>
					</dd>

					<dt id="can_i_merge_the_authors_page_and_the_users_page">
						<a href="#can_i_merge_the_authors_page_and_the_users_page">
							У меня теперь две страницы.
							Можно ли слить
							<a href="#what_is_an_authors_page" class="text-info">страницу автора</a> и
							<a href="#what_is_a_user_account" class="text-info">страницу пользователя</a>?
						</a>
					</dt>

					<dd>
						Нет, слить в одну страницу нельзя. Можно только привязать
						<a href="#what_is_an_authors_page" class="text-info">страницу автора</a> к
						<a href="#what_is_a_user_account" class="text-info">странице пользователя</a> -
						этот процесс называется
						<a href="#what_is_a_verified_page_of_the_author" class="text-info">верификацией</a>.
					</dd>

					<dt id="how_do_i_delete_a_book_from_my_author_page">
						<a href="#how_do_i_delete_a_book_from_my_author_page">
							Я верифицировал страницу автора. Как мне удалить книгу с моей страницы?
						</a>
					</dt>

					<dd>
						Мы не разрешаем удалять книги, чтобы не терять оценки и отзывы о книгах.
						Но вы можете
						<a href="#how_do_i_block_access_to_reading_or_downloading" class="text-info">закрыть доступ к чтению и скачиванию</a>.
					</dd>

					<dt id="how_do_i_delete_them_from_another_users_books">
						<a href="#how_do_i_delete_them_from_another_users_books">
							Я верифицировал страницу автора и на ней есть книги добавленные не мной. Как мне их убрать?</a>
					</dt>

					<dd>
						Книги добавленные другими пользователями можно заменить своей авторской версией книги.
						<ul>
							<li>Сначала <a href="#how_to_add_a_book" class="text-info">добавьте свою версию книги</a> и перейдите на ее страницу</li>
							<li>Откройте меню и нажмите "заменить книгу, которую добавил другой пользователь".</li>
							<li>Введите ID книги добавленной другим пользователем и нажмите "Заменить".</li>
						</ul>
						Оценки и комментарии сохранятся под обеими версиями книги.
					</dd>

					<dt id="how_do_i_block_access_to_reading_or_downloading">
						<a href="#how_do_i_block_access_to_reading_or_downloading">Как закрыть доступ к чтению или скачиванию?</a>
					</dt>

					<dd>
						Вам необходимо иметь
						<a href="#what_is_a_verified_page_of_the_author" class="text-info">верифицированную страницу автора</a>,
						чтобы появилась возможность закрывать доступ к книгам, которые расположены на
						<a href="#what_is_an_authors_page" class="text-info">вашей странице автора</a>.
						<ul>
							<li>Перейдите на страницу книги.</li>
							<li>Откройте меню книги.</li>
							<li>Нажмите на пункт "доступ к чтению и скачиванию".</li>
							<li>Уберите галочки "Доступ к чтению", "Доступ к скачиванию"</li>
							<li>Нажмите "Сохранить"</li>
						</ul>
					</dd>

					<dt id="how_to_open_access_to_reading_or_downloading">
						<a href="#how_to_open_access_to_reading_or_downloading">Как открыть доступ к чтению или скачиванию?</a>
					</dt>

					<dd>
						Вам необходимо иметь
						<a href="#what_is_a_verified_page_of_the_author" class="text-info">верифицированную страницу автора</a>,
						чтобы появилась возможность открыть доступ к книгам, которые расположены на
						<a href="#what_is_an_authors_page" class="text-info">вашей странице автора</a>.
						<ul>
							<li>Перейдите на страницу книги.</li>
							<li>Откройте меню книги.</li>
							<li>Нажмите на пункт "доступ к чтению и скачиванию".</li>
							<li>Установите галочки "Доступ к чтению", "Доступ к скачиванию"</li>
							<li>Нажмите "Сохранить"</li>
						</ul>
					</dd>

					<dt id="i_found_a_repeat_of_the_authors_page">
						<a href="#i_found_a_repeat_of_the_authors_page">Я нашел повтор страницы автора. Можно ли их объединить?</a>
					</dt>

					<dd>Сообщите, пожалуйста, нам о повторе:
						<ul>
							<li>Перейдите на страницу автора</li>
							<li>Нажмите "Сообщить о повторе".</li>
							<li>Добавьте все повторы страниц автора в поле "Список авторов".</li>
							<li>И нажмите "Добавить"</li>
						</ul>
					</dd>

					<dt id="where_to_find_your_favorite_authors">
						<a href="#where_to_find_your_favorite_authors">Где найти моих избранных авторов?</a>
					</dt>

					<dd>
						<ul>
							<li>Откройте <a href="#what_is_the_user_menu" class="text-info">меню пользователя</a></li>
							<li>Нажмите "Мои авторы"</li>
							<li>В открывшемся меню нажмите "Избранное"</li>
						</ul>
					</dd>

					<dt id="how_to_get_notifications_about_new_books_by_authors">
						<a href="#how_to_get_notifications_about_new_books_by_authors">Как получать уведомления о новых книгах авторов?</a>
					</dt>

					<dd>
						Для начала вам необходимо добавить в избранное <a href="#what_is_an_authors_page" class="text-info">страницу автора</a>.
						Когда на <a href="#what_is_an_authors_page" class="text-info">странице автора</a> появятся новые книги, вы увидите их количество
						в <a href="#what_is_the_user_menu" class="text-info">меню пользователя</a>, рядом с пунктом "Мои авторы".
						Чтобы просмотреть список последних опубликованных книг нажмите на "Мои авторы", затем на "Книги избранных авторов".
					</dd>

					<dt id="can_i_add_e_wallet_numbers_for_donations_to_the_authors_description">
						<a href="#can_i_add_e_wallet_numbers_for_donations_to_the_authors_description">
							Можно ли в описание автора вносить номера электронных кошельков для пожертвований, донатов?</a>
					</dt>

					<dd>
						Да, после
						<a href="#what_is_a_verified_page_of_the_author" class="text-info">прохождения верификации</a>
						вы можете добавить в описание вашей
						<a href="#what_is_an_authors_page" class="text-info">страницы автора</a>
						номера электронных кошельков и карт для получения пожертвований.
					</dd>

					<dt id="why_reader_can_download_my_book">
						<a href="#why_reader_can_download_my_book">
							Я автор и добавил книгу. Почему читатель не может скачать мою книгу?</a>
					</dt>

					<dd>
						<ul>
							<li>
								Проверьте верифицировали ли вы свою страницу автора. Если нет, то
								<a href="#how_to_verify_the_authors_page" class="text-info">верифицируйте страницу автора</a>.
							</li>
							<li>Проверьте есть ли вообще у книги файлы для скачивания.
								Если нет, то
								<a href="#how_attach_a_book_file_to_an_existing_book_page" class="text-info">прикрепите файл книги</a>.
							</li>
							<li>Проверьте
								<a href="#how_to_find_out_if_a_book_is_published" class="text-info">опубликована</a>
								ли книга. Если нет, то
								<a href="#how_to_publish_a_book" class="text-info">опубликуйте</a> вашу книгу.
							</li>
							<li>Проверьте открыт ли доступ к скачиванию. Если нет, то
								<a href="#how_to_open_access_to_reading_or_downloading" class="text-info">откройте доступ к скачиванию</a>.
							</li>
							<li>
								Если все проверки выполнены, но читатель все еще не может скачать вашу книгу, то
								<a href="#user_question" class="text-info">сообщите нам</a>.
							</li>
						</ul>
					</dd>

				</div>

				<h4 id="sell_books" class="mb-3">
					<a href="#sell_books">Продажи книг</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="i_want_to_sell_my_books">
						<a href="#i_want_to_sell_my_books">Я писатель и хочу продавать мои книги. Как это можно сделать?</a>
					</dt>

					<dd>
						Для удобства писателей мы создали специальную пошаговую инструкцию, которая проведет вас от регистрации, до размещения платной
						книги.<br/>
						После выполнения каждого шага нажмите на кнопку обновить, чтобы получить следующие инструкции.
						<a href="{{ route('authors.how_to_start_selling_books') }}" class="text-info">Перейти к инструкции</a>
					</dd>

					<dt id="where_to_read_the_agreement">
						<a href="#where_to_read_the_agreement">Где ознакомиться с соглашением?</a>
					</dt>

					<dd>
						С соглашением можно будет ознакомиться на этапе
						<a href="#how_to_get_permission_to_sell_books" class="text-info">
							подачи заявки для получения разрешения на продажи книг</a>.
					</dd>

					<dt id="how_to_get_permission_to_sell_books">
						<a href="#how_to_get_permission_to_sell_books">Я верифицировал мою страницу автора. Как мне получить разрешение продавать книги?</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на вашу страницу автора.</li>
							<li>Откройте меню рядом с именем автора и нажмите "Заявка на продажи книг"</li>
							<li>Заполните заявку, ознакомтесь с соглашением и нажмите "Отправить заявку"</li>
							<li>Ожидайте рассмотрения заявки.</li>
							<li>После рассмотрения заявки вы получите уведомление.</li>
						</ul>
					</dd>

					<dt id="how_to_set_the_book_price">
						<a href="#how_to_set_the_book_price">Я получил разрешение продавать книги. Как мне установить цену?</a>
					</dt>

					<dd>
						<ul>
							<li>Перейдите на страницу книги, которую хотите продавать.</li>
							<li>Откройте меню книги и нажмите на "продажи".</li>
							<li>Установите цену и количество бесплатных глав</li>
							<li>Нажмите "Сохранить"</li>
						</ul>
					</dd>

					<dt id="how_do_i_sell_a_book_added_by_another_user">
						<a href="#how_do_i_sell_a_book_added_by_another_user">
							Как мне выставить на продажу книгу добавленную другим пользователем?
						</a>
					</dt>

					<dd>
						Книгу добавленную другим пользователем выставить на продажу нельзя.
						Вам необходимо добавить свою авторскую версию книги и заменить книгу добавленную другим пользователем.
						<a href="#how_do_i_delete_them_from_another_users_books" class="text-info">Здесь подробно описано как это сделать</a>
					</dd>

					<dt id="how_do_i_add_chapters_by_subscription">
						<a href="#how_do_i_add_chapters_by_subscription">Как добавлять главы по подписке?</a>
					</dt>

					<dd>
						Главы по подписке выкладываются так же, как
						<a href="#how_to_add_a_new_chapter" class="text-info">обычные главы</a>.
						Обратите внимание на <a href="#what_does_the_chapter_published_and_draft_mean" class="text-info">функцию</a>.
						Добавляемые главы администрация не проверяет.
					</dd>

				</div>

				<h4 id="user_account" class="mb-3">
					<a href="#user_account">Аккаунт пользователя</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="what_is_a_user_account">
						<a href="#what_is_a_user_account">Что такое аккаунт пользователя?</a>
					</dt>

					<dd>
						Аккаунт пользователя появляется после регистрации пользователя на сайте.
						Для регистрации
						<a href="{{ route('invitation') }}" class="text-info">перейдите сюда</a>.
						После регистрации пользователю становится доступно
						<a href="#what_is_the_user_menu">меню пользователя</a>.
					</dd>

					<dt id="what_is_the_user_menu">
						<a href="#what_is_the_user_menu">Что такое меню пользователя (сайдбар)?</a>
					</dt>

					<dd>
						Когда вы зайдете в аккаунт, слева вы увидите список: "Мои закладки", "Ваше имя", "Сообщения", "Уведомления" и прочее.
						Это и есть меню пользователя.
					</dd>

					<dt id="how_the_user_menu_works">
						<a href="#how_the_user_menu_works">Как работает меню пользователя?</a>
					</dt>

					<dd>
						В версии сайта для компьютера вы можете скрыть или открыть меню нажав на кнопку
						<button class="btn btn-primary"><i class="fas fa-bars"></i></button>
						.<br/>
						В мобильной версии меню всегда скрывается. Нажмите на кнопку, чтобы открыть его.
					</dd>

					<dt id="how_to_change_your_password">
						<a href="#how_to_change_your_password">Как поменять пароль?</a>
					</dt>

					<dd>
						Воспользуйтесь функцией восстановления пароля.
						<ul>
							<li>Выйдите из аккаунта пользователя</li>
							<li>Нажмите "Восстановить пароль"</li>
							<li>Введите ваш почтовый ящик</li>
							<li>Следуйте инструкциям сайта</li>
						</ul>
					</dd>

					<dt id="how_to_change_your_mailbox">
						<a href="#how_to_change_your_mailbox">Как изменить почтовый ящик?</a>
					</dt>

					<dd>
						<ul>
							<li>В меню пользователя найдите пункт "Мои настройки"</li>
							<li>Нажмите "Почтовые ящики"</li>
							<li>Добавьте новый почтовый ящик</li>
							<li>Подтвердите новый почтовый ящик</li>
							<li>Нажмите в меню нового почтового ящика на "Использовать для получения уведомлений" и "Использовать для восстановления"</li>
							<li>Можете удалить старый почтовый ящик</li>
						</ul>
					</dd>

					<dt id="how_do_i_upload_an_avatar_or_photo">
						<a href="#how_do_i_upload_an_avatar_or_photo">Как мне загрузить аватар или фото?</a>
					</dt>

					<dd>
						<ul>
							@guest
								<li>Создайте
									<a href="#what_is_a_user_account" class="text-info">аккаунт пользователя</a>
									или войдите в ваш аккаунт
								</li>
							@endguest

							<li>Перейдите на вашу страницу пользователя</li>
							<li>Нажмите на кнопку
								<button class="btn btn-light" type="button">
									<i class="fas fa-ellipsis-h"></i>
								</button>
								рядом с вашем именем.
								В открывшемся меню нажмите "Редактировать профиль"
							</li>
							<li>Нажмите на кнопку "Выберите файл" и выберите изображение</li>
							<li>Нажмите "Загрузить"</li>
						</ul>
					</dd>

				</div>

				<h4 id="comments" class="mb-3">
					<a href="#comments">Комментарии</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="how_long_can_i_edit_a_comment_to_a_book">
						<a href="#how_long_can_i_edit_a_comment_to_a_book">В течении какого времени можно отредактировать комментарий к книге?</a>
					</dt>

					<dd>
						Комментарий можно отредактировать в течении 7 дней со дня публикации
					</dd>

				</div>

				<h4 id="appearance_of_the_site" class="mb-3">
					<a href="#appearance_of_the_site">Внешний вид сайта</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="how_to_change_the_site_font">
						<a href="#how_to_change_the_site_font">Мне не нравится шрифт сайта. Можно ли его поменять?</a>
					</dt>

					<dd>
						Да, конечно.
						<ul>
							<li>В меню пользователя найдите пункт "Мои настройки"</li>
							<li>Нажмите "Внешний вид сайта"</li>
							<li>На этой странице вы можете поменять размер или тип шрифта</li>
							<li>Не забудьте нажать "Сохранить"</li>
						</ul>
					</dd>

					<dt id="can_i_switch_from_the_mobile_version_of_the_site_to_the_full_version_of_the_site">
						<a href="#can_i_switch_from_the_mobile_version_of_the_site_to_the_full_version_of_the_site">
							Можно ли перейти с мобильной версии сайта на полную версию сайта?
						</a>
					</dt>

					<dd>
						Возможно, что да. Зависит от вашей версии браузера. В Гугл Хроме полная версия сайта включается в меню браузера: нужно открыть меню и
						установить галочку рядом с "Версия для ПК". Попробуйте поискать в интернете как включить полную версию для вашего браузера.
					</dd>

				</div>

				<h4 id="sequences" class="mb-3">
					<a href="#sequences">Серии книг</a>
				</h4>

				<div class="ml-3 mb-4">

					<dt id="where_to_find_your_favorite_sequences">
						<a href="#where_to_find_your_favorite_sequences">Где найти мои избранные серии?</a>
					</dt>

					<dd>
						<ul>
							<li>Откройте <a href="#what_is_the_user_menu" class="text-info">меню пользователя</a></li>
							<li>Нажмите "Мои серии"</li>
							<li>В открывшемся меню нажмите "Избранное"</li>
						</ul>
					</dd>

				</div>

			</div>
		</div>

		@if ($errors->question->any())
			<div class="errors alert alert-danger">
				<ul class="mb-0">
					@foreach ($errors->question->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<div id="user_question" class="card">
			<div class="card-header">
				{{ __('If you can not find an answer to your question, please contact us:') }}
			</div>
			<div class="card-body">

				@guest
					<div class="alert alert-info">
						{{ __('Please register or log in to your account') }}
					</div>
				@endguest

				@can('create_topic', $forum)

					<form role="form" method="post"
						  action="{{ route('questions.store') }}">

						@csrf

						<div class="form-group">
							<input name="name" type="text" value="{{ old('name') }}" required minlength="2" maxlength="200"
								   class="form-control" placeholder="{{ __('Write a short question title in this field') }}"/>
						</div>

						<div class="form-group">
							<label for="bb_text" class="col-form-label">
								{{ __('question.bb_text') }}: ({{ __('At least 30 characters') }})
							</label>
							<textarea id="bb_text" class="sceditor form-control {{ $errors->has('bb_text') ? ' is-invalid' : '' }}"
									  rows="{{ config('litlife.textarea_rows') }}" name="bb_text" required minlength="30"
									  placeholder="{{ __('Write your question in this field') }}">{{ old('bb_text') }}</textarea>
						</div>

						<div class="form-group form-check">
							<input type="hidden" name="notify_about_responses" value="0"/>
							<input type="checkbox" name="notify_about_responses" value="1"
								   class="form-check-input" id="notify_about_responses" checked="checked">
							<label class="form-check-label" for="notify_about_responses">
								{{ __('question.notify_about_responses') }}
							</label>
						</div>

						<button type="submit" class="btn btn-primary">{{ __('Ask a question') }}</button>
					</form>
				@endcan
			</div>
		</div>
	</div>

@endsection