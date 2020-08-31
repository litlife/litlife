@php ($rand = rand(5, 10))

@if(count($sequences) > 0)
	<div class="table-responsive">
		<table class="table table-striped table-light">
			<thead>
			<tr>
				<td>{{ __('sequence.name') }}</td>
				<td>{{ __('sequence.book_count') }}</td>
			</tr>
			</thead>
			<tbody class="sequences">

			@foreach ($sequences as $sequence)

				@include('sequence.list.'.$item_render)

				@if (in_array($loop->index, [$rand]))
					@can('see_ads', \App\User::class)
						<tr>
							<td colspan="100%">
								@include('ads.adaptive_horizontal')
							</td>
						</tr>
					@endcan
				@endif

			@endforeach

			</tbody>
		</table>
	</div>

	@if ($sequences->hasPages())
		{{ $sequences->appends(request()->except(['page', 'ajax']))->links() }}
	@endif
@else
	<p class="alert alert-info">{{ __('sequence.nothing_found') }}</p>
@endif