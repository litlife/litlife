@extends('layouts.email')

@section('content')

	{!! __('email.new_personal_message.text', [
		'user_url' => route('profile', $msg->sender),
		'user_name' => $msg->sender->userName,
		'url' => route('users.messages.index', ['user' => $msg->sender])
	]) !!}

@endsection




