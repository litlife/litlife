@extends('layouts.app')

@push('scripts')

@endpush

@push('css')

@endpush

@section('content')

	<div class="row">
		<div class="col-12 d-flex flex-wrap">
			@foreach ($smiles as $smile)
				<div style="width:50px; height:50px;" class="emoticon d-inline-block text-center"
					 data-simple-form="{{ $smile->simple_form }}">
					<img data-src="{{ $smile->fullUrl }}" class="lazyload" style="max-width:40px; "/>
				</div>
			@endforeach
		</div>
	</div>

@endsection
