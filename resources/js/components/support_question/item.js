export default function item() {

	let self = this;

	this.init = function (i) {

		self.i = i;
		self.id = self.i.data('id');
		self.buttons = self.i.find('.buttons').first();

		self.btn_start_review = self.buttons.find('.btn-start-review').first();
		self.btn_approve = self.buttons.find('.btn-approve').first();
		self.btn_stop_review = self.buttons.find('.btn-stop-review').first();
		self.btn_continue_reviewing = self.buttons.find('.btn-continue-reviewing').first();

		self.status = self.i.find('.status').first();

		self.btn_approve.unbind('click').on('click', self.onApprove);
		//self.start_review.unbind('click').on('click', self.onStartReview);
		self.btn_stop_review.unbind('click').on('click', self.onStopReview);
	};

	this.onApprove = function (event) {

		var btn = $(this);

		event.preventDefault();

		btn.addClass('loading-cap');

		$.ajax({
			method: "GET",
			url: self.btn_approve.attr('href')
		}).done(function (msg) {
			btn.removeClass('loading-cap');
			self.status.html(msg);

			self.btn_approve.hide();
			self.btn_stop_review.hide();
			self.btn_start_review.hide();
			self.btn_continue_reviewing.hide();

		}).fail(function () {
			btn.removeClass('loading-cap');
		});
	};
	/*
		this.onStartReview = function (event) {

			var btn = $(this);

			event.preventDefault();

			btn.addClass('loading-cap');

			$.ajax({
				method: "GET",
				url: self.start_review.attr('href')
			}).done(function (msg) {
				btn.removeClass('loading-cap');
				self.status.html(msg);

				self.btn_approve.show();
				self.start_review.hide();
				self.btn_stop_review.show();

			}).fail(function () {
				btn.removeClass('loading-cap');
			});
		};
		*/

	this.onStopReview = function (event) {

		var btn = $(this);

		event.preventDefault();

		btn.addClass('loading-cap');

		$.ajax({
			method: "GET",
			url: self.btn_stop_review.attr('href')
		}).done(function (msg) {
			btn.removeClass('loading-cap');

			self.status.html(msg);

			self.btn_approve.hide();
			self.btn_stop_review.hide();
			self.btn_start_review.show();
			self.btn_continue_reviewing.hide();

		}).fail(function () {
			btn.removeClass('loading-cap');
		});
	};
}

