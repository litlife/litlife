@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/users.achievements.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="row mb-3">
		<div class="col-12">
			@can ('create', App\Achievement::class)
				<a class="btn btn-primary" href="{{ route('achievements.create') }}">
					{{ __('achievement.create') }}
				</a>
			@endcan
		</div>
	</div>

	@if (!empty($achievements) and $achievements->count())
		<div class="achievements card-columns">
			@foreach ($achievements as $achievement)
				@include ('achievement.list.default')
			@endforeach
		</div>
	@else
		<div class="row">
			<div class="col-12">
				<div class="alert alert-info">{{ __('achievement.nothing_found') }}</div>
			</div>
		</div>
	@endif

	@if ($achievements->hasPages())
		<div class="row">
			<div class="col-12">
				{{ $achievements->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection