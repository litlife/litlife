export default function BookChaptersListGoTo() {

	var self = this;

	this.init = function () {

		self.modalBody = self.modal.find('.modal-body');

		self.modalBody.html('<div class="text-center"><i class="h1 fas fa-spinner fa-spin"></i></div>');

		self.modal.on('show.bs.modal', self.showBsModal);
	};

	this.showBsModal = function (event) {

		if (self.modalBody.find('.list-group').length < 1) {
			$.ajax({
				method: "GET",
				url: self.modal.data('href')
			}).done(self.onLoadDone);
		}
	};

	this.onLoadDone = function (html) {

		self.modalBody.html(html);
	};
}










