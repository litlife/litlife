@cannot('pass_age_restriction', $book)

	@push('main_classes') blur @endpush

	@push('scripts_before_app')

		<script type="text/javascript">

			$(function () {
				let main = $('main').first();

				main.addClass('blur');

				$('#askAgeModal').modal({
					backdrop: 'static',
					focus: true,
					show: true
				}).on('hidden.bs.modal', function (e) {
					main.removeClass('blur');

					$.ajax({
						method: "GET",
						url: '{{ route('user_pass_age_restriction') }}',
						data: {age: '{{ $book->age }}'}
					}).done(function (msg) {


					});

				}).find('.no').on('click', function () {
					window.location = '{{ route('home') }}';
				});
			});

		</script>

		<div class="modal" id="askAgeModal" tabindex="-1" role="dialog" aria-labelledby="askAgeModalTitle"
			 aria-hidden="true">
			<div class="modal-dialog modal-sm modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<div class="h5">{{ __('common.are_you_older_than', ['age' => $book->age]) }}</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-success" data-dismiss="modal">{{ __('common.yes') }}</button>
						<button type="button" class="btn btn-danger no">{{ __('common.no') }}</button>
					</div>
				</div>
			</div>
		</div>

	@endpush

@endcan