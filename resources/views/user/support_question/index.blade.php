@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

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

	@can ('create', \App\SupportQuestion::class)
		<div class="mb-3">
			<a href="{{ route('support_questions.create', ['user' => $user]) }}" class="btn btn-primary">
				{{ __('New question') }}
			</a>
		</div>
	@endcan

	@if ($supportQuestions->hasPages())
		{{ $supportQuestions->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@if (!$supportQuestions->count())
		<div class="alert alert-info">
			{{ __('No support questions were found') }}
		</div>
	@else
		<div class="list-group">
			@foreach ($supportQuestions as $item)
				@include('user.support_question.item', ['item' => $item])
			@endforeach
		</div>
	@endif

	@if ($supportQuestions->hasPages())
		{{ $supportQuestions->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection