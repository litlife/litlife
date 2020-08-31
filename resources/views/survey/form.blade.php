@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/survey.create.js', config('litlife.assets_path')) }}" type="text/javascript"></script>
@endpush

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

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form action="{{ $action }}" method="post" enctype="multipart/form-data">

				@csrf

				<div class="form-group">
					<label for="exampleInputEmail1">{{ __('survey.do_you_read_books_or_download_them') }}</label>
					<select name="do_you_read_books_or_download_them" class="custom-select">
						<option disabled selected>{{ __('survey.please_select_an_option') }}</option>
						<option value="only_read_online">{{ __('survey.do_you_read_books_or_download_them_options.only_read_online') }}</option>
						<option value="only_download_books">{{ __('survey.do_you_read_books_or_download_them_options.only_download_books') }}</option>
						<option value="download_and_read_online">{{ __('survey.do_you_read_books_or_download_them_options.download_and_read_online') }}</option>
					</select>
				</div>

				<fieldset class="form-group">
					<legend class="col-form-label">{{ __('survey.what_file_formats_do_you_download') }}</legend>
					<div>
						@foreach (['fb2', 'epub', 'docx', 'mobi', 'odt', 'rtf', 'txt', 'doc', 'mp3', 'ogg', 'pdf', 'djvu'] as $extension)
							<div class="form-check">
								<input name="what_file_formats_do_you_download[]" class="form-check-input" type="checkbox" name="gridRadios"
									   id="gridRadios_{{ $extension }}" value="{{$extension }}">
								<label class="form-check-label" for="gridRadios_{{ $extension }}">
									{{ $extension }}
								</label>
							</div>
						@endforeach
					</div>
				</fieldset>

				<div class="form-group">
					<textarea name="how_improve_download_book_files"
							  placeholder="{{ __('survey.how_improve_download_book_files') }}"
							  class="form-control autogrow">{{ old('how_improve_download_book_files') }}</textarea>
				</div>

				<div>
					<div class="form-group">
						<label for="exampleInputEmail1">{{ __('survey.how_do_you_rate_the_convenience_of_reading_books_online') }}</label>
						<select name="how_do_you_rate_the_convenience_of_reading_books_online" class="custom-select">
							<option disabled selected>{{ __('survey.please_select_an_option') }}</option>
							<option value="5">{{ __('survey.scale_options.5') }}</option>
							<option value="4">{{ __('survey.scale_options.4') }}</option>
							<option value="3">{{ __('survey.scale_options.3') }}</option>
							<option value="2">{{ __('survey.scale_options.2') }}</option>
							<option value="1">{{ __('survey.scale_options.1') }}</option>
						</select>
					</div>

					<div class="form-group">
						<textarea name="how_to_improve_the_convenience_of_reading_books_online"
								  placeholder="{{ __('survey.how_to_improve_the_convenience_of_reading_books_online') }}"
								  class="form-control autogrow">{{ old('how_to_improve_the_convenience_of_reading_books_online') }}</textarea>
					</div>
				</div>

				<div>
					<div class="form-group">
						<label for="exampleInputEmail1">{{ __('survey.how_do_you_rate_the_convenience_and_functionality_of_search') }}</label>
						<select class="custom-select"
								name="how_do_you_rate_the_convenience_and_functionality_of_search">
							<option disabled selected>{{ __('survey.please_select_an_option') }}</option>
							<option value="5">{{ __('survey.scale_options.5') }}</option>
							<option value="4">{{ __('survey.scale_options.4') }}</option>
							<option value="3">{{ __('survey.scale_options.3') }}</option>
							<option value="2">{{ __('survey.scale_options.2') }}</option>
							<option value="1">{{ __('survey.scale_options.1') }}</option>
						</select>
					</div>

					<div class="form-group">
						<textarea name="how_to_improve_the_convenience_of_searching_for_books"
								  placeholder="{{ __('survey.how_to_improve_the_convenience_of_searching_for_books') }}"
								  class="form-control autogrow">{{ old('how_to_improve_the_convenience_of_searching_for_books') }}</textarea>
					</div>
				</div>

				<div class="mb-4">
					<div class="form-group">
						<label for="exampleInputEmail1">{{ __('survey.how_do_you_rate_the_site_design') }}</label>
						<select name="how_do_you_rate_the_site_design"
								class="custom-select">
							<option disabled selected>{{ __('survey.please_select_an_option') }}</option>
							<option value="5">{{ __('survey.scale_options.5') }}</option>
							<option value="4">{{ __('survey.scale_options.4') }}</option>
							<option value="3">{{ __('survey.scale_options.3') }}</option>
							<option value="2">{{ __('survey.scale_options.2') }}</option>
							<option value="1">{{ __('survey.scale_options.1') }}</option>
						</select>
					</div>

					<div class="form-group">
						<textarea name="how_to_improve_the_site_design"
								  placeholder="{{ __('survey.how_to_improve_the_site_design') }}"
								  class="form-control autogrow">{{ old('how_to_improve_the_site_design') }}</textarea>
					</div>
				</div>
				<div class="mb-4">
					<div class="form-group">
						<label for="exampleInputEmail1">{{ __('survey.how_do_you_assess_the_work_of_the_site_administration') }}</label>
						<select name="how_do_you_assess_the_work_of_the_site_administration"
								class="custom-select">
							<option disabled selected>{{ __('survey.please_select_an_option') }}</option>
							<option value="5">{{ __('survey.scale_options.5') }}</option>
							<option value="4">{{ __('survey.scale_options.4') }}</option>
							<option value="3">{{ __('survey.scale_options.3') }}</option>
							<option value="2">{{ __('survey.scale_options.2') }}</option>
							<option value="1">{{ __('survey.scale_options.1') }}</option>
						</select>
					</div>

					<div class="form-group">
						<textarea name="how_improve_the_site_administration"
								  placeholder="{{ __('survey.how_improve_the_site_administration') }}"
								  class="form-control autogrow">{{ old('how_improve_the_site_administration') }}</textarea>
					</div>
				</div>

				<div class="form-group">
					<textarea name="what_do_you_like_on_the_site"
							  placeholder="{{ __('survey.what_do_you_like_on_the_site') }}"
							  class="form-control autogrow">{{ old('what_do_you_like_on_the_site') }}</textarea>
				</div>

				<div class="form-group">
					<textarea name="what_you_dont_like_about_the_site"
							  placeholder="{{ __('survey.what_you_dont_like_about_the_site') }}"
							  class="form-control autogrow">{{ old('what_you_dont_like_about_the_site') }}</textarea>
				</div>

				<div class="form-group">
					<textarea name="what_you_need_on_our_site"
							  placeholder="{{ __('survey.what_you_need_on_our_site') }}"
							  class="form-control autogrow">{{ old('what_you_need_on_our_site') }}</textarea>
				</div>

				<fieldset class="form-group">
					<legend class="col-form-label">{{ __('survey.what_site_features_are_interesting_to_you') }}</legend>
					<div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="what_site_features_are_interesting_to_you[]"
								   id="gridRadios1" value="reading_and_downloading_books">
							<label class="form-check-label" for="gridRadios1">
								{{ __('survey.what_site_features_are_interesting_to_you_options.reading_and_downloading_books') }}
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="what_site_features_are_interesting_to_you[]"
								   id="gridRadios2" value="chat_on_the_forum">
							<label class="form-check-label" for="gridRadios2">
								{{ __('survey.what_site_features_are_interesting_to_you_options.chat_on_the_forum') }}
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="what_site_features_are_interesting_to_you[]"
								   id="gridRadios3" value="search_for_information">
							<label class="form-check-label" for="gridRadios3">
								{{ __('survey.what_site_features_are_interesting_to_you_options.search_for_information') }}
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="what_site_features_are_interesting_to_you[]"
								   id="gridRadios4" value="commenting_and_rating_of_books">
							<label class="form-check-label" for="gridRadios4">
								{{ __('survey.what_site_features_are_interesting_to_you_options.commenting_and_rating_of_books') }}
							</label>
						</div>
					</div>
				</fieldset>

				<button type="submit" class="btn btn-primary">{{ __('survey.send') }}</button>
			</form>

		</div>
	</div>

@endsection
