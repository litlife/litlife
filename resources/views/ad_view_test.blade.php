@extends('layouts.app')

@push('body_append')

	<script type="text/javascript" src="https://code.giraff.io/data/widget-litlifeclub.js"></script>
@endpush

@section('content')

	<div class="card">
		<div class="card-body">
			<x-ad-block name="test"/>
		</div>
	</div>

@endsection