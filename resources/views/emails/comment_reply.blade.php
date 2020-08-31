@extends('layouts.email')

@section('content')

	{!! __('email.comment_reply.text', [
	'user_url' => route('profile', $comment->create_user),
	'user_name' => $comment->create_user->userName,
	'url' => route('comments.go', compact('comment'))
	]) !!}

@endsection



