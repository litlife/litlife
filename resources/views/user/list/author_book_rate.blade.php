@component('user.list.default', ['user' => $vote->create_user, 'vote' => $vote, 'rand' => $rand ?? ''])

	<div class="mt-1 mb-1">
		{{ __('common.vote') }}:
		<x-book-vote :vote="$vote"/>
		&nbsp;<x-time :time="$vote->user_updated_at"/>
	</div>

	{{ trans_choice('book.books', 1) }}:
	<x-book-name :book="$vote->book" badge="0"/>
@endcomponent


