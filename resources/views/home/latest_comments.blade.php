@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/latest-comments.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('home.navbar')

	@if ($comments->hasPages())
		{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@foreach ($comments as $comment)
		@include('comment.list.default', ['item' => $comment, 'go_to_button' => true, 'no_limit' => true])
	@endforeach

	@if ($comments->hasPages())
		{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection