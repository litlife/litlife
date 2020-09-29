<div class="card mb-3">
	<div class="card-header">
		{{ $block->name }}
	</div>
	<div class="card-body">

		<a class="btn btn-sm btn-outline-secondary" href="{{ route('ad_blocks.edit', ['ad_block' => $block->id]) }}">
			{{ __('Edit') }}
		</a>

		<a class="btn btn-sm btn-outline-secondary" href="{{ route('ad_blocks.delete', ['ad_block' => $block->id]) }}">
			{{ __('Delete') }}
		</a>

	</div>
</div>