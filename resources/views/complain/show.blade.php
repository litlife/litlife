@extends('layouts.app')

@section('content')

	@if ($complain->isSentForReview())
		<div class="alert alert-info">
			{{ __('complain.complaint_is_pending') }}
		</div>
	@endif

	@if ($complain->isReviewStarts())
		<div class="alert alert-info">
			{{ __('Сomplaint is currently under review') }}
		</div>
	@endif

	@if ($complain->isAccepted())
		<div class="alert alert-success">
			{{ __('Сomplaint has been reviewed') }}
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
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

	<div class="card">
		<div class="card-body">
			{{  $complain->text }}
		</div>
	</div>

@endsection