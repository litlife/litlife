@if ($paginator->hasPages())

	{!! $paginator->links('vendor.pagination.simple-bootstrap-4') !!}

@endif
