@extends('layouts.app')

@push('scripts')

@endpush

@push('css')
	<link href="{{ mix('css/sections-list.css', config('litlife.assets_path')) }}" rel="stylesheet"/>
@endpush

@section('content')

	@include ('book.edit_tab')

	@if(!empty($sections) and count($sections) > 0)
		<ol class="list-group list-group-flush mb-3 pl-0">
			@foreach($sections as $number => $section)
				@include('book.page.item', ['item' => $section])
			@endforeach
		</ol>
	@else
		<div class="alert alert-info p-3">
			{{ __('section.nothing_found') }}
		</div>
	@endif

	@include('book.age_access_modal')

@endsection
