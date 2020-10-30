@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/support_questions.index.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@include('support_question.tabs')

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

	@if ($supportQuestions->hasPages())
		{{ $supportQuestions->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@if (!$supportQuestions->count())
		<div class="alert alert-info">
			{{ __('No questions found') }}
		</div>
	@else
		<div>
			@foreach ($supportQuestions as $item)
				@include('support_question.item', ['item' => $item])
			@endforeach
		</div>
	@endif

	@if ($supportQuestions->hasPages())
		{{ $supportQuestions->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection