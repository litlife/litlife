@if ($paginator->hasPages())
	<div style="max-width: 100%; overflow: hidden;">
		<ul class="pagination flex-nowrap"
			style="overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
			{{-- Previous Page Link --}}
			@if ($paginator->onFirstPage())
				<li class="page-item disabled" data-toggle="tooltip" data-placement="top">
                <span class="page-link">
                    <i class="fas fa-angle-left"></i>
                </span>
				</li>
			@else
				<li class="page-item" data-toggle="tooltip" data-placement="top">
					<a class="page-link" href="{{ $paginator->url(1) }}" rel="prev">
						<i class="fas fa-angle-double-left"></i>
					</a>
				</li>

				<li class="page-item" data-toggle="tooltip" data-placement="top">
					<a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
						<i class="fas fa-angle-left"></i>
					</a>
				</li>
			@endif

			<li class="page-item disabled">
				<a class="page-link" href="javascript:void(0)">
					{{ $paginator->currentPage() }}
				</a>
			</li>

			{{-- Next Page Link --}}
			@if ($paginator->hasMorePages())
				<li class="page-item" data-toggle="tooltip" data-placement="top">
					<a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
						<i class="fas fa-angle-right"></i>
					</a>
				</li>
			@else
				<li class="page-item disabled" data-toggle="tooltip" data-placement="top">
					<span class="page-link"><i class="fas fa-angle-right"></i></span>
				</li>
			@endif

			<li class="ml-1 d-flex align-items-center">
				<input class="form-control form-control current-page" value="{{ $paginator->currentPage() }}"
					   data-url="{{ $paginator->url($paginator->currentPage()) }}"
					   style="width:3rem; text-align: center">
			</li>
			<li class="ml-1 d-flex align-items-center">
				<button class="btn btn-light btn-sm set-current-page">{{ __('pagination.go_to') }}</button>
			</li>
			<li class="ml-3  d-flex align-items-center">
				<small>{{ __('pagination.per_page') }}:</small>
			</li>
			<li class="ml-1 d-flex align-items-center">
				<input class="form-control form-control per-page" value="{{ $paginator->perPage() }}"
					   style="width:3rem; text-align: center">
			</li>
			<li class="ml-1 d-flex align-items-center">
				<button class="btn btn-light btn-sm set-per-page">{{ __('pagination.select') }}</button>
			</li>
		</ul>
	</div>
@endif
