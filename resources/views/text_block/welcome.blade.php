@extends('layouts.app')

@section('content')

	@include('text_block.item', ['name' => 'Приветствие'])

	@auth
		<a class="btn btn-primary"
		   href="{{ route('profile', auth()->user()) }}">{{ __('Go to my page') }}</a>
	@endauth

@endsection