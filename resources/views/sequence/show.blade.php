@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/sequences.show.js', config('litlife.assets_path')) }}"></script>
@endpush

@push ('css')

@endpush

@section('content')

	@if ($sequence->isMerged())
		<div class="alert alert-warning" role="alert">
			{{ __('sequence.merged_with') }} @include('sequence.name', ['sequence' => $sequence->merged_sequence]).
			{{ __('sequence.merged_by') }}
			<x-user-name :user="$sequence->merge_user"/>
			<x-time :time="$sequence->merged_at"/>
		</div>
	@endif

	<div class="sequence">
		<div class="card mb-3">

			<div class="card-header">
				@if ($sequence->trashed())
					<h5>{{ __('sequence.deleted') }}</h5>
				@else
					<h2 class="h5">{{ __('model.sequence') }}
						: @include('sequence.name', ['sequence' => $sequence, 'href_disable' => true])</h2>
				@endif
			</div>

			<div class="card-body">

				<div class="card-text mb-3">

					@include('like.item', ['item' => $sequence, 'like' => pos($sequence->likes) ?: null, 'likeable_type' => 'sequence', 'likeable_id' => $sequence->id])

					@include('user_library_button', ['item' => $sequence,
					'user_library' => pos($sequence->library_users) ?: null, 'type' => 'sequence',
					'id' => $sequence->id, 'count' => $sequence->added_to_favorites_count])

					<div class="btn-group" data-toggle="tooltip" data-placement="top"
						 title="{{ __('common.open_actions') }}">
						<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-ellipsis-h"></i>
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							@can ('update', $sequence)
								<a class="dropdown-item text-lowercase" href="{{ route('sequences.edit', $sequence) }}">
									{{ __('common.edit') }}
								</a>
							@endcan

							@can ('delete', $sequence)
								<a class="dropdown-item text-lowercase" href="{{ route('sequences.delete', $sequence) }}">
									{{ __('common.delete') }}
								</a>
							@elsecan('restore', $sequence)
								<a class="dropdown-item text-lowercase" href="{{ route('sequences.delete', $sequence) }}">
									{{ __('common.restore') }}
								</a>
							@endcan

							@can ('book_numbers_edit', $sequence)
								<a class="dropdown-item text-lowercase" href="{{ route('sequences.book_numbers', $sequence) }}">
									{{ __('sequence.change_books_numbers') }}
								</a>
							@endcan

							@can ('merge', $sequence)
								<a class="dropdown-item text-lowercase" href="{{ route('sequences.merge_form', $sequence) }}">
									{{ __('common.merge') }}
								</a>
							@endcan

							@can ('unmerge', $sequence)
								<a class="dropdown-item text-lowercase" href="{{ route('sequences.unmerge', $sequence) }}">
									{{ __('common.detach') }}
								</a>
							@endcan

							@can ('watch_activity_logs', $sequence)
								<a class="dropdown-item text-lowercase"
								   href="{{ route('sequences.activity_logs', $sequence) }}">
									{{ __('sequence.logs') }}
								</a>
							@endcan

						</div>
					</div>
				</div>

				<p class="card-text text-muted">
					ID: {{ $sequence->id }}

					@if ($sequence->added_to_favorites_count > 0)
						&nbsp;
						{{ trans_choice('sequence.added_to_favorites_times', $sequence->added_to_favorites_count, ['count' => $sequence->added_to_favorites_count]) }}
					@endif
				</p>

			</div>

			@if (!$sequence->trashed() and !empty($sequence->description))

				<div class="card-footer" id="description">
					{!! $sequence->description !!}
					<div class="btn btn-light expand-biography" style="display: none">{{ __('common.expand') }}</div>
					<div class="btn btn-light compress-biography" style="display: none">{{ __('common.compress') }}</div>
				</div>

			@endif

		</div>

		@if (!$sequence->trashed() and !$sequence->isMerged())

			<div class="card">

				<div class="card-header">

					<ul class="nav nav-tabs card-header-tabs" id="sequenceTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" href="#books" data-toggle="tab">
								{{ trans_choice('book.books', 2) }} <span class="badge">{{ $books->count() }}</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#comments" data-toggle="tab">
								{{ __('sequence.book_comments') }} <span class="badge"></span>
							</a>
						</li>
					</ul>

				</div>

				<div class="card-body p-2">

					<div class="tab-content" id="sequence_tabContent">
						<div class="tab-pane fade show active" id="books" role="tabpanel">
							@include('sequence.books', compact('books'))
						</div>
						<div class="tab-pane fade" id="comments" role="tabpanel"></div>
					</div>
				</div>
			</div>

		@endif
	</div>


@endsection
