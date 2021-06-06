export default function RemoveAdsModal() {

	var self = this;

	this.init = function () {

		self.button.unbind('click').on('click', function (event) {
			event.preventDefault();

			self.button.addClass('loading-cap');

			self.modal = bootbox.dialog({
				message: '<div class="text-center"><h1><i class="fas fa-spinner fa-spin"></i></h1></div>',
				backdrop: true
			});

			self.modal.init(function () {
				$.ajax({
					method: "GET",
					url: '/remove_ads'
				})
					.done(self.onLoadDone)
					.fail(self.onLoadFail);
			});
		});
	};

	this.onLoadDone = function (html) {

		self.button.removeClass('loading-cap');

		self.modal.find('.bootbox-body').html(html);
	};

	this.onLoadFail = function (html) {
		self.modal.modal('hide');

		self.button.removeClass('loading-cap');
	};
}








