@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST" action="{{ route('collections.destroy', ['collection' => $collection]) }}"
				  enctype="multipart/form-data">

				@csrf
				@method('delete')

				<div class="form-group">
					{{ __('Do you really want to delete a collection without being able to restore it?') }}
					{{ __('Comments will also be deleted') }}
				</div>

				<button type="submit" class="btn btn-primary">{{ __('Delete') }}</button>

			</form>
		</div>
	</div>

@endsection
