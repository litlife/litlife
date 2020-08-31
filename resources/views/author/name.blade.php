@if (isset($author))
	@if ($author->trashed())
		<a class="author " href="{{ route('authors.show', $author) }}">{{ __('author.deleted') }}</a>
	@else

		@if (empty($href_disable))
			<a class="author name @if (!empty($online) and $author->isOnline()) online @endif"
			   href="{{ route('authors.show', $author) }}">
				@endif
				{{ $author->last_name }} {{ $author->first_name }} {{ $author->middle_name }} <i>{{ $author->nickname }}</i>
				@if (empty($href_disable))
			</a>
		@endif

		@if (($author->lang != 'RU') and(!empty($author->lang)))
			({{ $author->lang }})
		@endif

		@if ($author->isPrivate())
			<i class="fas fa-lock" data-toggle="tooltip" data-placement="top"
			   title="{{ __('book.private_tooltip') }} "></i>
		@endif

	@endif
@else
	<span class="author">{{ __('author.deleted') }}</span>
@endif