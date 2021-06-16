@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/book-keyword.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include ('book.edit_tab')

	@if(count($keywords) > 0)
		<div class="card mb-3">
			<div class="card-body">

				@include('book.keyword.table')
			</div>
		</div>
	@else

		<div class="alert alert-info">{{ __('keyword.nothing_found') }}</div>

	@endif


	@can('addKeywords', $book)
		<div class="card">
			<div class="card-body">


				<form class="keywords-form" role="form" method="POST"
					  action="{{ route('books.keywords.store', compact('book')) }}">

					@csrf

					<div class="form-group{{ $errors->has('keywords') ? ' has-error' : '' }}">

						<select name="keywords[]" data-placeholder="{{ __('common.enter_name_or_id') }}"
								class="keywords form-control select2-multiple" multiple style="width:100%"></select>

						@if ($errors->has('keywords'))
							<p class="help-block">{{ $errors->first('keywords') }}</p>
						@endif

					</div>

					<button type="submit" class="btn btn-light">{{ __('common.attach') }}</button>

					<a target="_blank" class="btn btn-primary" href="{{ route('text_block.keywords_helper') }}">
						{{ __('book.list_of_all_keywords') }}
					</a>

				</form>

			</div>
		</div>
	@endcan

@endsection
