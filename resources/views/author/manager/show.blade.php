@extends('layouts.app')

@section('content')

	@if ($manager->isRejected())
		<div class="row">
			<div class="col-12">
				<div class="alert alert-info">
					{{ __('manager.declined') }}
				</div>
			</div>
		</div>
	@elseif ($manager->isSentForReview())
		<div class="row">
			<div class="col-12">
				<div class="alert alert-info">
					{{ __('manager.request_on_review') }}
				</div>
			</div>
		</div>
	@elseif ($manager->isAccepted())
		<div class="row">
			<div class="col-12">
				<div class="alert alert-success">
					{{ __('manager.succeed') }}
				</div>
			</div>
		</div>
	@endif

@endsection