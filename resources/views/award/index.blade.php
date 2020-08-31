@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/awards.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="row mb-3">
		<div class="col-12">
			@can ('create', App\Award::class)
				<a class="btn btn-primary" href="{{ route('awards.create') }}">
					{{ __('award.create') }}
				</a>
			@endcan
		</div>
	</div>

	@if (!empty($awards) and $awards->count())
		<div class="awards card-columns">
			@foreach ($awards as $award)
				@include ('award.list.default')
			@endforeach
		</div>
	@else
		<div class="row">
			<div class="col-12">
				<div class="alert alert-info">{{ __('award.nothing_found') }}</div>
			</div>
		</div>
	@endif

	@if ($awards->hasPages())
		<div class="row">
			<div class="col-12">
				{{ $awards->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection