@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/comments_list.js', config('litlife.assets_path')) }}"></script>
@endpush

@section ('content')

	@include('comment.search')

@endsection