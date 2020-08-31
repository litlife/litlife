@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/message.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@can('write_private_messages', $user)
		@include('message.create_form')
	@endcan

	<div class="messages" role="main">
		@include('message.index_ajax')
	</div>

@endsection