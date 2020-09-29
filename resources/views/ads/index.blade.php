@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@if (session('success'))
		<div class="alert alert-success alert-dismissable mb-3">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if ($blocks->hasPages())
		{{ $blocks->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@if (!$blocks->count())
		<div class="alert alert-info">
			{{ __('No blocks found') }}
		</div>
	@else
		<div>
			@foreach ($blocks as $block)
				@include('ads.item', ['block' => $block])
			@endforeach
		</div>
	@endif

	@if ($blocks->hasPages())
		{{ $blocks->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	<div class="mt-3">
		<a href="{{ route('ad_blocks.create') }}" class="btn btn-primary">
			{{ __('Create') }}
		</a>
	</div>

@endsection