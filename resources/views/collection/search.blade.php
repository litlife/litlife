@push('scripts')
	<script src="{{ mix('js/collections.index.js', config('litlife.assets_path')) }}"></script>
@endpush

<div class="row">

	<div class="col-md-4 order-md-2 order-sm-1 ">
		<div class="card mb-3">
			<div class="card-body">
				<form class="collection-form" role="form" action="{{ Request::url() }}"
					  method="get" enctype="multipart/form-data">

					<div class="form-group">
						<input name="search" class="form-control" type="text" placeholder="{{ __('collection.search') }}"
							   value="{{ $input['search'] ?? ''  }}">
					</div>

					<button class="btn btn-outline-primary d-sm-none btn-sm" type="button" data-toggle="collapse"
							data-target="#more_filters"
							aria-expanded="false"
							aria-controls="more_filters">
						{{ __('common.search_filters') }}
					</button>

					<div id="more_filters" class="collapse dont-collapse-xs mt-3">

						<div class="form-group">
							<label>{{ __('common.order') }}:</label>
							<select class="form-control" name="order">
								@foreach ($order_array as $code => $function)
									<option value="{{ $code }}"
											@if ($code == $input['order']) selected="selected" @endif > {{ __('collection.sorting.'.$code.'') }}</option>
								@endforeach
							</select>
						</div>

						<button type="submit" class="btn btn-primary">{{ __('common.seek') }}</button>

					</div>

				</form>
			</div>
		</div>
	</div>

	<div class="col-md-8  order-md-1 order-sm-2" role="main">
		<div class="list">
			@include("collection.list")
		</div>
	</div>

</div>