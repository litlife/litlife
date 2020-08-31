@extends('layouts.email')

@section('content')

	{!! __('email.wall_reply.text', [
	'user_url' => route('profile', $blog->create_user),
	'user_name' => $blog->create_user->userName,
	'url' => route('users.blogs.go', ['user' => $blog->owner, 'blog' => $blog])
	]) !!}

@endsection



