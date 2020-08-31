@extends('layouts.app')


@push('scripts')

	<script src="{{ mix('js/users_list.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('user.search')

@endsection