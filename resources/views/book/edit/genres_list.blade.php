<div class="row form-group">
	<label for="genres" class="col-md-3 col-lg-2 col-form-label">{{ trans_choice('genre.genres', 2) }} * </label>

	<div class="col-md-9 col-lg-10">

		<select id="genres" name="genres[]" multiple class="genres form-control {{ $errors->has('genres') ? ' is-invalid' : '' }}">

			@foreach(old('genres') ? App\Genre::whereIn('id', old('genres'))->orderByField('id', old('genres'))->get() : $book->genres as $c => $genre)
				<option value="{{ $genre->id }}" selected>{{ $genre->name }}</option>
			@endforeach

		</select>

		<small class="form-text text-muted">
			{{ __('book.genres_helper') }}
		</small>

		<button id="selected_genres_button" type="button" class="btn btn-outline-secondary text-nowrap text-truncate"
				data-toggle="modal"
				data-target="#selected_genres_modal">
			{{ __('common.select_genres') }}
		</button>

		@push('body_append')
			<div class="modal" id="selected_genres_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
				 aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">{{ __('common.select_genres') }}</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="container-fluid">

							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary"
									data-dismiss="modal">{{ __('common.close') }}</button>
						</div>
					</div>
				</div>
			</div>
		@endpush

	</div>

</div>