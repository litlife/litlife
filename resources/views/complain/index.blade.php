@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/complains.index.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@if (session('success'))
		<div class="row mb-3">
			<div class="col-12">
				<div class="alert alert-success alert-dismissable">
					{{ session('success') }}
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				</div>
			</div>
		</div>
	@endif

	@if(count($complains) > 0)

		@foreach ($complains as $item)
			@include('complain.item')
		@endforeach

	@else
		<p class="alert alert-info">{{ __('complain.nothing_found') }}</p>
	@endif

	@if ($complains->hasPages())
		<div class="row">
			<div class="col-12">
				{{ $complains->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection



