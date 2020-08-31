<div class="card mb-3">
	<div class="card-body">

		<p class="card-text">
			{{ trans_choice('author.authors', 2) }}:
			@foreach ($item->authors as $author)
				<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
			@endforeach
		</p>

		@if (!empty($item->comment))
			<blockquote class="blockquote">
				<p class="mb-0">{!! $item->comment !!}</p>
			</blockquote>
		@endif

		@can ('merge', App\Author::class)
			<a target="_blank" class="btn btn-outline-success "
			   href="{{ route('authors.merge', ['authors' => $item->authors->pluck('id')->toArray()]) }}">
				{{ __('common.merge') }}
			</a>
		@endcan

		@can ('update', $item)
			<a class="btn btn-outline-secondary "
			   href="{{ route('author_repeats.edit', compact('author_repeat')) }}">
				{{ __('common.edit') }}
			</a>
		@endcan

		@can ('delete', $item)
			<a class="btn btn-outline-danger "
			   href="{{ route('author_repeats.delete', compact('author_repeat')) }}">
				{{ __('common.delete') }}
			</a>
		@endcan
	</div>
	<div class="card-footer text-muted">
		{{ trans_choice('user.created', $item->create_user->gender) }}
		<x-user-name :user="$item->create_user"/>

		<x-time :time="$item->created_at"/>
	</div>
</div>