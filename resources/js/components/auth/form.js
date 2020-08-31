export default function form() {

	let self = this;

	this.init = function () {

		self.formLoaded = false;

		self.dialog = bootbox.dialog({
			message: '<div class="spinner" style="text-align: center; font-size:48px;"><i class="fas fa-spinner fa-spin"></i></div>',
			closeButton: true,
			backdrop: true,
			show: false,
			size: "small"
		}).on('shown.bs.modal', self.onShown);

		$('.auth_required').unbind('click').on('click', function () {
			self.showForm();
		});
	};

	this.showForm = function () {
		console.log('showForm');
		self.dialog.modal('show');
	};

	this.onShown = function () {
		console.log('onShown');

		self.dialogBody = self.dialog.find('.modal-body');

		if (!self.formLoaded)
			self.loadForm();
	};

	this.loadForm = function () {
		console.log('loadForm');

		$.ajax({
			method: "GET",
			url: '/login'
		}).done(function (html) {
			self.formLoaded = true;
			self.dialogBody.html(html);

		}).fail(function () {
			self.dialog.modal('hide');
		});

	};
}