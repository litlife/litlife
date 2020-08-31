@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/search.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	<div class="card">
		<div class="card-body">

			<form id="outter_form" class="d-flex" action="{{ route('search') }}">
				<div class="w-100">
					<input name="query" type="text" required
						   minlength="{{ config('litlife.minimum_number_of_letters_and_numbers') }}"
						   class="form-control" placeholder="{{ __('search.placeholder') }}" value="">
				</div>

				<div class="ml-2 flex-shrink-1">
					<button type="submit" class="btn btn-primary ">
						<i class="fas fa-search"></i>
					</button>
				</div>
			</form>

		</div>
	</div>

	<div class="modal" id="search" tabindex="-1" role="dialog" aria-labelledby="searchModal">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ __('search.result_of_search') }}: <span class="title_query"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-header">
					<form class="w-100" action="{{ route('search') }}">
						<div class="form-group mb-0">
							<input name="query" type="text" required
								   minlength="{{ config('litlife.minimum_number_of_letters_and_numbers') }}"
								   class="form-control" placeholder="{{ __('search.placeholder') }}" value="">
						</div>
					</form>
				</div>

				<div class="result">
					<h1 class="spinner text-center py-5">
						<i class="fas fa-spinner fa-spin"></i>
					</h1>
				</div>
			</div>
		</div>
	</div>

@endsection