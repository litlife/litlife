@extends('layouts.app')

@section('content')
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	<div class="card">
		<div class="card-body">


			<form role="form" method="POST" action="{{ route('author_repeats.store') }}" enctype="multipart/form-data">

				@csrf

				<div class="row form-group{{ $errors->has('authors') ? ' has-error' : '' }}">
					<label for="authors" class="col-md-3 col-lg-2 col-form-label">{{ __('author_repeat.authors') }}</label>
					<div class="col-md-9 col-lg-10">
						<select id="authors" name="authors[]" class="authors form-control select2-multiple" multiple
								style="width:100%">
							@foreach($authors ?? [] as $c => $author)
								<option value="{{ $author->id }}" selected>{{ $author->fullName }}</option>
							@endforeach
						</select>

						<small id="authorsHelp" class="form-text text-muted">{{ __('author_repeat.authors_helper') }}</small>
					</div>
				</div>

				<script type="text/javascript">

					document.addEventListener('DOMContentLoaded', function () {

						$('.authors').select2({
							width: 'style',
							tags: true,
							tokenSeparators: [','],
							ajax: {
								url: "/authors/search",
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

								if (repo.newTag) {
									markup += repo.text + ' - Добавить нового автора';
								} else {
									markup += "<div >";
									markup += repo.fullName;
									markup += " ID: " + repo.id;
									markup += " </div>";
								}

								return markup;
							},
							// отображение результатов в поле select
							templateSelection: function formatRepoSelection(repo) {

								console.log('templateSelection');
								console.log(repo);

								if (repo.id.match(/[0-9]+/i)) {
									var s = '<a href="/authors/' + repo.id + '" target="_blank">';

									if (repo.fullName)
										s += repo.fullName;

									if (repo.text)
										s += repo.text;

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

				<div class="row form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
					<label for="comment" class="col-md-3 col-lg-2 col-form-label">{{ __('author_repeat.comment') }}</label>
					<div class="col-md-9 col-lg-10">
						<textarea id="comment" class="form-control" rows="5" name="comment">{{ old('comment') }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-12 offset-md-2">
						<button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>
					</div>
				</div>

			</form>
		</div>
	</div>
@endsection