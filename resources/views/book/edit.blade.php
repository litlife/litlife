@extends('layouts.app')

@push('scripts')

	<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.jquery.min.js"></script>

	<script src="//cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.2.1/bloodhound.min.js"></script>

	<script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>

	<script src="{{ mix('js/books.edit.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('scripts.jquery-sortable')
	@include ('book.edit_tab')

	@if (session('try_publish'))
		<div class="alert alert-info">
			{{ __('book.fix_errors_before_publish') }}
		</div>
	@endif

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<div class="card">
		<div class="card-body">
			@include('book.edit_form')
		</div>
	</div>

@endsection
