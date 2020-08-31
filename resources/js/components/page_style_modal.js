import PageStyleEdit from "./PageStyleEdit";

export default function PageStyleModal() {

	var self = this;

	this.init = function () {

		self.button.unbind('click').on('click', function (event) {
			event.preventDefault();

			self.button.addClass('loading-cap');

			self.modal = bootbox.dialog({
				message: '<div class="text-center"><h1><i class="fas fa-spinner fa-spin"></i></h1></div>',
				backdrop: false,
				size: 'small'
			});

			self.modal.init(function () {
				$.ajax({
					method: "GET",
					url: '/read_style'
				})
					.done(self.onLoadDone)
					.fail(self.onLoadFail);
			});
		});
	};

	this.onLoadDone = function (html) {

		self.button.removeClass('loading-cap');

		self.modal.find('.bootbox-body').html(html);

		let instace = new PageStyleEdit();
		instace.form = $('form.read_style');
		instace.resetButton = instace.form.find('.reset').first();
		instace.init();
	};

	this.onLoadFail = function (html) {
		self.modal.modal('hide');

		self.button.removeClass('loading-cap');
	};
}








