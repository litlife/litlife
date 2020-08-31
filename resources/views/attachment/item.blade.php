<div class="item card" data-id="{{ $item->id }}" data-book-id="{{ $item->book->id }}" data-url="{{ $item->url }}">

	<a href="{{ $item->url }}" target="_blank">
		<img class="card-img-top" src="{{ $item->fullUrlMaxSize(300, 300) }}" alt="{{ $item->name }}">
	</a>
	<div class="card-body">
		<h5 class="title card-title">
			{{ $item->name }}

			@if ($item->isCover())
				({{ __('attachment.cover') }})
			@endif
		</h5>

		@if (!empty($item->getWidth()) and !empty($item->getHeight()))
			<p class="card-text">{{ $item->getWidth() }} x {{ $item->getHeight() }}</p>
		@endif

		<p class="card-text">{{ __('attachment.file_size') }}: {{ $item->size }}</p>

		@if (!empty($paste_button))
			<button class="insert btn btn-primary">
				{{ __('common.paste') }}
			</button>
		@endif

		<div class="btn-group">
			<button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown"
					id="attachment_{{ $item->id }}">
				<i class="fas fa-ellipsis-h"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="attachment_{{ $item->id }}">

				<a class="delete pointer dropdown-item" href="javascript:void(0)" disabled="disabled"
				   data-loading-text="{{ __('common.deleted_now') }}..."
				   @cannot ('delete', $item) style="display:none;"@endcannot>
					{{ mb_strtolower(__('common.delete')) }}
				</a>

				<a class="restore pointer dropdown-item" href="javascript:void(0)" disabled="disabled"
				   data-loading-text="{{ __('common.restored_now') }}..."
				   @cannot ('restore', $item) style="display:none;"@endcannot>
					{{ mb_strtolower(__('common.restore')) }}
				</a>

				@can ('setAsCover', $item)
					<li class="dropdown-item">
						<a href="{{ route('books.attachments.set_cover', ['book' => $item->book, 'id' => $item->id]) }}">
							{{ __('attachment.set_as_cover') }}
						</a>
					</li>
				@endcan
			</ul>
		</div>

	</div>
	<div class="card-footer">
		<small class="text-muted">{{ __('attachment.created_at') }}
			:
			<x-time :time="$item->created_at"/>
		</small>
	</div>
</div>