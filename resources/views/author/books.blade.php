@if (isActiveRoute('authors.show'))

	@push('body_append')

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/jquery.tablesorter.js"></script>
		<script
				src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>

	@endpush

	@push('css')

		<style type=text/css>

			#books .table {
				font-size: 0.9rem;
			}

			#books .table thead th {
				padding-top: 1rem;
				padding-bottom: 1rem;
				background-color: #fff !important;
				cursor: pointer;
				font-size: 0.9rem !important;
				text-align: center;
			}

			#books .table .filtered {
				display: none;
			}

			#books .table .tablesorter-errorRow td {
				text-align: center;
				cursor: pointer;
				background-color: #e6bf99;
			}

		</style>
	@endpush

	@if ($written_books->count() > 0)
		@include ('author.books_table', ['name' => __('author.written_books'), 'books' => $written_books])
	@endif

	@if ($translated_books->count() > 0)
		@include ('author.books_table', ['name' => __('author.translated_books'), 'books' => $translated_books])
	@endif

	@if ($edited_books->count() > 0)
		@include ('author.books_table', ['name' => __('author.edited_books'), 'books' => $edited_books])
	@endif

	@if ($illustrated_books->count() > 0)
		@include ('author.books_table', ['name' => __('author.illustrated_books'), 'books' => $illustrated_books])
	@endif

	@if ($compiled_books->count() > 0)
		@include ('author.books_table', ['name' => __('author.compiled_books'), 'books' => $compiled_books])
	@endif

@endif