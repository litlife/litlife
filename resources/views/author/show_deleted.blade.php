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
												href="0"
												style="max-width: 100%;"/>

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
								<h4>{{ __('author.deleted') }}</h4>
							</div>
						</div>

						<div class="row">
							<div class="col-12">

								@include('like.item', ['item' => $author, 'like' => pos($author->likes) ?: null, 'likeable_type' => 'author'])

								@include('user_library_button', ['item' => $author, 'user_library' => pos($author->library_users) ?: null, 'type' => 'author', 'id' => $author->id])


								<select class="read-status form-control inline" style="width:200px;">
									@foreach (__("author.read_status") as $code => $text)
										<option value="{{ $code }}"
												@if ((isset($user_read_status->code)) && ($user_read_status->code == $code)) selected @endif>
											{{ $text }}
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
