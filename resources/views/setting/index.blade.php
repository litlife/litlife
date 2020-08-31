@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/settings.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')


	<div class="card mb-3">
		<div class="card-body">


			<form role="form" method="POST" action="{{ route('settings.save') }}">

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

				<div class=" form-group{{ $errors->has('hide_from_main_page_forums') ? ' has-error' : '' }}">
					<label for="hide_from_main_page_forums"
						   class="col-form-label">{{ __('setting.hide_from_main_page_forums') }}</label>

					<select id="hide_from_main_page_forums" name="hide_from_main_page_forums[]"
							class="forums form-control select2-multiple"
							multiple style="width:100%">
						@if (!empty($forums))
							@foreach ($forums as $forum)
								<option value="{{ $forum->id }}" selected="selected">{{ $forum->name }}</option>
							@endforeach
						@endif
					</select>

				</div>

				<div class="form-group{{ $errors->has('forbidden_words') ? ' has-error' : '' }}">
					<label for="forbidden_words"
						   class="col-form-label">{{ __('setting.forbidden_words') }}</label>

					<textarea id="forbidden_words" name="forbidden_words" style="height:200px;"
							  aria-describedby="forbiddenWordsHelpBlock"
							  class="form-control">{{ implode("\r\n", $settings->value['forbidden_words'] ?? []) }}</textarea>

					<small id="forbiddenWordsHelpBlock" class="form-text">
						{{ __('setting.forbidden_words_helper') }}
					</small>
				</div>

				<div class="form-group{{ $errors->has('check_words_in_comments') ? ' has-error' : '' }}">
					<label for="check_words_in_comments"
						   class="col-form-label">{{ __('setting.check_words_in_comments') }}</label>

					<textarea id="check_words_in_comments" name="check_words_in_comments" style="height:200px;"
							  aria-describedby="checkWordsInCommentsHelpBlock"
							  class="form-control">{{ implode("\r\n", $settings->value['check_words_in_comments'] ?? []) }}</textarea>

					<small id="checkWordsInCommentsHelpBlock" class="form-text">
						{{ __('setting.check_words_in_comments_helper') }}
					</small>
				</div>

				<div class=" form-group{{ $errors->has('genres_books_comments_hide_from_home_page') ? ' has-error' : '' }}">
					<label for="genres_books_comments_hide_from_home_page"
						   class="col-form-label">{{ __('setting.genres_books_comments_hide_from_home_page') }}</label>

					<select id="genres_books_comments_hide_from_home_page" name="genres_books_comments_hide_from_home_page[]"
							class="genres_books_comments_hide_from_home_page form-control select2-multiple"
							multiple style="width:100%">
						@if (!empty($genres))
							@foreach ($genres as $genre)
								<option value="{{ $genre->id }}" selected="selected">{{ $genre->name }}</option>
							@endforeach
						@endif
					</select>

				</div>

				<script type="text/javascript">

					document.addEventListener('DOMContentLoaded', function () {
						$(".forbidden_words").select2({
							width: 'style',
							tags: true,
							tokenSeparators: [' ']
						});
					});


				</script>

				{{--        <div class="form-group{{ $errors->has('time_can_edit_message') ? ' has-error' : '' }}">
							<label for="time_can_edit_message" class="col-md-3 col-lg-2 col-form-label">{{ __('setting.time_can_edit_message') }}</label>
							<div class="col-md-9 col-lg-10">

								<input id="time_can_edit_message" type="text" class="form-control" value="{{ old('time_can_edit_message') }}" />

							</div>
						</div>--}}


				<button type="submit" class="btn btn-primary">
					{{ __('common.save') }}
				</button>


			</form>

		</div>
	</div>

	<a class="btn btn-light mb-3" href="{{ route('admin.refresh_counters') }}">
		{{ __('common.refresh_counters') }}
	</a>


	<div class="card mb-3">
		<div class="card-body">


			@if (session('test_mail_sended'))
				<div class="alert alert-success alert-dismissable">
					{{ __('') }}Письмо отправлено
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				</div>
			@endif

			<form role="form" method="POST" action="{{ route('settings.test_mail') }}">

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

				<div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
					<label for="email" class="col-md-3 col-lg-2 col-form-label">{{ __('setting.email') }}</label>
					<div class="col-md-9 col-lg-10">
                  <textarea id="email" name="email"
							class="form-control">{{ old('email') ?? 'litlife@yandex.ru' }}</textarea>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('test_mail_text') ? ' has-error' : '' }}">
					<label for="test_mail_text"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('setting.test_mail_text') }}</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="test_mail_text" name="test_mail_text"
								  class="form-control">{{ old('test_mail_text') ?? __('').'Текст тестового письма' }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-12 offset-md-2">
						<button type="submit" class="btn btn-primary">{{ __('common.send') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>

	<div class="card mb-3">
		<div class="card-body">
			Максимальный размер загружаемого файла: {{  human_filesize(getMaxUploadNumberBytes()) }}
		</div>
	</div>
@endsection
