@if (isset($item))
	@if ($item->trashed())
		{{ __('collection.deleted') }}
	@else
		<a href="{{ route('collections.show', $item) }}">{{ $item->title }}</a>
	@endif
@else
	{{ __('collection.deleted') }}
@endif
