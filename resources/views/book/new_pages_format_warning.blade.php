<div class="alert alert-warning" role="alert">

	{{ __('To edit the text of a book follow these steps') }}:

	<ul>
		<li>Перейдите на <a href="{{ route('books.show', $book) }}#files" class="text-info" target="_blank">страницу книги</a></li>
		<li>
			Найдите файл (в списке файлов книги) одного из следующих форматов:
			{{ implode(', ', collect(config('litlife.book_allowed_file_extensions'))->diff(config('litlife.no_need_convert'))->toArray()) }}
		</li>
		<li>Если вы не нашли файл, то
			<a href="{{ route('books.files.create', $book) }}" class="text-info" target="_blank">прикрепите файл</a>
			с текстом книги в формате:
			{{ implode(', ', collect(config('litlife.book_allowed_file_extensions'))->diff(config('litlife.no_need_convert'))->toArray()) }}.<br/>
			Если вы нашли файл подходящего формата, то переходите к следующему шагу.
		</li>
		<li>
			Откройте меню файла книги и нажмите пункт "Сделать источником".
		</li>
		<li>
			Через пару минут из выбранного файла будет создан новый
			<a href="{{ route('faq') }}#what_is_the_text_of_a_book_for_online_reading" class="text-info" target="_blank">текст книги для онлайн чтения</a>.
		</li>
		<li>
			Теперь вы можете
			<a href="{{ route('faq') }}#how_to_edit_the_text_of_a_book" class="text-info" target="_blank">отредактировать текст книги</a>
		</li>
	</ul>

</div>