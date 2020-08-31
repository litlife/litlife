@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/authors.edit.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	<div class="card mb-3">
		<div class="card-body">

			@if ((!empty($authors)) and ($authors->count()))
				<div class="table-responsive mb-0">
					<table class="table">
						@foreach ($authors as $author)
							<tr>
								<td>
									<x-author-name :author="$author"/>
								</td>
								<td>
									<a class="btn btn-danger" href="{{ route('authors.ungroup', $author) }}">
										{{ __('common.delete') }}
									</a>
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			@else
				<div class="alert alert-info" role="alert">
					{{ __('author.nothing_found') }}
				</div>
			@endif

		</div>
	</div>

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


			<form role="form" method="POST" action="{{ route('authors.group', $author) }}">
				@csrf

				<div class="form-group{{ $errors->has('author') ? ' has-error' : '' }}">
					<label for="author" class="col-form-label">{{ __('author.id') }}</label>

					<input id="email" type="text" class="form-control" name="author"
						   value="{{ old('author') }}" required>

					@if ($errors->has('author'))
						<span class="help-block">
                                <strong>{{ $errors->first('author') }}</strong>
                            </span>
					@endif

				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('author.add_to_group') }}
				</button>


			</form>

		</div>
	</div>


@endsection
