@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/bookmarks.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('scripts.jquery-sortable')

	<div id="bookmarks">
		<div class="row">
			<div class="col-md-5 col-lg-4 order-md-2 order-sm-1 mb-3">
				<div class="row ">
					<div class="col-12">
						<div class="position-save-output"></div>

						@include('bookmark.folder.list')

					</div>
				</div>
				<div class="row">
					<div class="col-12">
						@can ('create', App\BookmarkFolder::class)
							<form role="form" method="POST" action="{{ route('bookmark_folders.store') }}">
								@csrf
								<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

									<input id="title" type="text" class="form-control" name="title"
										   placeholder="{{ __('bookmark_folder.title') }}"
										   value="{{ old('title') }}" required>

								</div>
								<div class="d-flex">
									<button type="submit" class="btn btn-primary text-nowrap text-truncate">
										{{ __('bookmark_folder.create') }}
									</button>
								</div>
							</form>
						@endcan
						<div class="model" style="height:0px; overflow:hidden"></div>
					</div>
				</div>
			</div>

			<div class="bookmarks col-md-7 col-lg-8 order-md-1 order-sm-2">
				@include('bookmark.index')
			</div>
		</div>
	</div>

@endsection