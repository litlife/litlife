@extends('layouts.app')

@section('content')

	@if (!empty($parent))
		<div class="row">
			<div class="col-12">
				@include('forum.post.item.default', ['item' => $parent,
				'parent' => $parent->parent ?? null,
				'no_limit' => true,
				'no_button_panel' => true])
			</div>
		</div>
	@endif

	<div class="row">
		<div class="col-12">

			@include('forum.post.create_form')

		</div>
	</div>

@endsection
