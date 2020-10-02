<div class="card mb-3">
	<div class="card-header">
		{{ $block->name }}
	</div>
	<div class="card-body">
		{{ $block->description }}
	</div>
	<div class="card-footer">

		@if ($block->isEnabled())
			<a class="btn btn-sm btn-outline-success" href="{{ route('ad_blocks.disable', ['ad_block' => $block->id]) }}">
				<i class="fas fa-toggle-on"></i>
			</a>
		@else
			<a class="btn btn-sm btn-outline-secondary" href="{{ route('ad_blocks.enable', ['ad_block' => $block->id]) }}">
				<i class="fas fa-toggle-off"></i>
			</a>
		@endif

		<a class="btn btn-sm btn-outline-secondary" href="{{ route('ad_blocks.edit', ['ad_block' => $block->id]) }}">
			{{ __('Edit') }}
		</a>

		<a class="btn btn-sm btn-outline-secondary" href="{{ route('ad_blocks.delete', ['ad_block' => $block->id]) }}">
			{{ __('Delete') }}
		</a>

	</div>
</div>