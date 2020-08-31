@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/books.awards.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="row">
		@if(count($book_awards) > 0)
			<div class="col-12 awards card-columns">
				@foreach ($book_awards as $book_award)
					@include('book.award.default')
				@endforeach
			</div>
		@else
			<div class="col-12">
				<p class="alert alert-info">{{ __('award.nothing_found') }}</p>
			</div>
		@endif
	</div>

	@can('attachAward', $book)
		<div class="card">
			<div class="card-body">

				<form class="awards-form" role="form" method="POST"
					  action="{{ route('books.awards.store', compact('book')) }}">

					@if ($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					@if (session('success'))
						<div class="alert alert-success alert-dismissable">
							{{ session('success') }}
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						</div>
					@endif

					@csrf

					<div class="row form-group{{ $errors->has('awards') ? ' has-error' : '' }}">
						<label for="award"
							   class="col-md-3 col-lg-2 col-form-label">{{ trans_choice('award.awards', 1) }}</label>
						<div class="col-md-9 col-lg-10">
							<select id="award" name="award" data-placeholder="{{ __('common.enter_name_or_id') }}"
									class="form-control" style="width:100%"></select>
						</div>
					</div>

					<div class="row form-group{{ $errors->has('year') ? ' has-error' : '' }}">
						<label for="year" class="col-md-3 col-lg-2 col-form-label">{{ __('award.year') }}</label>
						<div class="col-md-9 col-lg-10">
							<input id="year" type="text" class="form-control" name="year">
						</div>
					</div>

					<button type="submit" class="btn btn-primary">{{ __('common.attach') }}</button>

				</form>

			</div>
		</div>
	@endcan

@endsection