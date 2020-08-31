@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-12">


			<form role="form" action="{{ route('posts.transfer') }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<div class="row form-group">
					{{ Form::label('ids', __('post.ids').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">

						<select name="posts[]" class="posts form-control select2-multiple" multiple style="width:100%">
							@if (!empty($ids))
								@foreach ($ids as $id)
									<option value="{{ $id }}" selected="selected">{{ $id }}</option>
								@endforeach
							@endif
						</select>

						<script type="text/javascript">

							document.addEventListener('DOMContentLoaded', function () {

								$(".posts").select2({
									width: 'style',
									tags: true,
									selectOnClose: true,
									closeOnSelect: false,
									tokenSeparators: [',', ' '],
									escapeMarkup: function (markup) {
										return markup;
									},
									minimumInputLength: 1,
									templateResult: function formatRepo(repo) {
										if (repo.loading) return repo.id;

										var markup = "<div >" + repo.id + "</div>";

										return markup;
									},
									createTag: function (params) {
										var term = $.trim(params.term);

										if (term.match(/^(^[0-9]+)$/i) === null)
											return null;

										if (term === '') {
											return null;
										}

										return {
											id: term,
											text: term,
											newTag: true // add additional parameters
										}
									}
								});
							});

						</script>

						@if ($errors->has('posts'))
							<p class="help-block">{{ $errors->first('posts') }}</p>
						@endif


					</div>
				</div>

				<div class="row form-group">
					{{ Form::label('topic_id', __('topic.id'), ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('topic_id', old('topic_id'), ['class' => 'form-control']) }}

						@if ($errors->has('topic_id'))
							<p class="help-block">{{ $errors->first('topic_id') }}</p>
						@endif
					</div>
				</div>

				<div class="row form-group">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary">{{ __('common.move') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>


@endsection