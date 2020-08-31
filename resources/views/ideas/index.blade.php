@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/ideas.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@if ($errors->idea->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->idea->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@auth
		<div class="card mb-2">
			<div class="card-body">

				<p>{{ __('idea.help_us_make_site_better_and_more_convenient_for_you') }}</p>

				<form id="idea_create_form" action="{{ route('ideas.store') }}" enctype="multipart/form-data" method="post">

					@csrf

					<div class="form-group">
						<input name="name" type="text" class="form-control" id="name" aria-describedby="nameHelp" value="{{ old('name') }}"
							   placeholder="{{ __('idea.short_name_of_the_idea') }}">
					</div>
					<div class="form-group">
						<label for="bb_text">{{ __('idea.bb_text') }}:</label>
						<textarea id="bb_text" class="sceditor form-control" placeholder="{{ __('idea.bb_text') }}"
								  rows="10" name="bb_text">{{ old('bb_text') }}</textarea>
					</div>
					<div class="form-group form-check">
						<input type="hidden" name="enable_notifications_of_new_messages" value="0"/>
						<input type="checkbox" name="enable_notifications_of_new_messages" value="1"
							   class="form-check-input" id="enable_notifications_of_new_messages" checked="checked">
						<label class="form-check-label" for="enable_notifications_of_new_messages">
							<i class="far fa-bell"></i>
							{{ __('idea.enable_notifications_of_new_messages') }}
							<i class="fas fa-question" data-toggle="tooltip" data-placement="top"
							   title="{{ __('idea.when_new_messages_appear_in_the_discussion_of_an_idea_you_will_receive_notifications_on_the_site_and_by_email') }}"></i>
						</label>
					</div>
					<button type="submit" class="btn btn-primary">{{ __('idea.save_idea') }}</button>
				</form>
			</div>
		</div>
	@else
		<div class="alert alert-warning">
			{{ __('idea.register_or_log_in_to_your_account_to_suggest_an_idea') }}
		</div>
	@endauth

	<div class="ideas list-group mb-3">
		@if(count($items) > 0)
			@include('ideas.search')
		@endif
	</div>

@endsection
