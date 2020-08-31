<ul class="nav nav-pills mb-3">
	<li class="nav-item">
		<a class="nav-link  text-truncate {{ isActiveRoute('collections.show') }}" style="max-width: 150px;"
		   href="{{ route('collections.show', $collection) }}">
			{{ $collection->title }}
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{ isActiveRoute('collections.books') }}" href="{{ route('collections.books', $collection) }}">
			{{ trans_choice('collection.books', $collection->books_count) }} <span
					class="badge">{{ $collection->books_count }}</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{ isActiveRoute('collections.comments') }}"
		   href="{{ route('collections.comments', $collection) }}">
			{{ trans_choice('collection.comments', $collection->comments_count) }} <span
					class="badge">{{ $collection->comments_count }}</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{ isActiveRoute('collections.users.index') }}"
		   href="{{ route('collections.users.index', $collection) }}">
			{{ trans_choice('collection.users', $collection->users_count) }} <span
					class="badge">{{ $collection->users_count }}</span>
		</a>
	</li>
</ul>