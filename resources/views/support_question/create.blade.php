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
			Чтобы не ждать ответа, советуем поискать решение
			в <a href="{{ route('faq') }}" class="text-info">Часто задаваемых вопросах</a>
			или на <a href="{{ route('forums.index') }}" class="text-info">Форуме</a>.
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			@include('support_question.form')
		</div>
	</div>

@endsection