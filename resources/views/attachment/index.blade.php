@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/books.attachments.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include ('book.edit_tab')

	@can ('create_attachment', $book)

		@if (session('success'))
			<div class="alert alert-success alert-dismissable">
				{{ session('success') }}
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			</div>
		@endif

		<div class="card mb-2">
			<div class="card-body">

				@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<form role="form" method="POST" action="{{ route('books.attachments.store', compact('book')) }}"
					  enctype="multipart/form-data">

					@csrf

					<div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">
						<div class="col-12">
							<input id="file" type="file" name="file" si>
						</div>
					</div>


					<button type="submit" class="btn btn-secondary">
						{{ __('common.upload') }}
					</button>

					<small id="fileuploadHelpBlock" class="form-text text-muted">
						{{ __('common.max_size') }}
						: {{ ByteUnits\Metric::kilobytes(config('litlife.max_image_size'))->format() }}
					</small>

				</form>
			</div>
		</div>

	@endcan

	@if ($attachments->hasPages())
		{{ $attachments->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@if (isset($attachments) and $attachments->count())
		<div class="attachments card-columns">
			@foreach($attachments as $attachment)
				@include('attachment.item', ['item' => $attachment])
			@endforeach
		</div>
	@endif

	@if ($attachments->hasPages())
		{{ $attachments->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection