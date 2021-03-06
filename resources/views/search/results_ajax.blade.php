@if (empty($books_count) and empty($authors_count) and empty($sequences_count) and empty($collections_count) and empty($users_count) and empty($topics_count))
	<div class="card">
		<div class="card-body">
			{{ __('search.nothing_found') }}
		</div>
	</div>
@else
	<div class="modal-header">
		@include('search.nav')
	</div>
	<div class="modal-body modal-lg" style="background-color: #EEEEEE;">
		@include('search.body')
	</div>
@endif
