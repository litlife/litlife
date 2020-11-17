@extends('layouts.app')

@section('content')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card mb-2">
		<div class="card-body">
			{{ __('To avoid waiting for an answer, we recommend that you look for a solution in') }}
			<a href="{{ route('faq') }}" class="text-info">{{ __('Frequently asked question') }}</a>
			{{ __('or at') }} <a href="{{ route('forums.index') }}" class="text-info">{{ __('Forum') }}</a>. <br />
			{{ __('You can suggest an idea') }} <a href="{{ route('ideas.index') }}" class="text-info">{{ __('here') }}</a>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			@include('support_question.form')
		</div>
	</div>

@endsection