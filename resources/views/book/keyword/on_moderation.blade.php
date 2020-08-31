@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/book-keyword.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')


	@if(count($keywords) > 0)
		<div class="card mb-3">
			<div class="card-body">
				@include('book.keyword.table')
			</div>
		</div>
		@if ($keywords->hasPages())
			{{ $keywords->appends(request()->except(['page', 'ajax']))->links() }}
		@endif
	@else
		<div class="alert alert-info">{{ __('keyword.nothing_found') }}</div>
	@endif

@endsection