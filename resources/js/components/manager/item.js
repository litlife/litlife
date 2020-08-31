export default function item() {

	let self = this;

	this.init = function (i) {

		self.i = i;
		self.id = self.i.data('manager-id');
		self.btn_approve = self.i.find('.btn-approve');
		self.btn_decline = self.i.find('.btn-decline');
		self.start_review = self.i.find('.btn-start-review');
		self.stop_review = self.i.find('.btn-stop-review');

		self.btn_delete = self.i.find('.btn-delete');
		self.status = self.i.find('.status');

		self.btn_approve.unbind('click').on('click', self.onApprove);
		self.btn_decline.unbind('click').on('click', self.onDecline);
		self.start_review.unbind('click').on('click', self.onStartReview);
		self.stop_review.unbind('click').on('click', self.onStopReview);
	};

	this.onApprove = function (event) {

		var btn = $(this);

		event.preventDefault();

		btn.addClass('loading-cap');

		$.ajax({
			method: "GET",
			url: '/managers/' + self.id + '/approve/'
		}).done(function (msg) {
			btn.removeClass('loading-cap');
			self.status.html(msg);

			self.btn_approve.hide();
			self.btn_decline.hide();
			self.start_review.hide();
			self.stop_review.hide();

		}).fail(function () {
			btn.removeClass('loading-cap');
		});
	};

	this.onDecline = function (event) {

		var btn = $(this);

		event.preventDefault();

		btn.addClass('loading-cap');

		$.ajax({
			method: "GET",
			url: '/managers/' + self.id + '/decline/'
		}).done(function (msg) {
			btn.removeClass('loading-cap');
			self.status.html(msg);

			self.btn_approve.hide();
			self.btn_decline.hide();
			self.start_review.hide();
			self.stop_review.hide();

		}).fail(function () {
			btn.removeClass('loading-cap');
		});
	};

	this.onStartReview = function (event) {

		var btn = $(this);

		event.preventDefault();

		btn.addClass('loading-cap');

		$.ajax({
			method: "GET",
			url: '/managers/' + self.id + '/start_review/'
		}).done(function (msg) {
			btn.removeClass('loading-cap');
			self.status.html(msg);

			self.btn_approve.show();
			self.btn_decline.show();
			self.start_review.hide();
			self.stop_review.show();

		}).fail(function () {
			btn.removeClass('loading-cap');
		});
	};

	this.onStopReview = function (event) {

		var btn = $(this);

		event.preventDefault();

		btn.addClass('loading-cap');

		$.ajax({
			method: "GET",
			url: '/managers/' + self.id + '/stop_review/'
		}).done(function (msg) {
			btn.removeClass('loading-cap');

			self.status.html(msg);

			self.btn_approve.hide();
			self.btn_decline.hide();
			self.start_review.show();
			self.stop_review.hide();

		}).fail(function () {
			btn.removeClass('loading-cap');
		});
	};
}

