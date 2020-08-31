@extends('layouts.app')

@section('content')

	@if (isset($surveys) and $surveys->hasPages())
		<div class="row">
			<div class="col-12">
				{{ $surveys->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

	@foreach ($surveys as $survey)

		<div class="card mb-2">
			<div class="card-body">

				<div class="mb-3">
					<x-user-name :user="$survey->create_user"/>
					<x-time :time="$survey->created_at"/>
				</div>

				<dl>
					@if ($survey->do_you_read_books_or_download_them)
						<dt>{{ __('survey.do_you_read_books_or_download_them') }}</dt>
						<dd>{{ __('survey.do_you_read_books_or_download_them_options.'.$survey->do_you_read_books_or_download_them) }}</dd>
					@endif

					@if ($survey->what_file_formats_do_you_download)
						<dt>{{ __('survey.what_file_formats_do_you_download') }}</dt>
						<dd>{{ implode(', ', $survey->what_file_formats_do_you_download) }}</dd>
					@endif

					@if ($survey->how_improve_download_book_files)
						<dt>{{ __('survey.how_improve_download_book_files') }}</dt>
						<dd>{{ $survey->how_improve_download_book_files }}</dd>
					@endif
					@if ($survey->how_do_you_rate_the_convenience_of_reading_books_online)
						<dt>{{ __('survey.how_do_you_rate_the_convenience_of_reading_books_online') }}</dt>
						<dd>{{ __('survey.scale_options.'.$survey->how_do_you_rate_the_convenience_of_reading_books_online) }}</dd>
					@endif

					@if ($survey->how_to_improve_the_convenience_of_reading_books_online)
						<dt>{{ __('survey.how_to_improve_the_convenience_of_reading_books_online') }}</dt>
						<dd>{{ $survey->how_to_improve_the_convenience_of_reading_books_online }}</dd>
					@endif

					@if ($survey->how_do_you_rate_the_convenience_and_functionality_of_search)
						<dt>{{ __('survey.how_do_you_rate_the_convenience_and_functionality_of_search') }}</dt>
						<dd>{{ __('survey.scale_options.'.$survey->how_do_you_rate_the_convenience_and_functionality_of_search) }}</dd>
					@endif

					@if ($survey->how_to_improve_the_convenience_of_searching_for_books)
						<dt>{{ __('survey.how_to_improve_the_convenience_of_searching_for_books') }}</dt>
						<dd>{{ $survey->how_to_improve_the_convenience_of_searching_for_books }}</dd>
					@endif

					@if ($survey->how_do_you_rate_the_site_design)
						<dt>{{ __('survey.how_do_you_rate_the_site_design') }}</dt>
						<dd>{{ __('survey.scale_options.'.$survey->how_do_you_rate_the_site_design) }}</dd>
					@endif

					@if ($survey->how_to_improve_the_site_design)
						<dt>{{ __('survey.how_to_improve_the_site_design') }}</dt>
						<dd>{{ $survey->how_to_improve_the_site_design }}</dd>
					@endif

					@if ($survey->how_do_you_assess_the_work_of_the_site_administration)
						<dt>{{ __('survey.how_do_you_assess_the_work_of_the_site_administration') }}</dt>
						<dd>{{ __('survey.scale_options.'.$survey->how_do_you_assess_the_work_of_the_site_administration) }}</dd>
					@endif

					@if ($survey->how_improve_the_site_administration)
						<dt>{{ __('survey.how_improve_the_site_administration') }}</dt>
						<dd>{{ $survey->how_improve_the_site_administration }}</dd>
					@endif

					@if ($survey->what_do_you_like_on_the_site)
						<dt>{{ __('survey.what_do_you_like_on_the_site') }}</dt>
						<dd>{{ $survey->what_do_you_like_on_the_site }}</dd>
					@endif

					@if ($survey->what_you_dont_like_about_the_site)
						<dt>{{ __('survey.what_you_dont_like_about_the_site') }}</dt>
						<dd>{{ $survey->what_you_dont_like_about_the_site }}</dd>
					@endif

					@if ($survey->what_you_need_on_our_site)
						<dt>{{ __('survey.what_you_need_on_our_site') }}</dt>
						<dd>{{ $survey->what_you_need_on_our_site }}</dd>
					@endif

					@if (!empty($survey->what_site_features_are_interesting_to_you) and count($survey->what_site_features_are_interesting_to_you) > 0 )
						<dt>{{ __('survey.what_site_features_are_interesting_to_you') }}</dt>
						<dd>
							@foreach ($survey->what_site_features_are_interesting_to_you as $value)
								{{ __('survey.what_site_features_are_interesting_to_you_options.'.$value) }}{{ $loop->last ? '' : ', ' }}
							@endforeach
						</dd>
					@endif
				</dl>
			</div>
		</div>
	@endforeach

	@if (isset($surveys) and $surveys->hasPages())
		<div class="row mt-3">
			<div class="col-12">
				{{ $surveys->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection
