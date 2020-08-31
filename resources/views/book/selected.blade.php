@extends('layouts.app')

@section('content')


	<form role="form" method="POST" action="{{ route('books.move') }}" enctype="multipart/form-data">

		@csrf

		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif


		<div class="form-group{{ $errors->has('books') ? ' has-error' : '' }}">
			<div class="col-12">
				<div class="table-responsive">
					<table class="table table-stripped">
						@foreach ($books as $book)
							<tr>
								<td>
									<x-book-name :book="$book"/>
								</td>
								<td>
									@if(count($book->writers) > 0)
										{{ __('') }}Авторы:
										@foreach ($book->writers as $author)
											<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
										@endforeach
									@endif
								</td>
								<td>
									<div>
										@if(count($book->genres) > 0)
											{{ __('') }}Жанр:
											@foreach ($book->genres as $number => $genre)
												<a href="{{ route('genres.show', ['genre' => $genre->getIdWithSlug()]) }}">{{ $genre->name }}</a>{{ $loop->last ? '' : ', ' }}
											@endforeach

										@endif
									</div>
								</td>
							</tr>
						@endforeach
					</table>
				</div>
				<input name="books[]" type="hidden" value="{{ $book->id }}">

			</div>
		</div>

		<div class="form-group{{ $errors->has('author') ? ' has-error' : '' }}">
			<input name="author" class="form-control" type="text"
				   placeholder="{{ __('') }}Введите id автора к котору нужно переместить книги"
				   value="{{ old('author') ?? ''  }}">
		</div>

		<button type="submit" class="btn btn-light">{{ __('') }}Перенести</button>

	</form>



@endsection
