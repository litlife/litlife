@if ($paginator->hasPages())

	{!! $paginator->links('vendor.pagination.bootstrap-4') !!}

@endif
