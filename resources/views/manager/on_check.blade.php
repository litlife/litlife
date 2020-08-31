@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/managers.on_check.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@if ($managers->hasPages())
		{{ $managers->appends(request()->except(['page', 'ajax']))->links() }}
	@endif


	@if (!$managers->count())
		<div class="alert alert-info">
			{{ __('manager.nothing_found') }}
		</div>
	@else
		<div class="managers">
			@foreach ($managers as $item)
				@include('manager.item', ['item' => $item, 'show_comment' => true])
			@endforeach
		</div>
	@endif


	@if ($managers->hasPages())
		{{ $managers->appends(request()->except(['page', 'ajax']))->links() }}

	@endif



@endsection