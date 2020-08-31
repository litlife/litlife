@extends('layouts.app')

@section('content')

	@if (!empty($parent))
		@include('blog.list.default', ['item' => $parent,
		'parent' => $parent->parent ?? null,
		'no_limit' => true,
		'no_button_panel' => true])
	@endif

	@include('blog.create_form')

@endsection