@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/blogs.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('home.navbar')

	@if ($blogs->hasPages())
		{{ $blogs->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	<div class="blog-posts">
		@foreach ($blogs as $blog)
			@include('blog.list.default', [
			'item' => $blog,
			'go_to_button' => true,
			'no_limit' => true,
			'no_child_toggle' => false
			])
		@endforeach
	</div>


	@if ($blogs->hasPages())

		{{ $blogs->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection