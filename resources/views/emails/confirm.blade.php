@extends('layouts.email')

@section('content')

	{!! __('email.confirm.text', [
	'url' => route('email.confirm', ['email' => $token->email, 'token' => $token->token])
	]) !!}

@endsection