@if ($paginator->hasPages())
	<div style="max-width: 100%; overflow: hidden;">
		<ul class="pagination flex-nowrap"
			style="overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
			{{-- Previous Page Link --}}
			@if ($paginator->onFirstPage())
				<li class="page-item disabled"><span class="page-link">&laquo;</span></li>
			@else
				<li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
				</li>
			@endif

			{{-- Pagination Elements --}}
			@foreach ($elements as $element)
				{{-- "Three Dots" Separator --}}
				@if (is_string($element))
					<li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
				@endif

				{{-- Array Of Links --}}
				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
							<li class="page-item active"><span class="page-link">{{ $page }}</span></li>
						@else
							<li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
						@endif
					@endforeach
				@endif
			@endforeach

			{{-- Next Page Link --}}
			@if ($paginator->hasMorePages())
				<li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
				</li>
			@else
				<li class="page-item disabled"><span class="page-link">&raquo;</span></li>
			@endif

			<li class="ml-1 d-flex align-items-center">
				<input class="form-control form-control-sm current-page"
					   data-url="{{ $paginator->url($paginator->currentPage()) }}"
					   value="{{ $paginator->currentPage() }}"
					   style="width:3rem; text-align: center">
			</li>
			<li class="ml-1 d-flex align-items-center">
				<button class="btn btn-light btn-sm set-current-page">{{ __('pagination.go_to') }}</button>
			</li>
		</ul>
	</div>
@endif
