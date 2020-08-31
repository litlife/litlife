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


			<form role="form" action="{{ route('topics.merge', compact('topic')) }}"
				  method="post" enctype="multipart/form-data">

				@csrf


				<input type="hidden" value="{{ $topic->id }}"/>

				<div class="form-group">
					<label for="topics" class="col-form-label">
						{{ __('topic.merge_search') }} <span class="text-primary">{{ $topic->name }}</span>:
					</label>

					<select id="topics" name="topics[]" class="topics form-control select2-multiple" multiple
							style="width:100%" aria-expanded=""
							data-placeholder="">
						@if (isset($topics))
							@foreach ($topics as $topic)
								<option value="{{ $topic->id }}" selected="selected">
									{{ $topic->name }}
								</option>
							@endforeach
						@endif
					</select>

					<small class="text-warning">
						{{ __('topic.merge_cant_undone') }}
					</small>
					<script type="text/javascript">

						document.addEventListener('DOMContentLoaded', function () {


							$(".topics").select2({
								width: 'style',
								tags: true,
								ajax: {
									url: "/topics/search",
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
								tokenSeparators: [',', ' '],
								escapeMarkup: function (markup) {
									return markup;
								},
								minimumInputLength: 1,
								templateResult: function formatRepo(repo) {

									console.log('templateResult');
									if (repo.loading) return repo.id;

									console.log(repo);

									var markup = "<div >" + repo.name + " ID: " + repo.id + "</div>";

									return markup;
								},
								createTag: function (params) {
									return undefined;
								},
								templateSelection: function formatRepoSelection(repo) {

									console.log('templateSelection');

									console.log(repo);

									var s = repo.name || repo.text;

									return '' + s + ' ID: ' + repo.id;
								},
							});
						});

					</script>

					@if ($errors->has('topics'))
						<p class="help-block">{{ $errors->first('topics') }}</p>
					@endif

				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.merge') }}</button>


			</form>

		</div>
	</div>
@endsection