@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/comments.on_check.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')


	<div class="list">

		@if(count($comments) > 0)
			@foreach ($comments as $comment)
				@include('comment.list.default', ['check_buttons' => true,
				'go_to_button' => true,
				'item' => $comment,
				'show_text_even_if_on_review' => true])
			@endforeach
		@else

			<p class="alert alert-info">{{ __('comment.nothing_found') }}</p>

		@endif
	</div>

@endsection