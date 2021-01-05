@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/sequences.search.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	<div class="sequences-search-container row">
		<div class="col-lg-4 col-md-4 order-md-2 order-sm-1">

			<div class="card mb-3">
				<div class="card-body">

					<form class="sequence-form" role="form" action="{{ Request::url() }}" method="GET">

						<div class="form-group">
							<input name="search" class="form-control" type="text" placeholder="{{ __('sequence.search') }}"
								   value="{{ $input['search'] ?? ''  }}">
						</div>

						<div class="form-group">
							<label>{{ __('common.order') }}: </label>
							<select class="form-control" name="order">
								@foreach ($order_array as $code => $function)
									<option value="{{ $code }}"
										@if ($code == $resource->getInputValue('order')) selected="selected" @endif >
										{{ __('sequence.sorting.'.$code.'') }}</option>
								@endforeach
							</select>
						</div>

						<button type="submit" class="btn btn-primary">{{ __('common.seek') }}</button>

					</form>

				</div>
			</div>
		</div>

		<div class="list col-lg-8 col-md-8 order-md-1 order-sm-2" role="main">

			@include('sequence.list')

		</div>

	</div>

@endsection