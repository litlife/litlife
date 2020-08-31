@extends('layouts.app')

@section('content')

	<div class="card-columns">
		@foreach ($genres as $genre)
			<div class="card " style="">
				<div class="card-header">
					<a href="{{ route('genres.show', ['genre' => $genre->getIdWithSlug()]) }}">
						<h3 class="mb-0 h6">{{ $genre->name }}</h3>
					</a>
				</div>
				<div class="list-group list-group-flush">
					@foreach ($genre->childGenres as $childGenre)
						<a class="list-group-item border-0 list-group-item-action"
						   href="{{ route('genres.show', ['genre' => $childGenre->getIdWithSlug()]) }}">
							<div class="d-flex w-100 justify-content-between">
								<h6 class="h6 font-weight-normal mb-0">{{ $childGenre->name }}
									@if ($childGenre->age > 0)
										{{ $childGenre->age }}+
									@endif
								</h6>
								<small class="ml-2 text-muted">{{ $childGenre->book_count }}</small>
							</div>
						</a>
					@endforeach
				</div>
			</div>
		@endforeach
	</div>

	@can ('create', App\Genre::class)
		<div class="row ">
			<div class="col-12">
				<a class="btn btn-primary" href="{{ route('genres.create') }}">{{ __('common.create') }}</a>
			</div>
		</div>
	@endcan

@endsection