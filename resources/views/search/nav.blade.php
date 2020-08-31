<ul class="nav nav-pills">

	@if ($books_count > 0)
		<li class="nav-item">
			<a class="nav-link" href="#books">
				{{ __('search.books') }} <span class="badge badge-light">{{ $books_count }}</span>
			</a>
		</li>
	@endif

	@if ($authors_count > 0)
		<li class="nav-item">
			<a class="nav-link" href="#authors">
				{{ __('search.authors') }} <span class="badge badge-light">{{ $authors_count }}</span>
			</a>
		</li>
	@endif

	@if ($sequences_count > 0)
		<li class="nav-item">
			<a class="nav-link" href="#sequences">
				{{ __('search.sequences') }} <span class="badge badge-light">{{ $sequences_count }}</span>
			</a>
		</li>
	@endif

	@if ($collections_count > 0)
		<li class="nav-item">
			<a class="nav-link" href="#collections">
				{{ __('search.collections') }} <span class="badge badge-light">{{ $collections_count }}</span>
			</a>
		</li>
	@endif

	@if ($users_count > 0)
		<li class="nav-item">
			<a class="nav-link" href="#users">
				{{ __('search.users') }} <span class="badge badge-light">{{ $users_count }}</span>
			</a>
		</li>
	@endif

	@isset($topics_count)
		<li class="nav-item">
			<a class="nav-link" href="#topics">
				{{ __('search.topics') }} <span class="badge badge-light">{{ $topics_count }}</span>
			</a>
		</li>
	@endisset
</ul>
