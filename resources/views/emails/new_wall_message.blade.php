@extends('layouts.email')

@section('content')

	{!! __('email.new_wall_message.text', [
		'user_url' => route('profile', $blog->create_user),
		'user_name' => $blog->create_user->userName,
		'url' => route('users.blogs.go', ['user' => $blog->owner, 'blog' => $blog])
	]) !!}

@endsection



