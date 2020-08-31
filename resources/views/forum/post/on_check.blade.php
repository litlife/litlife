@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/posts.on_check.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="list">

		@if(count($posts) > 0)
			@foreach ($posts as $post)
				@include('forum.post.item.default', ['item' => $post,
				'check_buttons' => true,
				'no_limit' => true,
				'show_text_even_if_on_review' => true
				])
			@endforeach
		@else
			<p class="alert alert-info">{{ __('post.nothing_found') }}</p>
		@endif
	</div>

@endsection