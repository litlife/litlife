@extends('note.view')

@section ('text')

	<div @if ($section->getSectionId()) id="{{ $section->getSectionId() }}"
		 @endif class="book_text @if ($book->copy_protection) noselect @endif">

		@foreach ($pages as $page)
			{!! $page->contentHandled !!}
		@endforeach

	</div>

@endsection

