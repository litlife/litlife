export default function item() {

	let self = this;

	this.init = function (i) {

		self.i = i;
		self.id = self.i.data("id");
		self.counter = self.i.find('.counter');
		self.$btn_like = self.i.find('.like');
		self.$you_support_this_idea = self.i.find('.you_support_this_idea');

		self.$btn_like.unbind('click').bind('click', self.onClick);
	};

	this.onClick = function (event) {

		event.preventDefault();

		self.$btn_like.addClass('loading-cap');

		let url = self.$btn_like.attr('href');

		$.ajax({
			method: "GET",
			url: url
		}).done(self.onDone)
			.fail(self.onFail);
	};

	this.onDone = function (result) {
		let count = result.item.like_count;

		self.counter.text(count);
		self.$you_support_this_idea.show();
		self.$btn_like.hide();
		self.$btn_like.removeClass('loading-cap');
	};

	this.onFail = function () {
		self.$btn_like.removeClass('loading-cap');
	};
}