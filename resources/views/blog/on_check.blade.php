@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/blogs.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="blog-posts">
		@if(count($blogs) > 0)
			@foreach ($blogs as $blog)
				@include('blog.list.default', ['check_buttons' => true,
				'go_to_button' => true,
				'item' => $blog,
				'show_text_even_if_on_review' => true])
			@endforeach
		@else
			<p class="alert alert-info">{{ __('blog.nothing_found') }}</p>
		@endif
	</div>

@endsection