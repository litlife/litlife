@extends('layouts.email')

@section('content')

	{!! __('email.password_reset.text', [
		'url' => route('password.reset_form', ['confirm' => $reset->token])
	]) !!}

@endsection




