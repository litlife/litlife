@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/book-keyword.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="card">
		<div class="card-body">

			<form role="form" method="post" enctype="multipart/form-data" class="keywords-form"
				  action="{{ route('books.keywords.store', $book) }}">

				@csrf
				@method('patch')

				<div class="row form-group">

					<select name="keywords[]" class="keywords form-control select2-multiple" multiple style="width:100%">
						@if (isset($book->keywords))
							@foreach ($book->keywords as $keyword)
								<option value="{{ $keyword->id }}" selected="selected">{{ $keyword->text }}</option>
							@endforeach
						@endif
					</select>

				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('common.save') }}
				</button>

			</form>
		</div>
	</div>
@endsection