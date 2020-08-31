@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/topics.search.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="topic-search-container row">

		<div class="col-md-3 order-md-2 order-sm-1">
			<div class="card">
				<div class="card-body">

					<form role="form" action="{{ Request::url() }}"
						  method="get" enctype="multipart/form-data">

						@csrf

						<div class="form-group">
							<input name="search_str" class="form-control" type="text"
								   placeholder="{{ __('topic.search_str') }}"
								   value="{{ $input['search_str'] ?? ''  }}">
						</div>

						<div class="form-group">
							<label for="order">{{ __('common.order') }}: </label>
							<select id="order" class="form-control" name="order">
								@foreach ($order_array as $code => $function)
									<option value="{{ $code }}" @if ($code == $input['order']) selected="selected" @endif>
										{{ __('topic.sorting.'.$code.'') }}
									</option>
								@endforeach
							</select>
						</div>

						<button type="submit" class="btn btn-primary">{{ __('common.seek') }}</button>

					</form>
				</div>
			</div>
		</div>

		<div class="col-md-9 order-md-1 order-sm-2" role="main">
			<div class="list">
				@include("forum.topic.list")
			</div>
		</div>

	</div>


@endsection