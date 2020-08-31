@if ($books->count() > 0)

	<div id="books">
		<a href="{{ $books_url }}" class="d-flex flex-row text-decoration-none">
			<h5 id="books" class="font-weight-bold mb-3">{{ __('search.books') }}</h5>

			@if ($books_count > $books->count())
				<div class="ml-3">
					{{ __('search.more_books') }}: {{ $books_count - $books->count() }}
				</div>

			@endif
		</a>

		@foreach ($books as $book)
			@include('book.list.item')
		@endforeach
	</div>
@endif

@if ($authors->count() > 0)
	<div id="authors">
		<a href="{{ $authors_url }}" class="d-flex flex-row text-decoration-none">
			<h5 class="font-weight-bold mb-3">{{ __('search.authors') }}</h5>

			@if ($authors_count > $authors->count())
				<div class="ml-3">
					{{ __('search.more_authors') }}: {{ $authors_count - $authors->count() }}
				</div>
			@endif
		</a>

		@foreach ($authors as $author)
			<div class="card mb-3">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 col-lg-4 col-xl-3 text-center mb-3">
							<x-author-photo :author="$author" width="50" height="50" class="rounded"/>
						</div>
						<div class="col-md-12 col-lg-8 col-xl-9">
							<h6 class="card-title">
								<x-author-name :author="$author" showOnline="1"/>
							</h6>
							<p class="card-text mb-1">{{ __('common.vote') }}: {{ round($author->vote_average, 2) }}
								({{ $author->votes_count }})
							</p>
							<p class="card-text">{{ __('author.books_count') }}: {{ $author->books_count }}</p>

						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endif

@if ($sequences->count() > 0)
	<div id="sequences">
		<a href="{{ $sequences_url }}" class="d-flex flex-row text-decoration-none">
			<h5 class="font-weight-bold mb-3">{{ __('search.sequences') }}</h5>

			@if ($sequences_count > $sequences->count())
				<div class="ml-3">
					{{ __('search.more_sequences') }}: {{ $sequences_count - $sequences->count() }}
				</div>
			@endif
		</a>

		@foreach ($sequences as $sequence)
			<div class="card mb-3">
				<div class="card-body d-flex">
					<div class="w-100">
						@include('sequence.name')
					</div>
					<div class="flex-shrink-1 text-nowrap">
						{{ __('sequence.books') }}: {{ $sequence->books_count }}
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endif

@if ($collections->count() > 0)
	<div id="collections">
		<a href="{{ $collections_url }}" class="d-flex flex-row text-decoration-none">
			<h5 id="collections" class="font-weight-bold mb-3">{{ __('search.collections') }}</h5>

			@if ($collections_count > $collections->count())
				<div class="ml-3">
					{{ __('search.more_collections') }}: {{ $collections_count - $collections->count() }}
				</div>
			@endif
		</a>

		@foreach ($collections as $item)
			@include('collection.item')
		@endforeach
	</div>
@endif

@if ($users->count() > 0)
	<div id="users">
		<a href="{{ $users_url }}" class="d-flex flex-row text-decoration-none">
			<h5 class="font-weight-bold mb-3">{{ __('search.users') }}</h5>

			@if ($users_count > $users->count())
				<div class="ml-3">
					{{ __('search.more_users') }}: {{ $users_count - $users->count() }}
				</div>
			@endif
		</a>

		@foreach ($users as $user)
			@include('user.list.default')
		@endforeach
	</div>
@endif

@isset($topics)
	@if ($topics->count() > 0)
		<div id="topics">
			<a href="{{ $topics_url }}" class="d-flex flex-row text-decoration-none">
				<h5 class="font-weight-bold mb-3">{{ __('search.topics') }}</h5>

				@if ($topics_count > $topics->count())
					<div class="ml-3">
						{{ __('search.more_collections') }}: {{ $topics_count - $topics->count() }}
					</div>
				@endif
			</a>

			<div class="table-responsive">
				<table class="table table-light ">
					@foreach ($topics as $item)
						@include('forum.topic.item.default')
					@endforeach
				</table>
			</div>

			@endif
		</div>
	@endif