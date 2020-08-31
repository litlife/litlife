@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/book_files.on_moderation.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@if (session('success'))

		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>

	@endif

	@if($files->count() < 1)
		<div class="alert alert-info">
			{{ __('book_file.nothing_found') }}
		</div>
	@else

		@foreach ($files as $file)
			<div class="file card mb-3" data-id="{{ $file->id }}" data-book-id="{{ $file->book_id }}">
				<div class="card-body">
					<div class="row mt-0">
						<div class="col-12 title">
							@if (!empty($file->book))
								<h6>
									<x-book-name :book="$file->book"/>
								</h6>
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-12 text-muted">

							{{ __('book_file.size') }}: {{ $file->file_size }} <br/>
							{{ __('book_file.download_count') }}: {{ $file->download_count }} <br/>
							{{ __('book_file.created_at') }}:
							<x-time :time="$file->created_at"/>
							<br/>

							@if (!empty($file->create_user))
								{{ trans_choice('user.created', $file->create_user->gender) }}:
								<x-user-name :user="$file->create_user"/>
								<br/>
							@endif

						</div>
					</div>
					<div class="row">
						<div class="col-12 btn-margin-bottom-1">
							@if ($file->exists())
								<a class="btn btn-light"
								   href="{{ route('books.files.show', ['book' => $file->book, 'fileName' => $file->name]) }}">
									{{ __('common.download') }} {{ $file->format }}
								</a>
							@else
								<span class="text-danger">{{ __('book_file.not_found', ['title' => $file->format]) }}</span>
							@endif

							@can ('approve', $file)
								<a class="btn btn-success"
								   href="{{ route('book_files.approve', ['file' => $file->id]) }}">
									{{ __('common.approve') }}
								</a>
							@endcan

							<button class="delete btn btn-danger text-lowercase"
									@cannot ('delete', $file) style="display:none;"@endcannot>
								{{ __('common.delete') }}
							</button>

							<button class="restore btn btn-light text-lowercase"
									@cannot ('restore', $file) style="display:none;"@endcannot>
								{{ __('common.restore') }}
							</button>

						</div>
					</div>

				</div>
			</div>

		@endforeach

		{{ $files->appends(request()->except(['page', 'ajax']))->links() }}


	@endif

@endsection