<div class="authors-search-container row ">

	<div class=" col-md-4 col-lg-4 col-sm-6 order-sm-2 order-xs-1">
		<div class="card mb-3">
			<div class="card-body">

				<form id="author-form" class="author-form " role="form" action="{{ Request::url() }}" method="get">

					<div class="form-group">
						<input name="search" class="form-control" type="text"
							   placeholder="{{ __('author.search') }}"
							   value="{{ $input['search'] ?? ''  }}">
					</div>

					<button class="btn btn-outline-primary d-sm-none btn-sm" type="button" data-toggle="collapse"
							data-target="#more_filters"
							aria-expanded="false"
							aria-controls="more_filters">
						{{ __('common.show_all_search_filters') }}
					</button>

					<div id="more_filters" class="collapse dont-collapse-xs mt-3">

						<div class="form-group">
							<input name="last_name" class="form-control" type="text"
								   placeholder="{{ __('author.last_name') }}"
								   value="{{ $input['last_name'] ?? ''  }}">
						</div>

						<div class="form-group">
							<input name="first_name" class="form-control" type="text"
								   placeholder="{{ __('author.first_name') }}"
								   value="{{ $input['first_name'] ?? ''  }}">
						</div>

						<div class="form-group">
							<input name="middle_name" class="form-control" type="text"
								   placeholder="{{ __('author.middle_name') }}"
								   value="{{ $input['middle_name'] ?? ''  }}">
						</div>

						<div class="form-group">
							<input name="nick" class="form-control" type="text"
								   placeholder="{{ __('author.nickname') }}"
								   value="{{ $input['nick'] ?? ''  }}">
						</div>

						<div class="form-group">
							<select name="gender" class="form-control">
								<option value="">{{ __('author.gender_array.any') }}</option>
								@foreach (App\Enums\Gender::getKeys() as $key)
									<option value="{{ $key }}" @if ($key == $input['gender']) selected @endif>
										{{ __('author.gender_array.'.$key) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							{{ Form::select('Photo', __('author.photo_array'), $input['Photo'], ['class' => 'form-control']) }}
						</div>

						<div class="form-group">
							{{ Form::select('Biography', __('author.biography_array'), $input['Biography'], ['class' => 'form-control']) }}
						</div>

						<div class="form-group">
							<label>{{ __('author.lang') }}:</label>
							<select class="form-control" name="lang">

								<option value=""> -</option>

								@foreach (App\Language::all() as $language)

									<option value="{{ $language->code }}"
											@if ($language->code == $input['lang']) selected="selected" @endif > {{ $language->name }}</option>

								@endforeach

								<option value="not_specified"
										@if ($input['lang'] == 'not_specified') selected="selected" @endif>{{ __('author.lang_not_set') }}</option>

							</select>
						</div>

						<div class="form-group">
							<label>{{ __('common.order') }}: </label>
							<select class="form-control" name="order">
								@foreach ($order_array as $code => $function)
									<option value="{{ $code }}"
											@if ($code == $input['order']) selected="selected" @endif > {{ __('author.sorting.'.$code.'') }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>{{ __('common.view') }}: </label>
							<select class="form-control" name="view">
								@foreach (['gallery', 'table'] as $view)
									<option value="{{ $view }}"
											@if ($view == $input['view']) selected="selected" @endif > {{ __('common.view_types.'.$view) }}</option>
								@endforeach
							</select>
						</div>

						<button type="submit" class="btn btn-primary">{{ __('common.show') }}</button>

					</div>

				</form>

				@can ('merge', App\Author::class)

					<form role="form" class="mt-3" action="{{ route('authors.merge.store') }}" method="get">

						@csrf

						<div class="form-group">
							<label>{{ __('author.chosen') }}: </label>

							<select name="authors[]" id="selected-authors" class="form-control" multiple=""
									style="width:100%"></select>
						</div>

						<div class="form-group mb-0">
							{!! Form::submit(__('common.merge'), ["class" => "btn btn-outline-primary btn-sm"]) !!}
						</div>

					</form>

				@endcan
			</div>
		</div>
	</div>

	<div class="col-md-8 col-lg-8 col-sm-6 list order-sm-1 order-xs-2 " role="main">
		@include($resource->getViewName())
	</div>
</div>