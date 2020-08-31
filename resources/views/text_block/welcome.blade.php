@extends('layouts.app')

@section('content')

	@include('text_block.item', ['name' => 'Приветствие'])

	@auth
		<a class="btn btn-primary"
		   href="{{ route('profile', auth()->user()) }}">{{ __('user.now_you_can_enter_to_your_profile') }}</a>
	@endauth


@endsection