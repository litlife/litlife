@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-md-8 order-md-0 order-1">


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
			<div class="card">
				<div class="card-body">

					<form action="{{ route('genre_blacklist.update', $user) }}" role="form" method="POST">

						@csrf

						<div class="row form-group">
							<div class="col-12">
								<select name="genre[]" data-placeholder="{{ __('common.enter_name_or_id') }}"
										class="genres form-control select2-multiple"
										multiple style="width:100%">
									@if (isset($user->genre_blacklist))
										@foreach ($user->genre_blacklist as $genre)
											<option value="{{ $genre->id }}" selected="selected">{{ $genre->name }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>


						<button type="submit" class="btn btn-primary">
							{{ __('common.save') }}
						</button>


					</form>

					<script type="text/javascript">

						document.addEventListener('DOMContentLoaded', function () {

							$(".genres").select2({
								width: 'style',
								tags: true,
								ajax: {
									url: "/genres/search",
									dataType: 'json',
									delay: 100,
									data: function (params) {

										var query = {
											q: params.term,
											page: params.page || 1
										};

										// Query parameters will be ?search=[term]&page=[page]
										return query;
									},
									processResults: function (data, params) {

										console.log('processResults');
										// parse the results into the format expected by Select2
										// since we are using custom formatting functions we do not need to
										// alter the remote JSON data, except to indicate that infinite
										// scrolling can be used
										params.page = params.page || 1;

										return {
											results: data.data,
											pagination: {
												more: (data.next_page_url) ? true : false
											}
										};
									},
									cache: true
								},
								escapeMarkup: function (markup) {
									return markup;
								}, // let our custom formatter work
								minimumInputLength: 1,
								// отображение в выпадающем меню
								templateResult: function formatRepo(repo) {

									console.log('templateResult');
									console.log(repo);

									if (repo.loading) return repo.text;

									var markup = "";

									markup += "<div >";
									markup += repo.name;
									markup += " ID: " + repo.id;
									markup += "</div>";

									return markup;
								},
								// отображение результатов в поле select
								templateSelection: function formatRepoSelection(repo) {

									console.log('templateSelection');
									console.log(repo);

									if (repo.id.match(/[0-9]+/i)) {
										var s = '<a href="/books?genre=' + repo.id + '" target="_blank">';

										s += (repo.text || repo.name);

										s += '</a> ID: ' + repo.id;

										return s;
									} else {
										return repo.text;
									}
								},
								createTag: function (params) {
									return undefined;
								}
							});
						});

					</script>

				</div>
			</div>
		</div>
		<div class="col-md-4  order-md-1 order-0">

			@include ('user.setting.navbar')

		</div>
	</div>


@endsection