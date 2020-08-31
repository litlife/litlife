@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="row">
		<div class="col-12">

			{{ $note->text }}
		</div>
	</div>

	{{--
		{!! JsValidator::formRequest('App\Http\Requests\StoreAuthor', '.content  form') !!}
	--}}

@endsection
