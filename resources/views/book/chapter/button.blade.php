@if (!empty($chapters_count))
	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#chaptersList">
		{{ __('page.select_section') }} ({{ $chapters_count }})
	</button>
@endif

@push('body_append')

	<div class="modal" id="chaptersList" tabindex="-1" role="dialog" aria-labelledby="chaptersListTitle"
		 aria-hidden="true" data-href="{{ route('books.sections.list_go_to', ['book' => $book]) }}">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="sectionsListTitle">{{ __('Chapters') }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					{{ __('Loading') }}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary"
							data-dismiss="modal">{{ __('Close') }}</button>
				</div>
			</div>
		</div>
	</div>
@endpush