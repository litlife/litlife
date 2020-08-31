@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/books.notes.index.js', config('litlife.assets_path')) }}" type="text/javascript"></script>

@endpush

@push('css')

	<link href="{{ mix('css/notes-list.css', config('litlife.assets_path')) }}" rel="stylesheet"/>

@endpush

@section('content')


	{{--   @include('scripts.jquery-sortable')
	   @include ('book.edit_tab')

	   @if (!$book->isPagesNewFormat())

		   @include('book.new_pages_format_warning')
	   @else
		   <div class="row">
			   <div class="col-12">

				   @if(count($notes) > 0)
					   <ol class="selectable sortable list-group list-group-flush mb-3">
						   @foreach($notes as $note)
							   @include('section.item', ['item' => $note])
						   @endforeach
					   </ol>
				   @else
					   <p class="alert alert-info" style="padding:10px">
						   {{ __('note.nothing_found') }}
					   </p>
				   @endif

			   </div>
		   </div>

		   <!-- <pre><div class="output"></div></pre> -->

		   @can ('update', $book)

			   <a class="btn btn-primary" href="{{ action('NoteController@create', compact('book')) }}">
				   {{ __('common.create') }}
			   </a>

			   @if(count($notes) > 0)
				   <button class="save btn btn-light">
					   {{ __('note.save_position') }}
				   </button>
				   <button class="move_to_sections btn btn-light" style="display:none;">
					   {{ __('note.move_to_section') }}
				   </button>
			   @endif

		   @endcan

	   @endif--}}

@endsection
