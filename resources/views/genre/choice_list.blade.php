@foreach ($genres_groups as $genres_group)

	<div class="col-md-3">

		{{ $genres_group->name }}

		<ul>

			@foreach ($genres_group->genres as $genre)
				<li><a href="{{ route('genres.show', ['genre' => $genre->getIdWithSlug()]) }}">{{ $genre->name }}</a>
					- {{ $genre->book_count }}</li>

			@endforeach
		</ul>

	</div>

@endforeach
