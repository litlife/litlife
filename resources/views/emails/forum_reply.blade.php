@extends('layouts.email')

@section('content')

	{!! __('email.forum_reply.text', [
		'user_url' => route('profile', $post->create_user),
		'user_name' => $post->create_user->userName,
		'url' => route('posts.go_to', compact('post'))
	]) !!}

@endsection



