@extends('layouts.app')

@push('css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.css">
@endpush

@push('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.js"></script>
@endpush

@section('content')

	@if (count($errors->photo) > 0)
		<div class="alert alert-danger mb-2">
			<ul>
				@foreach ($errors->photo->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@elseif (session('photo.success'))
		<div class="alert alert-success alert-dismissable mb-2">
			{{ session('photo.success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<div class="card mb-2">
		<div class="card-body">
			<form id="upload_avatar_form" class="mb-3" role="form"
				  action="{{ route('users.photos.store', compact('user')) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="row form-group{{ $errors->photo->has('file') ? ' has-error' : '' }}">
					<label for="file" class="col-md-3 col-lg-2 col-form-label">{{ __('user.photo') }}:</label>
					<div class="col-md-9 col-lg-10">
						<div class="mb-3">
							<x-user-avatar :user="$user" width="180" height="180"
										   class="img-fluid rounded avatar pointer lazyload"
										   href="{{ route('users.avatar.show', $user) }}"/>
						</div>

						<div class="">
							<input size="{{ ByteUnits\Metric::bytes(config('litlife.max_image_size'))->numberOfBytes() }}"
								   type="file" name="file"/>
						</div>
						<small class="form-text text-muted">
							{{ __('common.max_size') }}
							: {{ ByteUnits\Metric::kilobytes(config('litlife.max_image_size'))->format() }}
						</small>
					</div>
				</div>

				<div class="row form-group{{ $errors->photo->has('file') ? ' has-error' : '' }}">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button class="btn btn-primary" type="submit">{{ __('common.upload') }}</button>

						@can('remove_photo', $user)
							<a href="{{ route('users.photos.delete', ['user' => $user, 'photo' => $user->avatar]) }}"
							   class="btn btn-light">
								{{ __('common.delete') }}
							</a>
					@endcan

					<!--
                        @if (!empty($user->avatar->url))
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
{{ __('user.change_miniature') }}
								</button>

								<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
									 aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">{{ __('user.change_miniature') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted"><small>{{ __('user.change_miniature_helper') }}</small></p>
                                        <div class="img-container mb-2">
                                            <img data-src="{{ $user->avatar->url }}" id="image" style="max-width: 100%;">
                                        </div>

                                        <div class="preview" style="overflow: hidden; width: 200px; height: 200px;"></div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.close') }}</button>
                                        <button type="button" class="btn btn-primary save">{{ __('common.save') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif


							<script type="text/javascript">

								document.addEventListener('DOMContentLoaded', function () {

									$('#myModal').on('shown.bs.modal', function (e) {

										let modal = $('#myModal');
										var $image = $('#myModal').find('#image');
										$image.attr('src', $image.data('src'));
										var $preview = $('#myModal').find('.preview');
										let $btn_save = $('#myModal').find('.save');

										var MIN_WIDTH = 200,
											MIN_HEIGHT = 200;

										$image.cropper({
											aspectRatio: 1 / 1,
											viewMode: 2,
											background: false,
											center: false,
											autoCropArea: 1,
											movable: false,
											rotatable: false,
											scalable: false,
											zoomable: true,
											preview: $preview,
											crop: function (event) {

												var data = $(this).cropper('getData');

												if (data.width < MIN_WIDTH || data.height < MIN_HEIGHT) {
													data.width = MIN_WIDTH;
													data.height = MIN_HEIGHT;

													$(this).cropper('setData', data);
												}
											},
											zoom: function (event) {

												var data = $(this).cropper('getData');

												if (data.width < MIN_WIDTH || data.height < MIN_HEIGHT) {
													data.width = MIN_WIDTH;
													data.height = MIN_HEIGHT;

													$(this).cropper('setData', data);
												}
											},
										});

										// Get the Cropper.js instance after initialized
										var cropper = $image.data('cropper');

										$btn_save.click(function () {

											modal.addClass("loading-cap");

											let data = cropper.getData('width');

											$.ajax({
												url: "/users/{{ $user->id }}/set_miniature?width=" + data.width
                                                + "&height=" + data.height
                                                + "&x=" + data.x
                                                + "&y=" + data.y,
                                            context: document.body
                                        }).done(function() {
                                            location.reload();
                                            modal.removeClass("loading-cap");

                                        }).fail(function() {
                                            modal.removeClass("loading-cap");
                                        });
                                    });

                                }).on('hidden.bs.modal', function () {

                                });


                            });

                        </script>

                        -->
					</div>
				</div>

			</form>
		</div>
	</div>
	@if (count($errors->user) > 0)
		<div class="alert alert-danger mb-2">
			<ul>
				@foreach ($errors->user->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@elseif (session('success'))
		<div class="alert alert-success alert-dismissable mb-2">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif
	<div class="card">
		<div class="card-body">
			<form id="profile_edit_form" action="{{ route('users.update', $user) }}" method="post"
				  enctype="multipart/form-data">

				@csrf
				@method('patch')


				<div class="row form-group">
					<label for="nick" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.nick') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="nick" name="nick" type="text"
							   class="form-control{{ $errors->user->has('nick') ? ' is-invalid' : '' }}"
							   value="{{ old('nick') ?? $user->nick }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="first_name" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.first_name') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="first_name" name="first_name" type="text"
							   class="form-control{{ $errors->user->has('first_name') ? ' is-invalid' : '' }}"
							   value="{{ old('first_name') ?? $user->first_name }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="last_name" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.last_name') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="last_name" name="last_name" type="text"
							   class="form-control{{ $errors->user->has('last_name') ? ' is-invalid' : '' }}"
							   value="{{ old('last_name') ?? $user->last_name }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="middle_name" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.middle_name') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="middle_name" name="middle_name" type="text"
							   class="form-control{{ $errors->user->has('middle_name') ? ' is-invalid' : '' }}"
							   value="{{ old('middle_name') ?? $user->middle_name }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="name_show_type" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.name_show_type') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<select id="name_show_type" name="name_show_type"
								class="form-control{{ $errors->user->has('name_show_type') ? ' is-invalid' : '' }}">
							@foreach (\App\Enums\UserNameShowType::getKeys() as $key)
								@if ($key == (old('name_show_type') ?: $user->name_show_type))
									<option value="{{ $key }}"
											selected>{{ __('user.name_show_type_array.'.$key) }}</option>
								@else
									<option value="{{ $key }}">{{ __('user.name_show_type_array.'.$key) }}</option>
								@endif
							@endforeach
						</select>
					</div>
				</div>

				<div class="row form-group">
					<label for="gender" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.gender') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<select id="gender" name="gender"
								class="form-control{{ $errors->user->has('gender') ? ' is-invalid' : '' }}">
							@foreach(__("gender") as $key => $value)
								<option value="{{ $key }}"
										@if ($key == (old('gender') ?? $user->gender)) selected @endif
										@if ($key == 'unknown') disabled @endif>
									{{ $value }}
								</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="row form-group">
					<label for="born_date" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.born_date') }}
					</label>
					<div class="col-md-9 col-lg-10">

						<select name="born_day" style="width:5rem;"
								class="form-control d-inline-block {{ $errors->user->has('born_day') ? ' is-invalid' : '' }}">
							<option value="">{{ __('date.select_day') }}</option>
							@for ($a = 1; $a <= 31; $a++)
								<option value="{{ $a }}"
										@if ((old('born_day') ?? optional($user->born_date)->day) == $a) selected @endif>
									{{ $a }}
								</option>
							@endfor
						</select>

						<select name="born_month" style="width:8rem;"
								class="form-control d-inline-block {{ $errors->user->has('born_month') ? ' is-invalid' : '' }}">
							<option value="">{{ __('date.select_month') }}</option>
							@for ($a = 1; $a <= 12; $a++)
								<option value="{{ $a }}"
										@if ((old('born_month') ?? optional($user->born_date)->month) == $a) selected @endif>
									{{ __('date.month.'.$a) }}
								</option>
							@endfor
						</select>

						<select name="born_year" style="width:7rem;"
								class="form-control d-inline-block {{ $errors->user->has('born_year') ? ' is-invalid' : '' }}">
							<option value="">{{ __('date.select_year') }}</option>
							@for ($a = (date('Y') - 8); $a >= 1900; $a--)
								<option value="{{ $a }}"
										@if ((old('born_year') ?? optional($user->born_date)->year)  == $a) selected @endif>
									{{ $a }}
								</option>
							@endfor
						</select>

					</div>
				</div>

				<div class="row form-group">
					<label for="born_date_show" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.born_date_show') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<select id="born_date_show"
								class="form-control{{ $errors->user->has('born_date_show') ? ' is-invalid' : '' }}"
								name="born_date_show">
							@foreach(__("user.born_date_show_choices") as $key => $value)
								<option value="{{ $key }}"
										@if ($key == (old('born_date_show') ?? $user->born_date_show)) selected @endif>
									{{ $value }}
								</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="row form-group">
					<label for="about_self" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.about_self') }}
					</label>
					<div class="col-md-9 col-lg-10">
                <textarea id="about_self"
						  class="form-control{{ $errors->user->has('about_self') ? ' is-invalid' : '' }}"
						  name="data[about_self]"
						  rows="3">{{ old('about_self') ?? $user->data->about_self }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<label for="i_love" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.i_love') }}
					</label>
					<div class="col-md-9 col-lg-10">
                <textarea id="i_love" class="form-control{{ $errors->user->has('i_love') ? ' is-invalid' : '' }}"
						  name="data[i_love]"
						  rows="3">{{ old('i_love') ?? $user->data->i_love }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<label for="i_hate" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.i_hate') }}
					</label>
					<div class="col-md-9 col-lg-10">
                <textarea id="i_hate" class="form-control{{ $errors->user->has('i_hate') ? ' is-invalid' : '' }}"
						  name="data[i_hate]"
						  rows="3">{{ old('i_hate') ?? $user->data->i_hate }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<label for="favorite_authors" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.favorite_authors') }}
					</label>
					<div class="col-md-9 col-lg-10">
                <textarea id="favorite_authors"
						  class="form-control{{ $errors->user->has('favorite_authors') ? ' is-invalid' : '' }}"
						  name="data[favorite_authors]"
						  rows="3">{{ old('favorite_authors') ?? $user->data->favorite_authors }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<label for="favorite_genres" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.favorite_genres') }}
					</label>
					<div class="col-md-9 col-lg-10">
                <textarea id="favorite_genres"
						  class="form-control{{ $errors->user->has('favorite_genres') ? ' is-invalid' : '' }}"
						  name="data[favorite_genres]"
						  rows="3">{{ old('favorite_genres') ?? $user->data->favorite_genres }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<label for="favorite_music" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.favorite_music') }}
					</label>
					<div class="col-md-9 col-lg-10">
                <textarea id="favorite_music"
						  class="form-control{{ $errors->user->has('favorite_music') ? ' is-invalid' : '' }}"
						  name="data[favorite_music]"
						  rows="3">{{ old('favorite_music') ?? $user->data->favorite_music }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<label for="favorite_quote" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.favorite_quote') }}
					</label>
					<div class="col-md-9 col-lg-10">
                <textarea id="favorite_quote"
						  class="form-control{{ $errors->user->has('favorite_quote') ? ' is-invalid' : '' }}"
						  name="data[favorite_quote]"
						  rows="3">{{ old('favorite_quote') ?? $user->data->favorite_quote }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>

@endsection