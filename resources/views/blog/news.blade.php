@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/blog.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	<div class="blogs">
		@include('blog.news_ajax')
	</div>

@endsection