import wall_post from "../blog/item";
import post from "../post/item";
import comment from "../comment/item";

export default function item() {

	let self = this;

	this.init = function (i) {

		self.i = i;
		self.id = self.i.data('id');
		self.complainable_type = self.i.data('type');
		self.complainable = self.i.find('.complainable').first();
		self.complain_buttons = self.i.find('.complain_buttons').first();

		if (self.complainable_type === 'blog') {
			wall_post(self.complainable.find('.item').first());
		} else if (self.complainable_type === 'post') {
			post(self.complainable.find('.item').first());
		} else if (self.complainable_type === 'comment') {
			comment(self.complainable.find('.item').first());
		}

		self.btn_approve = self.complain_buttons.find('.btn-approve').first();
		self.btn_decline = self.complain_buttons.find('.btn-decline').first();
		self.start_review = self.complain_buttons.find('.btn-start-review').first();
		self.stop_review = self.complain_buttons.find('.btn-stop-review').first();

		self.status = self.i.find('.status').first();

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
			url: self.btn_approve.attr('href')
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
			url: self.start_review.attr('href')
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
			url: self.stop_review.attr('href')
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

