<div class="posts-search-container row">

	<div class="col-lg-3 col-md-4 order-md-2 order-sm-1 ">
		<div class="card mb-3">
			<div class="card-body">

				<button class="btn btn-primary d-md-none mb-3" type="button" data-toggle="collapse"
						data-target="#collapse-post-form"
						aria-expanded="false"
						aria-controls="collapse-post-form">
					{{ __('common.search_filters') }}
				</button>

				<form id="collapse-post-form" class="post-form collapse dont-collapse-sm" role="form"
					  action="{{ Request::url() }}"
					  method="get">

					<div class="form-group">
						<input name="search_str" class="form-control" type="text" placeholder="{{ __('post.search_str') }}"
							   value="{{ $input['search_str'] ?? ''  }}"/>
					</div>

					<div class="form-group">
						<label for="order">{{ __('common.order') }}: </label>
						<select id="order" class="form-control" name="order">
							@foreach ($order_array as $code => $function)
								<option value="{{ $code }}"
										@if ($code == $input['order']) selected="selected" @endif > {{ __('post.sorting.'.$code.'') }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary">{{ __('common.seek') }}</button>

				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-9 col-md-8 order-md-1 order-sm-2" role="main">
		<div class="list">
			@include("forum.post.list")
		</div>
	</div>

</div>
