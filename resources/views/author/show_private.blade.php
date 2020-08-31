@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/authors.show.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')
	<div class="card">
		<div class="card-body">

			<div class="author">

				<div class="row">

					<div class="col-md-3 text-center">

						<div class="row">
							<div class="col-12">

								<x-author-photo :author="$author" width="200" height="400"
												class="img-fluid rounded pointer lazyload"
												href="0" style="max-width: 100%;"/>

							</div>
						</div>
						<div class="row">
							<div class="col-12">

							</div>
						</div>
					</div>

					<div class="col-md-9">
						<div class="row">
							<div class="col-12">
								<h4>{{ __('author.access_denied') }}</h4>
							</div>
						</div>

						<div class="row">
							<div class="col-12">

								@include('like.item', ['item' => $author, 'like' => pos($author->likes) ?: null, 'likeable_type' => 'author'])

								@include('user_library_button', [
								'item' => $author, 'user_library' => pos($author->library_users) ?: null,
								'type' => 'author', 'id' => $author->id,
								'count' => $author->added_to_favorites_count])

								<select class="read-status inline custom-select mb-1" style="width:200px;">
									@foreach (\App\Enums\ReadStatus::getValues() as $status)
										<option value="{{ $status }}"
												@if ((isset($user_read_status->status)) && ($user_read_status->status == $status)) selected @endif>
											{{ trans_choice('author.read_status_array.'.$status, 1) }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
@endsection
