@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/collections.index.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if ($user->id == auth()->id())

		@can ('create', \App\Collection::class)
			<div class="row mb-3">
				<div class="col-12 ">
					<a class="btn btn-primary" href="{{ route('collections.create')  }}">{{ __('collection.create') }}</a>
				</div>
			</div>
		@endcan

	@endif

	@include('collection.search')

@endsection