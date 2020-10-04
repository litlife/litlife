<div class="list-group-item collection" data-book-id="{{ $book->id }}" data-collection-id="{{ $item->id }}">
	<div class="d-flex w-100 justify-content-between">
		<h6 class="mb-2">
			<a href="{{ route('collections.show', $collection) }}">
				{{ $item->title }}
			</a>
		</h6>
	</div>

	<div class="mb-2 d-flex flex-row">
		<div class="mr-3">
			@if ($item->books->count())
				{{ __('In collection') }}
			@else
				<button class="select btn btn-primary">{{ __('Select') }}</button>
			@endif
		</div>
		<div>
			{{ \Illuminate\Support\Str::limit($item->description, 150) }}
		</div>
	</div>

	<div class="d-flex w-100 justify-content-between align-items-center">
		<div class="flex-grow-1 d-flex flex-row align-items-center">
			<div class="mr-2" style="min-width: 30px; max-width: 30px;">
				<x-user-avatar :user="$item->create_user->avatar" width="30" height="30"/>
			</div>
			<x-user-name :user="$item->create_user"/>
		</div>

		<small class="ml-3 text-nowrap">
			<x-time :time="$item->created_at"/>
		</small>
	</div>
</div>