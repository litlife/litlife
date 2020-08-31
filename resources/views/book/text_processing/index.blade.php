@extends('layouts.app')

@section('content')

	@include ('book.edit_tab')

	@can('createTextProcessing', $book)
		<div class="mb-2">
			<a class="btn btn-primary" href="{{ route('books.text_processings.create', $book) }}">{{ __('common.create') }}</a>
		</div>
	@endcan

	@if ($textProcessings->count())
		<div class="card">
			<div class="card-body">
				<ul class="list-group list-group-flush">
					@foreach ($textProcessings as $textProcessing)
						<li class="list-group-item">
							@if ($textProcessing->remove_bold)
								{{ __('book_text_processing.remove_bold') }}<br/>
							@endif

							@if ($textProcessing->remove_extra_spaces)
								{{ __('book_text_processing.remove_extra_spaces') }}<br/>
							@endif

							@if ($textProcessing->split_into_chapters)
								{{ __('book_text_processing.split_into_chapters') }}<br/>
							@endif

							<small>
								{{ __('book_text_processing.created') }}
								<x-user-name :user="$textProcessing->create_user"/>

								@if ($textProcessing->isStarted())
									{{ __('book_text_processing.started') }}:
									<x-time :time="$textProcessing->started_at"/>
								@elseif ($textProcessing->isCompleted())
									{{ __('book_text_processing.completed') }}:
									<x-time :time="$textProcessing->completed_at"/>
								@else
									{{ __('book_text_processing.created') }}:
									<x-time :time="$textProcessing->created_at"/>
								@endif
							</small>
						</li>
					@endforeach
				</ul>

				@if ($textProcessings->hasPages())
					<div class="mt-2">
						{{ $textProcessings->appends(request()->except(['page', 'ajax']))->links() }}
					</div>
				@endif

			</div>
		</div>
	@else
		<div class="alert alert-info">
			{{ __('book_text_processing.no_text_processing_has_been_created_yet') }}
		</div>
	@endif

@endsection
