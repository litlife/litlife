@extends('layouts.app')

@section('content')

	@if (count($errors->invitation) > 0)
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->invitation->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if(session()->has('ok'))
		<div class="alert alert-success">
			{!! __('invitation.sended', ['email' => old('email')]) !!}
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form role="form" action="{{ route('invitation.store') }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="form-group">
					{{ __('invitation.enter_email') }}
				</div>

				<div class="form-group{{ $errors->invitation->has('email') ? ' has-error' : '' }}">
					<input id="email" name="email" type="text" value="{{ old('email') }}"
						   placeholder="{{ __('invitation.enter_your_mailbox') }}"
						   class="form-control"/>
				</div>

				<div class="form-group{{ $errors->invitation->has('g-recaptcha-response') ? ' has-error' : '' }}">
					{!! NoCaptcha::display() !!}
					@push('scripts') {!! NoCaptcha::renderJs(''.config('locale').'') !!} @endpush
				</div>

				<button type="submit" class="btn btn-primary mb-3">
					{{ __('invitation.send') }}
				</button>

				<div class="form-group">
					{{ __('invitation.click_the_send_invitation_button') }}<br/>
					{{ __('invitation.you_will_receive_an_email_to_the_specified_mailbox_within_minutes') }}
					"{{ __('notification.invitation.action') }}".
					<br/><br/>
					{{ __('invitation.if_the_invitation_letter_did_not_arrive_within_5_minutes_then') }}
					<ul>
						<li>{{ __('invitation.carefully_check_the_address_you_entered_you_may_have_made_a_small_mistake') }}</li>
						<li>{{ __('invitation.check_the_spam_folder') }}</li>
						<li>{{ __('invitation.try_using_a_different_mailbox') }}</li>
						<li>{{ __('invitation.try_again_later') }}</li>
					</ul>
				</div>

			</form>

		</div>
	</div>

@endsection