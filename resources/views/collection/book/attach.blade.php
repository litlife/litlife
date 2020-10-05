@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@include('collection.show_navbar')

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

			<form role="form" method="POST" action="{{ route('collections.books.attach', $collection) }}"
				  enctype="multipart/form-data">
				@csrf

				<div class="row form-group">
					<label for="book_id" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.book') }}
					</label>
					<div class="col-md-9 col-lg-10">

						<div class="selected_book mb-2">
							@if (!empty($book))
								@include('collection.book.selected_item', ['book' => $book])
							@endif
						</div>
						<!-- Button trigger modal -->
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#selectBookModal">
							{{ __('collection.select_book') }}
						</button>
					</div>
				</div>

				<div class="row form-group">
					<label for="number" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.number') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="number" name="number" type="text"
							   class="form-control{{ $errors->has('number') ? ' is-invalid' : '' }}"
							   value="{{ old('number') ?? $max }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="comment" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.comment') }}
					</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="comment" name="comment" rows="5"
								  class="form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}">{{ old('comment') }}</textarea>
					</div>
				</div>

				@push('body_append')
				<!-- Modal -->
					<div class="modal fade" id="selectBookModal" tabindex="-1" role="dialog"
						 aria-labelledby="exampleModalCenterTitle"
						 aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"
										id="exampleModalCenterTitle">{{ __('collection.book_selection') }}</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">

									<form class="select_book" role="form" action="{{ route('collections.books.list', ['collection' => $collection]) }}"
										  method="get">

										@csrf

										<div class="form-group">
											<input name="query" type="text" class="form-control"
												   placeholder="{{ __('collection.enter_book_title_author_or_book_id') }}">
										</div>
									</form>

									<div class="result_books">

									</div>
								</div>
							</div>
						</div>
					</div>
				@endpush

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('collection.attach_book') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>

	@push('body_append')

		<script type="text/javascript">

			$(function () {

				let modal = $('#selectBookModal:first');

				let list = modal.find('.result_books:first');

				let body = modal.find('.modal-body');

				let form = body.find('form:first');

				form.formChange({
					timeout: 500,
					onShow: function () {

						$(this).ajaxSubmit({
							beforeSubmit: function showRequest(formData, jqForm, options) {

								list.addClass("loading-cap");
								return true;
							},
							success: function (responseText, statusText, xhr, $form) {

								list.removeClass("loading-cap");

								list.html(responseText);

								define_pagination();

								on_list_ready();
							},
							error: function (jqXHR, error, type, $form) {

							}
						});
					}
				});

				function define_pagination() {
					list.off('click', '.pagination a').on('click', '.pagination a', function (e) {
						e.preventDefault();

						list.addClass("loading-cap");

						let url = $(this).attr('href');

						load_url(url);
					});

					list.off('click', '.view a').on('click', '.view a', function (e) {
						e.preventDefault();

						let url = $(this).attr('href');

						load_url(url);
					});
				}

				function load_url(url) {

					list.addClass("loading-cap");

					$.ajax({
						url: url, data: {'ajax': true}
					}).done(function (data) {
						list.removeClass("loading-cap");
						list.html(data);

						on_list_ready();

						modal.animate({
							scrollTop: list.offset().top - 80
						}, 100);

					}).fail(function () {
						if (jqXHR.status == 401) location.reload();
					});
				}

				function on_list_ready() {

					list.find('.book').each(function () {
						let item = $(this);
						let button = item.find('.select');
						let id = item.data('book-id');

						button.click(function () {

							$.ajax({
								url: '/collections/books/selected/' + id + '/item'
							}).done(function (data) {
								modal.modal('hide');
								$('.selected_book').html(data);
							});
						});
					});
				}
			});

		</script>

		@if (empty($book))
			<script type="text/javascript">
				$(window).on('load', function () {
					$('#selectBookModal').modal('show');
				});
			</script>
		@endif

	@endpush

@endsection
