<div class="d-flex">
	<div class="mr-3 text-center" style="width: 90px;">
		<x-book-cover :book="$book" width="80" height="80" style="max-width: 100%;"/>
	</div>
	<div class="w-100">
		<div class="d-flex w-100 justify-content-between">
			<h6 class="mb-1">{{ $book->title }}</h6>
		</div>
		<p class="mb-1">
			@foreach ($book->writers as $author)
				<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
			@endforeach
		</p>
	</div>
	<input id="book_id" type="hidden" name="book_id" value="{{ $book->id }}"/>
</div>