@extends('layouts.app')

@push('scripts')


@endpush

@section('content')

	<div class="card">
		<div class="card-body">


			<div class="row">
				<div class="col-12 text-center">
					<h4>{{ $achievement->title }}</h4>
				</div>
			</div>

			<div class="row">
				<div class="col-12 text-center">
					<p>{{ $achievement->description }}</p>
				</div>
			</div>

			<div class="row">
				<div class="col-12 text-center">
					<img class="img-fluid" src="{{ $achievement->image->url }}" alt="">

				</div>
			</div>

		</div>
	</div>


@endsection