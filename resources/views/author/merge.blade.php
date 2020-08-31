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

	<div class="card">
		<div class="card-body">


			<form action="{{ route('authors.merge.store') }}" role="form" method="POST">

				@csrf

				@foreach ($authors as $author)
					<input name="authors[]" type="hidden" value="{{ $author->id }}">
				@endforeach

				<div class="row form-group">
					<div class="col-12 ">
						{{ __('author.merge_best_version') }}:
					</div>
				</div>

				<div class="row form-group{{ $errors->has('main_author') ? ' has-error' : '' }}">
					<div class="col-sm-12">
						<div class="table-responsive">
							<table class="table table-striped">
								@foreach ($authors as $author)
									<tr>
										<td style="width:1%">
											<input name="main_author" type="radio" value="{{ $author->id }}">
										</td>
										<td>
											<a target="_blank"
											   href="{{ route('authors.show', $author) }}">{{ $author->fullName }}</a>

											ID: {{ $author->id }}
										</td>
									</tr>
								@endforeach
							</table>
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('common.merge') }}
				</button>


			</form>
		</div>
	</div>

@endsection
