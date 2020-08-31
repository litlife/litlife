@if (isset($sequence))
	@if ($sequence->trashed())
		<a class="sequence name" href="{{ route('sequences.show', $sequence) }}">{{ __('sequence.deleted') }}</a>
	@else

		@if (empty($href_disable))
			<a class="sequence name" href="{{ route('sequences.show', $sequence) }}">
				@endif
				{{ $sequence->name }}
				@if (empty($href_disable))
			</a>
		@endif

		@if ($sequence->isPrivate())
			<i class="fas fa-lock" data-toggle="tooltip" data-placement="top" title="{{ __('book.private_tooltip') }}"></i>
		@endif

	@endif
@else
	<span class="sequence name">{{ __('sequence.deleted') }}</span>
@endif