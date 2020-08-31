@extends('layouts.app')

@section('content')
	@include ('book/edit_tab')

	@if(count($book_files) > 0)

		@if ($book->need_create_new_files)
			<div class="alert alert-info">
				{{ __('') }}Файлы книг скоро обновятся, пожалуйста подождите.. Перезагрузите страницу, чтобы увидеть
				результат
			</div>
		@endif



		<div class="bookFileList container">
			<ol>
				@foreach ($book_files as $book_file)
					<li bookid="{{ $file->id }}">
						<a href="{{ route('books.files.show', ['book' => $file->book, 'fileName' => $file->name]) }}">{{ $file->name }}</a>
					</li>
				@endforeach
			</ol>
		</div>



	@else
		@if ($book->need_create_new_files)
			<div class="alert alert-info">
				{{ __('') }}Файлы книг скоро появятся, пожалуйста подождите.. Перезагрузите страницу, чтобы увидеть
				результат
			</div>
		@else
			<p class="alert alert-info">
				{{ __('') }}Ни одного файла книги не найдено
			</p>
		@endif
	@endif


@endsection
