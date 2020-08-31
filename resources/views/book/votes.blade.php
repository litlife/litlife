@extends('layouts.app')

@push('scripts')


@endpush

@section('content')


	@if(count($votes) > 0)
		<div class="row equal-height">
			@foreach ($votes as $vote)

				<div class="col-md-2">
					<div class="thumbnail">

						<div style="width:100px; height:100px; text-align:center; margin:auto;">
							<x-user-avatar :user="$vote->user" width="100" height="100"/>
						</div>

						<div class="caption text-center">
							<x-user-name :user="$vote->user"/>
							<br/>

							{{ __('common.vote') }}: {{ $vote->vote }} <br/>
							<x-time :time="$vote->user_updated_at"/>
							<br/>
						</div>

					</div>
				</div>

			@endforeach
		</div>

		@if ($votes->hasPages())
			<div class="row">
				<div class="col-12">
					{{ $votes->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

	@else
		<p class="alert alert-info">{{ __('book_votes.nothing_found') }}</p>
	@endif


@endsection