<div class="card collection mb-3" data-id="{{ $item->id }}">
	<div class="card-header d-flex">
		<div class="flex-grow-1 d-flex flex-row align-items-center">
			<div class="mr-2" style="min-width: 30px; max-width: 30px;">
				<x-user-avatar :user="$item->create_user" width="30" height="30"/>
			</div>
			<x-user-name :user="$item->create_user"/>
		</div>
		<div class="d-flex flex-row  align-items-center">

			<small class="ml-2 mr-2">
				<x-time :time="$item->created_at"/>
			</small>

			<div class="btn-group" data-toggle="tooltip" data-placement="top" title="{{ __('common.open_actions') }}">
				<button class="btn btn-light dropdown-toggle" type="button"
						id="collectionDropdownMenuButton_{{ $item->id }}"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
					<i class="fas fa-ellipsis-v"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right"
					 aria-labelledby="collectionDropdownMenuButton_{{ $item->id }}">

					@can('addBook', $item)
						<a class="dropdown-item text-lowercase" target="_blank"
						   href="{{ route("collections.books.select", $item) }}">
							{{ __('collection.attach_book') }}
						</a>
					@endcan

					@can('update', $item)
						<a class="btn-edit dropdown-item text-lowercase"
						   href="{{ route("collections.edit", $item) }}">
							{{ __('common.edit') }}
						</a>
					@endcan

					<a class="dropdown-item text-lowercase" target="_blank"
					   href="{{ route("likes.users", ['id' => $item->id, 'type' => 'collection']) }}">
						{{ __('collection.who_likes') }}
					</a>

					<a class="pointer dropdown-item text-lowercase"
					   href="{{ route("collections.delete.confirmation", $item) }}"
					   @cannot ('delete', $item) style="display:none;"@endcannot>
						{{ __('Delete') }}
					</a>

				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		@can ('view', $item)

			<div class="card-title">

				<h6 class="d-inline"><a href="{{ route('collections.show', $item) }}">{{ $item->title }}</a>
					@if ($item->isPrivate())
						<i class="fas fa-lock" data-toggle="tooltip" data-placement="top"
						   title="{{ __('collection.private_tooltip') }}"></i>
					@endif
				</h6>

				<small class="ml-2">
					<a href="{{ route('collections.books', $item) }}"
					   class="text-nowrap">{{ $item->books_count }} {{ trans_choice('collection.books', $item->books_count) }}</a>
				</small>
			</div>

			<p class="card-text">{{ $item->description }}</p>

			{{--
				@if ($item->latest_books->count() > 0)
					<div class="d-flex flex-nowrap mb-2" style="overflow-x:auto; ">
						@foreach ($item->latest_books as $book)
							@include('collection.carousel_item', ['book' => $book])
						@endforeach

						@if ($item->books_count > 3)
						<div class="p-2" style="">

							<a href="{{ route('collections.books', $item) }}">
								<div class="d-flex align-items-center justify-content-center" style="width: 150px; height:200px;">
									<div class="text-center" style="padding:0.5rem; ">
										<h1><i class="far fa-arrow-alt-circle-right"></i></h1>
										Все книги
									</div>
								</div>
							</a>
						</div>
							@endif
					</div>
				@endif
	--}}
		@else

			<div class="card-title">
				<h5 class="inline">{{ __('collection.access_to_the_collection_is_limited') }}</h5>
			</div>

		@endcan
	</div>

	<div class="card-footer text-muted d-flex align-items-center">
		<div class="flex-grow-1">
			@include('like.item', ['item' => $item, 'like' => $item->authUserLike ?? null, 'likeable_type' => 18])

			<a href="{{ route('collections.books', $item) }}" class="btn btn-light" data-toggle="tooltip"
			   data-placement="top" title="{{ __('collection.books_count') }}">
				<i class="fas fa-book"></i> <span class="ml-1">{{ $item->books_count }}</span>
			</a>

			@if ($item->users_count > 1)
				<a href="{{ route('collections.users.index', $item) }}" class="btn btn-light" data-toggle="tooltip"
				   data-placement="top" title="{{ __('collection.users_count') }}">
					<i class="far fa-user"></i> <span class="ml-1">{{ $item->users_count }}</span>
				</a>
			@endif

			<a href="{{ route('collections.comments', $item) }}#comments" class="btn btn-light"
			   data-toggle="tooltip" data-placement="top" title="{{ __('collection.comments_count') }}">
				<i class="far fa-comments"></i> <span class="ml-1">{{ $item->comments_count }}</span>
			</a>

			@include('favorite_button', ['type' => 'collection', 'favorite' => $item->usersAddedToFavorites->first()])

			@include('share.share_button')
		</div>
		<div>
			<i class="far fa-eye"></i>
			<span class="ml-1">{{ $item->views_count }}</span>

		</div>
	</div>
</div>
