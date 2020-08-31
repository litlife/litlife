<div class="item card" data-id="{{ $award->id }}">
	<div class="card-body">
		<h5 class="title card-title">
			<a href="{{ route('awards.show', compact('award')) }}">
				{{ $award->title }}
			</a>
		</h5>
		<p class="description card-text">{{ $award->description }}</p>
		<p class="description card-text">
			<a href="{{ route('books', ['award' => $award->title]) }}">{{ __('book.all_books') }}</a>
		</p>
	</div>

	<div class="card-footer d-flex justify-content-between align-items-center">
		@if (!empty($award->created_at))
			<small class="text-muted">{{ __('award.created_at') }}
				<x-time :time="$award->created_at"/>
			</small>
		@endif
		<div class="btn-group dropdown">
			<button class="btn btn-light dropdown-toggle" type="button" id="award_{{ $award->id }}" data-toggle="dropdown"
					aria-haspopup="true"
					aria-expanded="false">
				<i class="fas fa-ellipsis-h"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="award_{{ $award->id }}">
				<a class="delete text-lowercase dropdown-item pointer" disabled="disabled"
				   data-loading-text="{{ __('common.deleting') }}..."
				   @cannot ('delete', $award) style="display:none;"@endcannot>
					{{ __('common.delete') }}
				</a>

				<a class="restore text-lowercase dropdown-item pointer" disabled="disabled"
				   data-loading-text="{{ __('common.restoring') }}"
				   @cannot ('restore', $award) style="display:none;"@endcannot>
					{{ __('common.restore') }}
				</a>

				@can ('update', $award)
					<a class="dropdown-item text-lowercase" href="{{ route('awards.edit', $award) }}">
						{{ __('common.edit') }}
					</a>
				@endcan
			</div>
		</div>
	</div>

</div>
