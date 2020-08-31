@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/post.search.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('forum/post/search')

@endsection