<div class="p-2" style="">
	<div style="width: 150px;">
		<x-book-cover :book="$book" width="200" height="200" class="card-img-top" style="max-width: 100%;"/>

		<div class="text-center" style="padding:0.5rem;">
			<h6 class="card-title" style="font-size:1rem">
				<small class="">
					<a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
				</small>
			</h6>
			<div class="" style="font-size:1rem">
				<small class="">
					@foreach ($book->writers as $author)
						<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
					@endforeach
				</small>
			</div>
		</div>
	</div>
</div>