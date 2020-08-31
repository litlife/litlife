@extends('layouts.without_navbar_and_footer')

@push('scripts')
	<script src="{{ mix('js/books.attachments.index.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@can ('update', $book)
		<div class="row my-3">
			<div class="col-12">

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

				<form role="form" method="POST" action="{{ route('books.attachments.store', compact('book')) }}"
					  enctype="multipart/form-data">

					@csrf

					<div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">
						<div class="col-12">
							<input id="file" type="file" name="file">
						</div>
					</div>

					<button type="submit" class="btn btn-secondary">
						{{ __('common.upload') }}
					</button>

				</form>
			</div>
		</div>
	@endcan

	@if ($attachments->hasPages())
		{{ $attachments->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	<div class="attachments card-columns">
		@if (empty($attachments))
			<div class="alert alert-info">{{ __('book_file.nothing_found') }}</div>
		@else
			@foreach($attachments as $attachment)
				@include('attachment.item', ['item' => $attachment, 'paste_button' => true])
			@endforeach
		@endif
	</div>

	@if ($attachments->hasPages())
		{{ $attachments->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection
