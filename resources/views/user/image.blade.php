@extends('layouts.app')

@push('scripts')

@endpush

@section('content')


	@if($images->count())

		<div class="card-columns">
			@foreach($images as $image)

				<div class="item card" data-id="{{ $image->id }}">
					@if (!empty($image))
						<img class="card-img-top lazyload"
							 src="{{ $image->fullUrlMaxSize(350, 350) }}" alt="">
					@endif
					<div class="card-body">
						<p class="card-text">{{ $image->name }}</p>
						<p class="card-text">{{ __('image.size') }}: {{ $image->size }}</p>
						<p class="card-text">{{ __('image.created_at') }}
							:
							<x-time :time="$image->created_at"/>
						</p>
					</div>

				</div>

			@endforeach
		</div>
		@if ($images->hasPages())
			{{ $images->appends(request()->except(['page', 'ajax']))->links() }}
		@endif
	@else
		<div class="alert-info alert"></div>
	@endif




@endsection