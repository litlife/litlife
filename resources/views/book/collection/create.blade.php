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

			<form role="form" method="POST" action="{{ route('books.collections.store', $book) }}"
				  enctype="multipart/form-data">
				@csrf

				<div class="row form-group">
					<label for="book_id" class="col-md-3 col-lg-2 col-form-label">
						{{ __('Collection') }}
					</label>
					<div class="col-md-9 col-lg-10">

						<div class="selected_collection mb-2">
							@if (!empty($collection))
								@include('book.collection.selected', ['collection' => $collection])
							@endif
						</div>

						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#selectModal">
							{{ __('Select a collection') }}
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
							   value="{{ old('number') }}"/>
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
					<div class="modal fade" id="selectModal" tabindex="-1" role="dialog"
						 aria-labelledby="exampleModalCenterTitle"
						 aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"
										id="exampleModalCenterTitle">{{ __('Select a collection') }}</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">

									<form class="select_book" role="form" action="{{ route('books.collections.search', ['book' => $book]) }}"
										  method="get">

										@csrf

										<div class="form-group">
											<input name="search" type="text" class="form-control"
												   placeholder="{{ __('Start entering the name of the collection') }}">
										</div>
									</form>

									<div class="result">

									</div>
								</div>
							</div>
						</div>
					</div>
				@endpush

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('Add a book to a collection') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>

	@push('body_append')

		<script type="text/javascript">

			$(function () {

				let modal = $('#selectModal:first');

				let list = modal.find('.result:first');

				let body = modal.find('.modal-body');

				let form = body.find('form:first');

				modal.on('show.bs.modal', function (e) {
					if ($.trim(list.html()) === '') {
						form.formChange('inputChange');
					}
				});


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

					list.find('.collection').each(function () {
						let item = $(this);
						let button = item.find('.select');
						let id = item.data('collection-id');
						let book_id = item.data('book-id');

						button.click(function () {

							$.ajax({
								url: '/books/' + book_id + '/collections/' + id + '/selected'
							}).done(function (data) {
								modal.modal('hide');
								$('.selected_collection').html(data);
							});
						});
					});
				}
			});

		</script>

	@endpush

@endsection
