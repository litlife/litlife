@php
	if (empty($textBlock))
		$textBlock = App\TextBlock::latestVersion($name ?? '');
@endphp

<div class="card mb-3">
	<div class="card-body imgs-fluid mark_links_in_blue">

		@if (isset($textBlock->text))
			@can('view', $textBlock)
				{!! $textBlock->text !!}
			@endcan
		@endif

	</div>

	@if (!isset($textBlock))

		@can('create', new App\TextBlock)
			<div class="card-footer">
				<a href="{{ route('text_blocks.create', compact('name')) }}" class="btn btn-light">
					{{ __('text_block.create') }}
				</a>
			</div>
		@endcan

	@endif

	@can('update', $textBlock)
		<div class="card-footer">
			<a href="{{ route('text_blocks.edit', compact('name')) }}" class="btn btn-light">
				{{ __('text_block.edit') }}
			</a>
		</div>
	@endcan
</div>