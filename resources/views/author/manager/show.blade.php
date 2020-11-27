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

	<div class="card">
		<div class="card-body">

			@if ($manager->isCreateUserIsSameAsUser())
				<div class="card-text">
					{{ __('The request was sent by') }}
					<x-user-name :user="$manager->create_user"/>
				</div>
			@endif

			@if ($manager->comment)
				<div class="card-text">
					{{ __('manager.comment') }}: {{ $manager->comment }}
				</div>
			@endif

			@isset($manager->status_changed_user)
				<div class="card-text">
					{{ __('The request was processed by') }}
					<x-user-name :user="$manager->status_changed_user"/>
				</div>
			@endisset
		</div>
	</div>

@endsection