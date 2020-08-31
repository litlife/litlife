@if(count($users) > 0)

	@if ($users->hasPages())
		{{ $users->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@php ($rand = rand(6, 12))


	@foreach ($users as $user)
		@include('user.list.'.$item_render)
	@endforeach


	@if ($users->hasPages())
		{{ $users->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@else
	<p class="alert alert-info">{{ __('user.nothing_found') }}</p>
@endif