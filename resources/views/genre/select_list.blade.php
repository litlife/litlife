@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-12">
			<div class="card-columns">
				@foreach ($genres as $genre)
					<div class="card">
						<h6 class="card-header mb-0" data-type="genre_group" data-id="{{ $genre->id }}" style="cursor: pointer">
							{{ $genre->name }}
						</h6>

						<div class="list-group list-group-flush">
							@foreach ($genre->childGenres as $childGenre)
								<div class="list-group-item border-0 list-group-item-action">
									<div class="form-check">
										<input data-type="genre" data-id="{{ $childGenre->id }}"
											   data-parent-id="{{ $genre->id }}"
											   class="form-check-input" type="checkbox" value="" id="genre_{{ $childGenre->id }}">

										<label class="form-check-label" for="genre_{{ $childGenre->id }}">
											{{ $childGenre->name }}
										</label>
									</div>
								</div>
							@endforeach
						</div>

					</div>
				@endforeach
			</div>

		</div>
	</div>


@endsection