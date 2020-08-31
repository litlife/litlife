<li class="list-group-item section py-1 pl-2 pr-0">
	<div class="d-flex" style="min-height:2.5rem;">
		<div class="flex-grow-1 mr-2">
			<div class="d-flex flex-column">
				<a href="{{ route('books.old.page', ['book' => $book, 'page' => $item['page']]) }}{{ empty($item['sn']) ? '' : '#'.$item['sn'] }}"
				   class="title mt-2">
					<h6 class="mb-0 ">{{ $item['title'] }}</h6>
				</a>
			</div>
		</div>
		<div class="flex-shrink-1">

		</div>
	</div>
	<ol class="border-left ml-2 pl-0">
		@if(!empty($item['ch']) and count($item['ch']) > 0)
			@foreach($item['ch'] as $section)
				@include('book.page.item', ['item' => $section])
			@endforeach
		@endif
	</ol>
</li>

