@extends('layouts.app')

@section('content')


	<div class="alert alert-success" role="alert">
		{{ __('user_email.notice_disabled', ['email' => $email->email]) }}
	</div>


@endsection