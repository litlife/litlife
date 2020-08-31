@extends('layouts.email')

@section('content')

	{!! __('email.invitation.text', [
		'url' => route('users.registration', $invitation->token)
	]) !!}


@endsection




