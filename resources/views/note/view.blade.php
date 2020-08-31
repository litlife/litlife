@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/books.sections.show.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@if ($section->trashed())
		<div class="row mb-3">
			<div class="col-12 btn-margin-bottom-1">

				<h5>{{ __('note.deleted') }}</h5>

				@can('restore', $section)
					<a href="{{ route('books.sections.delete', ['book' => $book, 'section' => $section->inner_id]) }}"
					   class="btn btn-light">
						{{ __('common.restore') }}
					</a>
				@endcan

			</div>
		</div>
	@else
		<div class="row mb-3">
			<div class="col-12 btn-margin-bottom-1">
				@can('update', $section)
					@if ($section->isSection())
						<a href="{{ route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]) }}"
						   class="btn btn-light">
							{{ __('common.edit') }}
						</a>
					@else
						<a href="{{ route('books.notes.edit', ['book' => $book, 'note' => $section->inner_id]) }}"
						   class="btn btn-light">
							{{ __('common.edit') }}
						</a>
					@endif
				@endcan

				@if (auth()->check())
					<a href="{{ route('users.settings.read_style', auth()->user()) }}" class="btn btn-light" target="_blank">
						{{ __('common.change_read_style') }}
					</a>
				@endif

			</div>
		</div>

		<div class="card mb-3">
			<div class="card-body p-sm-4 p-md-5">
				<div class="imgs-fluid cke_editable">
					@yield('text')
				</div>
			</div>
		</div>

		@include('read_style_css')

		<div class="row">
			<div class="col-12 text-center">

				<a class="btn btn-light" href="javascript:history.back()">
					{{ __('common.go_back') }}
				</a>

			</div>
		</div>
	@endif
@endsection