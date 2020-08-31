@extends('layouts.app')

@section('content')
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	<div class="card">
		<div class="card-body">

			<form action="{{ route('forums.store', ['forum_group_id' => @$forum_group->id]) }}" method="post">

				@csrf


				<div class="form-group">
					<label for="name" class="col-form-label">
						{{ __('forum.name') }}
					</label>
					<input id="name" name="name" type="text"
						   class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
						   value="{{ old('name') }}">
				</div>

				<div class="form-group">
					<label for="description" class="col-form-label">
						{{ __('forum.description') }}
					</label>
					<textarea id="description" rows="5"
							  class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
							  name="description">{{ old('description') }}</textarea>
				</div>

				<div class="form-group">
					<label for="min_message_count" class="col-form-label">
						{{ __('forum.min_message_count') }}
					</label>
					<input id="min_message_count" name="min_message_count" type="text"
						   class="form-control{{ $errors->has('min_message_count') ? ' is-invalid' : '' }}"
						   value="{{ old('min_message_count') ?? '0' }}">
				</div>

				<div class="form-group">
					<div class="form-check">
						<input name="private" type="hidden" value="0"/>
						<input id="private_check" name="private" type="checkbox"
							   class="form-check-input {{ $errors->has('private') ? ' is-invalid' : '' }}"
							   @if (old('private')) checked="checked" @endif
							   value="1"/>
						<label class="form-check-label" for="private_check">
							{{ __('forum.private') }}
						</label>
					</div>
				</div>

				<div class="form-group">
					<label for="private_users" class="col-form-label">
						{{ __('forum.private_users') }}
					</label>
					<select id="private_users" name="private_users[]"
							class="form-control {{ $errors->has('private_users') ? ' is-invalid' : '' }}"
							multiple="" style="width:100%">
						@if (old('private_users'))

							@foreach (App\User::whereIn('id', old('private_users'))->get() as $user)
								<option value="{{ $user->id }}" selected>{{ $user->userName }} ID: {{ $user->id }}</option>
							@endforeach

						@endif
					</select>

					<script type="text/javascript">

						document.addEventListener('DOMContentLoaded', function () {
							$("#private_users").select2({
								width: 'style',
								tags: true,
								debug: true,
								ajax: {
									url: "/users_search",
									dataType: 'json',
									delay: 100,
									data: function (params) {
										return {
											q: params.term
										};
									},
									processResults: function (data, params) {
										// parse the results into the format expected by Select2
										// since we are using custom formatting functions we do not need to
										// alter the remote JSON data, except to indicate that infinite
										// scrolling can be used
										params.page = params.page || 1;

										return {
											results: data.items,
											pagination: {
												more: (params.page * 30) < data.total_count
											}
										};
									},
									cache: true
								},
								escapeMarkup: function (markup) {
									return markup;
								},
								minimumInputLength: 1,
								templateResult: function formatRepo(repo) {
									if (repo.loading) return repo.text;

									var markup = "<div >" + repo.userName + " ID: " + repo.id + "</div>";

									return markup;
								},
								createTag: function (params) {
									return undefined;
								},
								templateSelection: function formatRepoSelection(repo) {
									return repo.userName || repo.text;
								}
							});
						})

					</script>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>


			</form>
		</div>
	</div>
@endsection