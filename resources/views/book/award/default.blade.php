<div class="item card" data-id="{{ $book_award->id }}" data-award-id="{{ $book_award->award->id }}"
	 data-book-id="{{ $book_award->book_id }}">
	<div class="card-body">
		<h5 class="title card-title">
			<a href="{{ route('awards.show', ['award' => $book_award->award]) }}">
				{{ $book_award->award->title }}
			</a>
		</h5>
		<p class="description card-text">{{ $book_award->award->description }}</p>
		<p class="card-text">{{ $book_award->year }}</p>
	</div>

	<div class="card-footer d-flex justify-content-between align-items-center">
		@if (!empty($book_award->created_at))
			<small
					class="text-muted">{{ __('award.attached') }}
				<x-time :time="$book_award->created_at"/>
			</small>
		@endif
		<div class="btn-group dropdown">
			<button class="btn btn-light dropdown-toggle" type="button" id="award_{{ $book_award->award_id }}"
					data-toggle="dropdown"
					aria-haspopup="true"
					aria-expanded="false">
				<i class="fas fa-ellipsis-h"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="award_{{ $book_award->award_id }}">
				<a class="delete text-lowercase dropdown-item pointer" disabled="disabled"
				   data-loading-text="{{ __('common.deleting') }}..."
				   @cannot ('attachAward', $book) style="display:none;"@endcannot>
					{{ __('common.delete') }}
				</a>
			</div>
		</div>
	</div>

</div>
