<div class="users-search-container row">

	<div class="col-lg-4 col-md-5 col-sm-6 order-sm-2 order-xs-1 ">

		<div class="card mb-3">
			<div class="card-body">

				<form id="collapse-user-form" class="user-form" action="{{ Request::url() }}" method="GET">

					<div class="form-group">
						<input name="search" class="form-control" type="text" placeholder="{{ __('user.search.search_str') }}"
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
							<input name="last_name" class="form-control" type="text" placeholder="{{ __('user.last_name') }}"
								   value="{{ $input['last_name'] ?? ''  }}">
						</div>

						<div class="form-group">
							<input name="first_name" class="form-control" type="text"
								   placeholder="{{ __('user.first_name') }}"
								   value="{{ $input['first_name'] ?? ''  }}">
						</div>

						<div class="form-group">
							<input name="nick" class="form-control" type="text" placeholder="{{ __('user.nick') }}"
								   value="{{ $input['nick'] ?? ''  }}">
						</div>

						<div class="form-group">
							<input name="email" class="form-control" type="text"
								   placeholder="{{ trans_choice('user.email', 1) }}"
								   value="{{ $input['email'] ?? ''  }}">
						</div>

						<div class="form-group">
							<input name="text_status" class="form-control" type="text"
								   placeholder="{{ __('user.text_status') }}"
								   value="{{ $input['text_status'] ?? ''  }}">
						</div>

						<div class="form-group">
							<div class="form-check">
								{{ Form::checkbox('is_online', '1', $input['is_online'], ['id' => 'is_onlineCheck', 'class' => 'form-check-input']) }}
								<label class="form-check form-check-inline" for="is_onlineCheck">
									{{ __('user.search.is_online') }}
								</label>
							</div>
							<div class="form-check">
								{{ Form::checkbox('with_photo', '1', $input['with_photo'], ['id' => 'with_photoCheck', 'class' => 'form-check-input']) }}
								<label class="form-check form-check-inline" for="with_photoCheck">
									{{ __('user.search.with_photo') }}
								</label>
							</div>
						</div>

						<div class="form-group">
							<select name="gender" class="form-control">
								<option value="">{{ __('user.gender_array.any') }}</option>
								@foreach (App\Enums\Gender::getKeys() as $key)
									<option value="{{ $key }}" @if ($key == $input['gender']) selected @endif>
										{{ __('user.gender_array.'.$key) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							{{ Form::select('group', App\UserGroup::orderBy('id')->show()->limit(100)->get()->pluck('name', 'id')->prepend(' - ', '0'),
							$input['group'], ['class' => 'form-control']) }}
						</div>

						<div class="form-group">
							<label class="form-label">{{ __('user.born_date') }} ( {{ __('common.day') }}
								/ {{ __('common.month') }}
								/ {{ __('common.year') }} )</label>
							<div class="row">
								<div class="col">
									{{ Form::select('birth_day', collect(array_combine(range(1, 31), range(1, 31)))->prepend(' - ', ''), $input['birth_day'] ?? '', ['class' => 'form-control']) }}
								</div>
								<div class="col">
									{{ Form::select('birth_month', collect(__('date.month'))->prepend(' - ', ''), $input['birth_month'] ?? '', ['class' => 'form-control']) }}
								</div>
								<div class="col">
									<input name="birth_year" class="form-control" type="text" maxlength="4"
										   value="{{ $input['birth_year'] ?? ''  }}">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="order">{{ __('common.order') }}: </label>
							<select id="order" name="order" class="form-control">
								@foreach ($order_array as $code => $function)
									<option value="{{ $code }}" @if ($code == $input['order']) selected="selected" @endif>
										{{ __('user.sorting.'.$code.'') }}
									</option>
								@endforeach
							</select>
						</div>

						<button type="submit" class="btn btn-primary">
							{{ __('common.seek') }}
						</button>
					</div>

				</form>

			</div>
		</div>
	</div>

	<div class="col-lg-8 col-md-7 col-sm-6 list order-sm-1 order-xs-2" role="main">
		@include("user.list")
	</div>
</div>