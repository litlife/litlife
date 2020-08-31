@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/collections.index.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@can ('create', \App\Collection::class)

		<div class="row mb-3">
			<div class="col-12 ">
				<a class="btn btn-primary" href="{{ route('collections.create')  }}">{{ __('collection.create') }}</a>
			</div>
		</div>
	@endcan

	@include('collection.search')

@endsection
